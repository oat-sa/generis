<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\generis\Helper;

use common_Exception;
use common_exception_Error;
use common_report_Report as Report;
use core_kernel_classes_Resource;
use oat\generis\model\fileReference\FileReferenceSerializer;
use oat\generis\model\fileReference\FileSerializerException;
use oat\generis\model\fileReference\ResourceFileSerializer;
use oat\generis\model\fileReference\UrlFileSerializer;
use oat\generis\model\GenerisRdf;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\File;
use oat\oatbox\service\exception\InvalidServiceManagerException;
use oat\oatbox\service\ServiceManager;
use oat\search\base\exception\SearchGateWayExeption;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Helper class for the File serializer migration script
 * @see \oat\generis\scripts\tools\FileSerializerMigration
 */
class FileSerializerMigrationHelper
{

    use OntologyAwareTrait;

    /**
     * @var UrlFileSerializer
     */
    private $urlFileSerializer;

    /**
     * @var ResourceFileSerializer
     */
    private $resourceFileSerializer;

    /**
     * @var ComplexSearchService
     */
    private $search;

    /**
     * @var int
     */
    private $oldResourceCount = 0;

    /**
     * @var int
     */
    private $migratedCount = 0;

    /**
     * @var bool
     */
    private $isDryRun;

    /**
     * @var int[]
     */
    public $migrationInformation = [
        'old_resource_count' => 0,
        'old_resource_migration_count' => 0,
        'migrated_count' => 0
    ];

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * FileSerializerMigrationHelper constructor.
     * @param ServiceManager $serviceManager
     * @param ServiceLocatorInterface $serviceLocator
     * @param bool $dryRun
     */
    public function __construct($serviceManager, $serviceLocator, $dryRun = true)
    {
        $this->serviceManager = $serviceManager;
        $this->serviceLocator = $serviceLocator;
        $this->isDryRun = $dryRun;
        $this->urlFileSerializer = new UrlFileSerializer();
        $this->resourceFileSerializer = new ResourceFileSerializer();
    }


    /**
     * Get the resources that should be migrated.
     *
     * @param int $limit
     * @param int $offset
     * @return mixed[][]
     */
    public function getOldResourcesData($limit, $offset = 0)
    {
        $oldResources = [];
        $params = [
            'limit' => $limit,
            'offset' => $offset
        ];

        $fileResources = $this->getClass(GenerisRdf::CLASS_GENERIS_FILE)->getInstances(true, $params);

        foreach ($fileResources as $fileResource) {
            $oldResources[$fileResource->getUri()]['properties'] = $this->getPropertiesForResource($fileResource);
            $oldResources[$fileResource->getUri()]['resource'] = $fileResource;
        }

        $this->migrationInformation['old_resource_count'] = count($oldResources);

        return $oldResources;
    }

    /**
     * Migrate a single resource.
     *
     * @param core_kernel_classes_Resource $oldResource
     * @param string $predicateUri
     * @return void
     * @throws FileSerializerException
     * @throws InvalidServiceManagerException
     * @throws SearchGateWayExeption
     */
    private function migrateResource($oldResource, $predicateUri)
    {
        $property = $this->getProperty($predicateUri);
        $resource = $this->getParentResource($oldResource, $predicateUri);
        if ($resource === null) {
            return;
        }

        /** @var Directory|File $unserializedFileResource */
        $unserializedFileResource = $this->resourceFileSerializer->unserialize($oldResource);
        $migratedValue = $this->urlFileSerializer->serialize($unserializedFileResource);


        if (!$this->isDryRun) {
            $resource->editPropertyValues($property, $migratedValue);
            $oldResource->delete();
        }

        ++$this->migrationInformation['migrated_count'];
    }

    /**
     * Update the FileReferenceSerializer service to use the UrlFileSerializer.
     *
     * @return bool
     * @throws common_Exception
     */
    public function updateFileSerializer()
    {
        if ($this->fileSerializerNeedsUpdate()) {
            if (!$this->isDryRun) {
                $this->serviceManager->register(FileReferenceSerializer::SERVICE_ID, new UrlFileSerializer());
            }
            return true;
        }

        return false;
    }

    /**
     * Get the properties that are using this resource
     *
     * @param core_kernel_classes_Resource $fileResource
     * @return string[][]
     */
    private function getPropertiesForResource($fileResource)
    {
        $sql = "SELECT predicate FROM statements WHERE object = '" . $fileResource->getUri() . "'";
        $persistence = $fileResource->getModel()->getPersistence();
        return $persistence->query($sql)->fetchAll();
    }

    /**
     * @param $oldResource
     * @param $predicateUri
     * @return core_kernel_classes_Resource|null
     * @throws InvalidServiceManagerException
     * @throws SearchGateWayExeption
     */
    private function getParentResource($oldResource, $predicateUri)
    {
        $parentResource = null;
        $search = $this->getSearch();
        $queryBuilder = $search->query();
        $query = $queryBuilder->newQuery();
        $criteria = $query->add($predicateUri)->equals($oldResource->getUri());
        $queryBuilder = $queryBuilder->setCriteria($criteria);
        $results = $search->getGateway()->search($queryBuilder);

        if ($results->total() !== 0) {
            $parentResource = $results->current();
            ++$this->migrationInformation['old_resource_migration_count'];
        }

        return $parentResource;
    }

    /**
     * Check if the file serializer service needs to be updated
     *
     * @return bool
     */
    private function fileSerializerNeedsUpdate()
    {
        $currentFileReferenceSerializer = $this->serviceManager->get(FileReferenceSerializer::SERVICE_ID);

        if ($currentFileReferenceSerializer instanceof UrlFileSerializer) {
            return false;
        }

        return true;
    }

    /**
     * Migrate ResourceFileSerializer resources to UrlFileSerializer resources
     *
     * @param mixed[][] $oldResourcesData
     * @return void
     * @throws InvalidServiceManagerException
     * @throws SearchGateWayExeption
     * @throws common_exception_Error
     */
    public function migrateResources(array $oldResourcesData)
    {
        $serviceLocator = $this->serviceLocator;
        $this->urlFileSerializer->setServiceLocator($serviceLocator);
        $this->resourceFileSerializer->setServiceLocator($serviceLocator);

        foreach ($oldResourcesData as $oldResourceData) {
            foreach ($oldResourceData['properties'] as $data) {
                $this->migrateResource($oldResourceData['resource'], $data['predicate']);
            }
        }
    }

    /**
     * Get the search service
     * @throws \oat\oatbox\service\exception\InvalidServiceManagerException
     */
    private function getSearch()
    {
        if ($this->search !== null) {
            return $this->search;
        }

        $this->search = $this->serviceManager->get(ComplexSearchService::SERVICE_ID);
        return $this->search;
    }
}
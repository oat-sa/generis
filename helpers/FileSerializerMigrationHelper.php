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
use oat\oatbox\service\ServiceManagerAwareTrait;
use oat\search\base\exception\SearchGateWayExeption;

/**
 * Helper class for the File serializer migration script
 * @see \oat\generis\scripts\tools\FileSerializerMigration
 */
class FileSerializerMigrationHelper
{

    use OntologyAwareTrait;
    use ServiceManagerAwareTrait;

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
     * @var bool
     */
    private $isWetRun;

    /**
     * @var int[]
     */
    public $migrationInformation = [
        'old_resource_count' => 0,
        'old_resource_migration_count' => 0,
        'migrated_count' => 0
    ];

    /**
     * @var string[][]
     */
    public $failedResources = [];

    /**
     * FileSerializerMigrationHelper constructor.
     * @param bool $wetRun
     */
    public function __construct($wetRun = false)
    {
        $this->isWetRun = $wetRun;
        $this->urlFileSerializer = new UrlFileSerializer();
        $this->resourceFileSerializer = new ResourceFileSerializer();

        $serviceManager = ServiceManager::getServiceManager();
        $this->urlFileSerializer->setServiceLocator($serviceManager);
        $this->resourceFileSerializer->setServiceLocator($serviceManager);
    }

    /**
     * Get the resources that should be migrated.
     *
     * @return mixed[][]
     */
    public function getOldResourcesData()
    {
        $oldResources = [];
        $fileResources = $this->getClass(GenerisRdf::CLASS_GENERIS_FILE)->getInstances(true);

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
     * @throws SearchGateWayExeption
     */
    public function migrateResource($oldResource, $predicateUri)
    {
        $property = $this->getProperty($predicateUri);
        $resource = $this->getParentResource($oldResource, $predicateUri);
        if ($resource === null) {
            return;
        }

        /** @var Directory|File $unserializedFileResource */
        $unserializedFileResource = $this->resourceFileSerializer->unserialize($oldResource);
        $migratedValue = $this->urlFileSerializer->serialize($unserializedFileResource);


        if ($this->isWetRun) {
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
        $updated = false;
        if ($this->fileSerializerNeedsUpdate()) {
            if ($this->isWetRun) {
                ServiceManager::getServiceManager()->register(FileReferenceSerializer::SERVICE_ID, new UrlFileSerializer());
            }
            $updated = true;
        }

        return $updated;
    }

    /**
     * Get the properties that are using this resource
     *
     * @param core_kernel_classes_Resource $fileResource
     * @return string[][]
     */
    public function getPropertiesForResource($fileResource)
    {
        $fileResourceUri = $fileResource->getUri();
        $sql = "SELECT predicate FROM statements WHERE object = '" . $fileResourceUri . "'";
        $persistence = $fileResource->getModel()->getPersistence();
        $properties = $persistence->query($sql)->fetchAll();

        if (empty($properties)) {
            $this->failedResources[$fileResourceUri][] = 'Resource is not used by any property';
        }

        return $properties;
    }

    /**
     * @param $oldResource
     * @param $predicateUri
     * @return core_kernel_classes_Resource|null
     * @throws SearchGateWayExeption
     */
    private function getParentResource($oldResource, $predicateUri)
    {
        $parentResource = null;
        $oldResourceUri = $oldResource->getUri();
        $search = $this->getSearch();
        $queryBuilder = $search->query();
        $query = $queryBuilder->newQuery();
        $criteria = $query->add($predicateUri)->equals($oldResourceUri);
        $queryBuilder = $queryBuilder->setCriteria($criteria);
        $results = $search->getGateway()->search($queryBuilder);

        if ($results->total() !== 0) {
            $parentResource = $results->current();
            ++$this->migrationInformation['old_resource_migration_count'];
        }

        if ($parentResource === null) {
            $this->failedResources[$oldResourceUri][] = 'Unable to retrieve parent resource';
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
        $needsUpdate = true;
        $currentFileReferenceSerializer = ServiceManager::getServiceManager()->get(FileReferenceSerializer::SERVICE_ID);
        if ($currentFileReferenceSerializer instanceof UrlFileSerializer) {
            $needsUpdate = false;
        }

        return $needsUpdate;
    }

    /**
     * Migrate ResourceFileSerializer resources to UrlFileSerializer resources
     *
     * @param mixed[][] $oldResourcesData
     * @return void
     * @throws SearchGateWayExeption
     * @throws FileSerializerException
     */
    public function migrateResources(array $oldResourcesData)
    {
        foreach ($oldResourcesData as $oldResourceData) {
            foreach ($oldResourceData['properties'] as $data) {
                $this->migrateResource($oldResourceData['resource'], $data['predicate']);
            }
        }
    }

    /**
     * Get the search service
     */
    private function getSearch()
    {
        if ($this->search === null) {
            $this->search = ServiceManager::getServiceManager()->get(ComplexSearchService::SERVICE_ID);
        }

        return $this->search;
    }
}
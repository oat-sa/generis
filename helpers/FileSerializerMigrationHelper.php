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

use core_kernel_classes_Resource;
use oat\generis\model\fileReference\FileSerializerException;
use oat\generis\model\fileReference\ResourceFileIterator;
use oat\generis\model\fileReference\ResourceFileSerializer;
use oat\generis\model\fileReference\UrlFileSerializer;
use oat\generis\model\GenerisRdf;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\File;
use oat\oatbox\service\ServiceManagerAwareTrait;
use oat\search\base\exception\SearchGateWayExeption;
use Zend\ServiceManager\ServiceLocatorInterface;

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
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);
    }

    /**
     * @return UrlFileSerializer
     */
    protected function getUrlFileSerializer()
    {
        if (empty($this->urlFileSerializer)) {
            $this->urlFileSerializer = new UrlFileSerializer();
            $this->urlFileSerializer->setServiceLocator($this->getServiceLocator());
        }
        return $this->urlFileSerializer;
    }

    /**
     * @return ResourceFileSerializer
     */
    protected function getResourceFileSerializer()
    {
        if (empty($this->resourceFileSerializer)) {
            $this->resourceFileSerializer = new ResourceFileSerializer();
            $this->resourceFileSerializer->setServiceLocator($this->getServiceLocator());
        }
        return $this->resourceFileSerializer;
    }

    /**
     * Get the resources that should be migrated.
     *
     * @param int $cacheSize
     * @return ResourceFileIterator
     */
    public function getOldResourcesData($cacheSize = 100)
    {
        $iterator = new ResourceFileIterator([GenerisRdf::CLASS_GENERIS_FILE], $cacheSize);
        return $iterator;
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
        $unserializedFileResource = $this->getResourceFileSerializer()->unserialize($oldResource);
        $migratedValue = $this->getUrlFileSerializer()->serialize($unserializedFileResource);

        $resource->editPropertyValues($property, $migratedValue);
        $oldResource->delete();

        ++$this->migrationInformation['migrated_count'];
    }

    /**
     * @param $oldResource
     * @param $predicateUri
     * @return core_kernel_classes_Resource|null
     * @throws SearchGateWayExeption
     */
    private function getParentResource(core_kernel_classes_Resource $oldResource, $predicateUri)
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
     * Migrate ResourceFileSerializer resources to UrlFileSerializer resources
     *
     * @param mixed[][] $oldResourcesData
     * @return void
     * @throws SearchGateWayExeption
     * @throws FileSerializerException
     */
    public function migrateResources(array $oldResourcesData)
    {
        foreach ($oldResourcesData as $key => $oldResourceData) {
            if (empty($oldResourceData['properties'])) {
                $this->failedResources[$key][] = 'Resource is not used by any property';
            }
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
            $this->search = $this->getServiceLocator()->get(ComplexSearchService::SERVICE_ID);
        }
        return $this->search;
    }
}

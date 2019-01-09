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
 */

namespace oat\generis\scripts\tools\FileSerializerMigration;

use core_kernel_classes_Resource;
use oat\generis\model\fileReference\FileSerializerException;
use oat\generis\model\fileReference\ResourceFileSerializer;
use oat\generis\model\fileReference\UrlFileSerializer;
use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\File;
use oat\oatbox\service\ServiceManager;
use oat\tao\model\resources\ResourceIterator;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Helper class for the File serializer migration script
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
     * @var ServiceManager
     */
    private $serviceLocator;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * FileSerializerMigrationHelper constructor.
     *
     * @param bool $wetRun
     */
    public function __construct($wetRun = false)
    {
        $this->isWetRun = $wetRun;
    }

    /**
     * Get the file resources and migrate them.
     *
     * @return void
     * @throws FileSerializerException
     * @throws \common_exception_Error
     */
    public function migrateFiles()
    {
        $fileResources = new ResourceFileIterator(GenerisRdf::CLASS_GENERIS_FILE);

        foreach ($fileResources as $fileResourceData) {
            $resourceUri = $fileResourceData['resource']->getUri();
            if (!isset($fileResourceData['property'])) {
                $this->failedResources[$resourceUri][] = 'Unable to find property';
                continue;
            }
            if (!isset($fileResourceData['parent'])) {
                $this->failedResources[$resourceUri][] = 'Unable to find parent resource';
                continue;
            }

            ++$this->migrationInformation['old_resource_count'];
            $this->migrateResource(
                $fileResourceData['resource'], $fileResourceData['property'], $fileResourceData['parent']
            );
        }
    }

    /**
     * Migrate a single resource.
     *
     * @param core_kernel_classes_Resource $oldResource
     * @param string $predicateUri
     * @param string $parentResourceUri
     * @return void
     * @throws FileSerializerException
     */
    public function migrateResource(core_kernel_classes_Resource $oldResource, $predicateUri, $parentResourceUri)
    {
        $property = $this->getProperty($predicateUri);
        $resource = $this->getResource($parentResourceUri);
        if ($resource === null) {
            return;
        }

        /** @var Directory|File $unserializedFileResource */
        $unserializedFileResource = $this->getResourceFileSerializer()->unserialize($oldResource);
        $migratedValue = $this->getUrlFileSerializer()->serialize($unserializedFileResource);


        if ($this->isWetRun) {
            $resource->editPropertyValues($property, $migratedValue);
            $oldResource->delete();
        }

        ++$this->migrationInformation['migrated_count'];
    }

    /**
     * Get the URL file serializer
     */
    private function getUrlFileSerializer()
    {
        if ($this->urlFileSerializer === null) {
            $this->urlFileSerializer = new UrlFileSerializer();
            $this->urlFileSerializer->setServiceLocator($this->getServiceLocator());
        }

        return $this->urlFileSerializer;
    }

    /**
     * Get the Resource file serializer
     */
    private function getResourceFileSerializer()
    {
        if ($this->resourceFileSerializer === null) {
            $this->resourceFileSerializer = new ResourceFileSerializer();
            $this->resourceFileSerializer->setServiceLocator($this->getServiceLocator());
        }

        return $this->resourceFileSerializer;
    }

    /**
     * Set the Service Locator for this class
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get the Service Locator
     *
     * @return ServiceLocatorInterface
     */
    private function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set the Service Manager for this class
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Get the Service Manager
     *
     * @return ServiceManager
     */
    private function getServiceManager()
    {
        return $this->serviceManager;
    }
}
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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\generis\scripts\tools\FileSerializerMigration;

use core_kernel_classes_Property;
use core_kernel_classes_Resource;
use oat\generis\model\resource\ResourceCollection;
use oat\generis\model\fileReference\FileSerializerException;
use oat\generis\model\fileReference\ResourceFileSerializer;
use oat\generis\model\fileReference\UrlFileSerializer;
use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\File;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Helper class for the File serializer migration script
 */
class MigrationHelper implements ServiceLocatorAwareInterface
{
    use OntologyAwareTrait;
    use ServiceLocatorAwareTrait;

    /**
     * Amount of resources processed in one batch
     */
    const BATCH_LIMIT = 100;

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
     * @var bool
     */
    public $endReached = false;

    /**
     * @var ResourceCollection
     */
    private $fileResourceCollection;

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
     */
    public function migrateFiles()
    {
        $fileResources = $this->getFileResourceData();

        foreach ($fileResources as $fileResourceData) {
            $resourceUri = $fileResourceData['fileResource']->getUri();
            if (!isset($fileResourceData['property'])) {
                $this->failedResources[$resourceUri][] = 'Unable to find property';
                continue;
            }
            if (!isset($fileResourceData['subject'])) {
                $this->failedResources[$resourceUri][] = 'Unable to find subject';
                continue;
            }

            ++$this->migrationInformation['old_resource_count'];
            $this->migrateResource(
                $fileResourceData['fileResource'],
                $fileResourceData['property'],
                $fileResourceData['subject']
            );
        }
    }

    /**
     * Migrate a single resource.
     *
     * @param core_kernel_classes_Resource $fileResource
     * @param core_kernel_classes_Property $property
     * @param core_kernel_classes_Resource $subject
     * @return void
     * @throws FileSerializerException
     */
    public function migrateResource(core_kernel_classes_Resource $fileResource, core_kernel_classes_Property $property, core_kernel_classes_Resource $subject)
    {
        /** @var Directory|File $unserializedFileResource */
        $unserializedFileResource = $this->getResourceFileSerializer()->unserialize($fileResource);
        $migratedValue = $this->getUrlFileSerializer()->serialize($unserializedFileResource);

        if ($this->isWetRun) {
            $subject->editPropertyValues($property, $migratedValue);
            $fileResource->delete();
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
     * @return array
     */
    private function getFileResourceData()
    {
        $mappedResourceData = [];
        if ($this->fileResourceCollection === null) {
            $this->fileResourceCollection = $this->getClass(GenerisRdf::CLASS_GENERIS_FILE)->getInstanceCollection();
            $this->fileResourceCollection->useLimit();
        }

        $fileResourceUris = [];
        foreach ($this->fileResourceCollection as $fileResource) {
            $fileResourceUris[] = $fileResource['subject'];
        }

        $this->endReached = $this->fileResourceCollection->getEndReached();

        $parentFileResources = new ResourceCollection(null, 0);
        $parentFileResources->addFilter('object', 'IN', $fileResourceUris);

        foreach ($parentFileResources as $resourceUri => $parentFileResource) {
            $mappedResourceData[] = [
                'fileResource' => $this->getResource($parentFileResource['object']),
                'property' => $this->getProperty($parentFileResource['predicate']),
                'subject' => $this->getResource($parentFileResource['subject']),
            ];
        }

        return $mappedResourceData;
    }
}

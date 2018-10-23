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

namespace oat\generis\test\unit\helpers;

use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use oat\generis\Helper\FileSerializerMigrationHelper;
use oat\generis\model\fileReference\FileReferenceSerializer;
use oat\generis\model\fileReference\ResourceFileSerializer;
use oat\generis\model\fileReference\UrlFileSerializer;
use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\oatbox\service\ServiceManager;
use oat\taoDevTools\helper\DataGenerator;

/**
 * Test cases for the File serializer migration script helper
 * @see \oat\generis\Helper\FileSerializerMigrationHelper
 */
class FileSerializerMigrationHelperTest extends GenerisPhpUnitTestRunner
{

    /**
     * @var FileSerializerMigrationHelper
     */
    protected $fileMigrationHelper;

    /**
     * @var core_kernel_classes_Resource[]
     */
    private $testItems;

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
    private $fileSerializerSwitched = false;

    /**
     * Initialize test
     * @throws \oat\oatbox\service\exception\InvalidServiceManagerException
     */
    public function setUp()
    {
        $this->fileMigrationHelper = new FileSerializerMigrationHelper(true);
        $this->urlFileSerializer = new UrlFileSerializer();
        $this->resourceFileSerializer = new ResourceFileSerializer();

        $currentFileSerializer = ServiceManager::getServiceManager()->get(FileReferenceSerializer::SERVICE_ID);
        if ($currentFileSerializer instanceof UrlFileSerializer) {
            $this->fileSerializerSwitched = true;
            ServiceManager::getServiceManager()->register(FileReferenceSerializer::SERVICE_ID, new ResourceFileSerializer());
        }
    }

    /**
     * Test the migration of a file resource
     */
    public function testResourceMigration()
    {
        $this->generateFileResources();
        foreach ($this->testItems as $testItem) {
            $resource = $testItem->getOnePropertyValue(
                $testItem->getProperty('http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent')
            );
            $this->fileMigrationHelper->migrateResource($resource, 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent');
        }

        static::assertSame($this->fileMigrationHelper->migrationInformation['migrated_count'], 10);

    }

    /**
     * Generate 10 file resources used for testing
     */
    private function generateFileResources()
    {
        if ($this->testItems !== null) {
            $this->tearDown();
        }

        /** @var core_kernel_classes_Class $itemClass */
        $itemClass = DataGenerator::generateItems(10);
        $this->testItems = $itemClass->getInstances(true);
    }

    /**
     * Clean up after running tests
     */
    public function tearDown()
    {
        if ($this->fileSerializerSwitched === true) {
            ServiceManager::getServiceManager()->register(
                FileReferenceSerializer::SERVICE_ID, new UrlFileSerializer()
            );
        }

        if ($this->testItems !== null) {
            foreach ($this->testItems as $testItem) {
                $testItem->delete();
            }
        }
    }
}
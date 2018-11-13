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

use core_kernel_classes_Resource;
use oat\generis\Helper\FileSerializerMigrationHelper;
use oat\generis\model\GenerisRdf;
use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\oatbox\service\ServiceManager;

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
     * @var core_kernel_classes_Resource[]
     */
    private $testResources;

    /**
     * @var \core_kernel_classes_Class
     */
    protected $testClass;

    /**
     * Initialize test
     * @throws \oat\oatbox\service\exception\InvalidServiceManagerException
     */
    public function setUp()
    {
        $this->fileMigrationHelper = new FileSerializerMigrationHelper(ServiceManager::getServiceManager());
    }

    /**
     * Test the migration of a file resource
     */
    public function testResourceMigration()
    {
        $this->generateFileResources();
        foreach ($this->testItems as $testItem) {
            /** @var \core_kernel_classes_Resource $resource */
            $resource = $testItem->getOnePropertyValue(
                $testItem->getProperty('http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent')
            );
            $this->fileMigrationHelper->migrateResource($resource, 'http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent');
        }

        static::assertSame($this->fileMigrationHelper->migrationInformation['migrated_count'], 2);

    }

    /**
     * Generate 10 file resources used for testing
     */
    private function generateFileResources()
    {
        $clazz = new \core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAOItem.rdf#Item');
        if ($clazz->isClass()) {
            $clazz = new \core_kernel_classes_Class($clazz);
            $this->testClass = \core_kernel_classes_ClassFactory::createSubClass($clazz, 'testClass1', '');
            $fileClass = new \core_kernel_classes_Class(GenerisRdf::CLASS_GENERIS_FILE);

            $item1 = \core_kernel_classes_ResourceFactory::create($this->testClass, 'testItem1', '');
            $item1->setPropertiesValues(['http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel' => 'http://www.tao.lu/Ontologies/TAOItem.rdf#QTI']);
            $resource1 = $fileClass->createInstanceWithProperties(array(
                GenerisRdf::PROPERTY_FILE_FILENAME => 'testFile1',
                GenerisRdf::PROPERTY_FILE_FILEPATH => 'testPath1',
                GenerisRdf::PROPERTY_FILE_FILESYSTEM => 'itemDirectory1'
            ));
            $item1->setPropertiesValues(['http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent' => $resource1->getUri()]);
            $this->testResources[] = $resource1;
            $this->testItems[] = $item1;
            $item2 = \core_kernel_classes_ResourceFactory::create($this->testClass, 'testItem2', '');
            $item2->setPropertiesValues(['http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel' => 'http://www.tao.lu/Ontologies/TAOItem.rdf#QTI']);
            $resource2 = $fileClass->createInstanceWithProperties(array(
                GenerisRdf::PROPERTY_FILE_FILENAME => 'testFile2',
                GenerisRdf::PROPERTY_FILE_FILEPATH => 'testPath2',
                GenerisRdf::PROPERTY_FILE_FILESYSTEM => 'itemDirectory2'
            ));
            $item2->setPropertiesValues(['http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent' => $resource2->getUri()]);
            $this->testItems[] = $item2;
            $this->testResources[] = $resource2;
        }
    }

    /**
     * Clean up after running tests
     */
    public function tearDown()
    {
        if ($this->testClass !== null) {
            $this->testClass->delete();
        }

        if ($this->testItems !== null) {
            foreach ($this->testItems as $item) {
                $item->delete();
            }
        }

        if ($this->testResources !== null) {
            foreach ($this->testResources as $resource) {
                $resource->delete();
            }
        }
    }
}
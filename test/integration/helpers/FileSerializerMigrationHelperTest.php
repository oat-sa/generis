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

namespace oat\generis\test\integration\helpers;

use common_Config;
use core_kernel_persistence_smoothsql_SmoothModel;
use oat\generis\scripts\tools\FileSerializerMigration\MigrationHelper;
use oat\generis\model\fileReference\ResourceFileSerializer;
use oat\generis\model\fileReference\UrlFileSerializer;
use oat\generis\model\GenerisRdf;
use oat\generis\test\GenerisTestCase;
use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\File;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\filesystem\FileSystemHandler;
use oat\generis\test\FileSystemMockTrait;

/**
 * Test cases for the File serializer migration script helper
 * @see \oat\generis\Helper\FileSerializerMigrationHelper
 */
class FileSerializerMigrationHelperTest extends GenerisTestCase
{
    use FileSystemMockTrait;

    const PARENT_RESOURCE_URI = 'http://www.tao.lu/Ontologies/generis.rdf#UnitTest';
    const PROPERTY_URI = 'http://www.tao.lu/Ontologies/generis.rdf#TestFile';
    const SAMPLE_FILE = 'fileMigrationUnitTest.txt';

    /**
     * @var MigrationHelper
     */
    protected $fileMigrationHelper;

    /**
     * @var \core_kernel_classes_Class
     */
    protected $testClass;

    /**
     * @var core_kernel_persistence_smoothsql_SmoothModel
     */
    private $ontologyMock;

    /**
     * @var ResourceFileSerializer
     */
    private $resourceFileSerializer;

    /**
     * @var UrlFileSerializer
     */
    private $urlFileSerializer;

    /**
     * @var string
     */
    private $tempFileSystemId;

    /**
     * @var
     */
    private $tempDirectory;

    /**
     * @var File|Directory
     */
    private $testFile;

    /**
     * @var FileSystemService
     */
    private $fileSystemService;

    /**
     * Initialize test
     */
    public function setUp(): void
    {
        common_Config::load();
        $this->fileMigrationHelper = new MigrationHelper();
        $this->resourceFileSerializer = new ResourceFileSerializer();
        $this->urlFileSerializer = new UrlFileSerializer();

        $serviceLocator = $this->getServiceLocatorMock([FileSystemService::SERVICE_ID => $this->getMockFileSystem()]);
        $this->fileMigrationHelper->setServiceLocator($serviceLocator);
        $this->resourceFileSerializer->setServiceLocator($serviceLocator);
        $this->urlFileSerializer->setServiceLocator($serviceLocator);

        $this->ontologyMock = $this->getOntologyMock();
    }

    /**
     * Test the migration of a file resource
     */
    public function testResourceMigration()
    {
        try {
            $fileResource = $this->getFileResource();
            $this->fileMigrationHelper->migrateResource(
                $fileResource,
                $this->ontologyMock->getProperty(self::PARENT_RESOURCE_URI),
                $this->ontologyMock->getResource(self::PROPERTY_URI)
            );
        } catch (\Exception $e) {
            if ($this->testFile !== null) {
                $this->testFile->delete();
            }
            throw new \Exception($e->getMessage());
        }

        self::assertSame($this->fileMigrationHelper->migrationInformation['migrated_count'], 1);

        $this->testFile->delete();
    }

    /**
     * Generate a file resource used for testing
     */
    private function getFileResource()
    {
        $dir = $this->getTempDirectory();
        $fileClass = $this->ontologyMock->getClass(GenerisRdf::CLASS_GENERIS_FILE);
        $this->testFile = $dir->getFile(self::SAMPLE_FILE);
        $this->testFile->write(self::SAMPLE_FILE, 'PHP Unit test file');

        if ($this->testFile instanceof File) {
            $filename = $this->testFile->getBasename();
            $filePath = dirname($this->testFile->getPrefix());
        } elseif ($this->testFile instanceof Directory) {
            $filename = '';
            $filePath = $this->testFile->getPrefix();
        } else {
            return false;
        }

        $resource = $fileClass->createInstanceWithProperties(
            [
                GenerisRdf::PROPERTY_FILE_FILENAME => $filename,
                GenerisRdf::PROPERTY_FILE_FILEPATH => $filePath,
                GenerisRdf::PROPERTY_FILE_FILESYSTEM => $this->ontologyMock->getResource($this->testFile->getFileSystemId()),
            ]
        );

        self::assertInstanceOf(FileSystemHandler::class, $this->resourceFileSerializer->unserialize($resource));

        $unitTestResource = $this->ontologyMock->getResource(self::PARENT_RESOURCE_URI);
        $testFileProperty = $this->ontologyMock->getProperty(self::PROPERTY_URI);
        $unitTestResource->setPropertyValue($testFileProperty, $unitTestResource);

        return $resource;
    }

    /**
     * @return Directory
     */
    private function getTempDirectory()
    {
        if (!$this->tempDirectory) {
            $this->tempDirectory = $this->getFileSystemMock(['unit-test'])->getDirectory('unit-test');
        }
        return $this->tempDirectory;
    }

    protected function tearDown(): void
    {
        $dir = $this->getTempDirectory();
        $this->testFile = $dir->getFile(self::SAMPLE_FILE);
        $this->testFile->delete();
    }

    /**
     * @return FileSystemService
     */
    private function getMockFileSystem()
    {
        if ($this->fileSystemService === null) {
            $this->fileSystemService = $this->getServiceLocatorMock([FileSystemService::SERVICE_ID => new FileSystemService()])->get(FileSystemService::SERVICE_ID);
        }

        return $this->fileSystemService;
    }
}

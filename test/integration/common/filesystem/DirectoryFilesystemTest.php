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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\generis\test\integration\common\filesystem;

use oat\generis\scripts\tools\FileSerializerMigration\MigrationHelper;
use oat\generis\test\GenerisTestCase;
use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\File;
use oat\oatbox\filesystem\FileSystemService;

class DirectoryFilesystemTest extends GenerisTestCase
{
    /**
     * @var MigrationHelper
     */
    protected $fileMigrationHelper;

    /**
     * @var \core_kernel_classes_Class
     */
    protected $testClass;

    /**
     * @var string
     */
    private $tempFileSystemId;

    /**
     * @var Directory
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

    public function testDirectoryRename()
    {
        $this->generateFile('/testDirectory/fileRename.txt');
        $directory = $this->tempDirectory->getDirectory('testDirectory');
        $this->assertTrue($directory->rename('testDirectory.back'));
    }

    public function testDirectoryRenameAlreadyExistException()
    {
        $this->generateFile('/testDirectory.exist/fileRename.txt');
        $directory = $this->tempDirectory->getDirectory('testDirectory.exist');
        $this->expectExceptionObject(new \common_exception_FileSystemError("Unable to rename 'testDirectory.exist' into 'testDirectory.exist'. File already exists."));
        $directory->rename('testDirectory.exist');
    }


    /**
     * @return bool
     * @throws \League\Flysystem\FileExistsException
     * @throws \common_Exception
     */
    private function generateFile($path)
    {
        $dir = $this->getTempDirectory();
        $this->testFile = $dir->getFile($path);
        $this->testFile->write($path, 'PHP Unit test file');
        return true;
    }

    /**
     * @return Directory
     */
    private function getTempDirectory()
    {
        if (!$this->tempDirectory) {
            $fileSystemService = $this->getMockFileSystem();
            $this->tempFileSystemId = uniqid('rename-test-', true);

            $adapters = $fileSystemService->getOption(FileSystemService::OPTION_ADAPTERS);
            if (class_exists('League\Flysystem\Memory\MemoryAdapter')) {
                $adapters[$this->tempFileSystemId] = [
                    'class' => \League\Flysystem\Memory\MemoryAdapter::class
                ];
            } else {
                $adapters[$this->tempFileSystemId] = [
                    'class' => FileSystemService::FLYSYSTEM_LOCAL_ADAPTER,
                    'options' => ['root' => '/tmp/testing']
                ];
            }
            $fileSystemService->setOption(FileSystemService::OPTION_ADAPTERS, $adapters);
            $fileSystemService->setOption(FileSystemService::OPTION_DIRECTORIES, [$this->tempFileSystemId => $this->tempFileSystemId]);
            $fileSystemService->setOption(FileSystemService::OPTION_FILE_PATH, '/tmp/unit-test');

            $fileSystemService->setServiceLocator($this->getServiceLocatorMock([
                FileSystemService::SERVICE_ID => $fileSystemService
            ]));

            $this->tempDirectory = $fileSystemService->getDirectory($this->tempFileSystemId);
        }
        return $this->tempDirectory;
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

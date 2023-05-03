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

use common_Exception;
use common_exception_FileSystemError;
use core_kernel_classes_Class;
use oat\generis\scripts\tools\FileSerializerMigration\MigrationHelper;
use oat\generis\test\FileSystemMockTrait;
use oat\generis\test\GenerisTestCase;
use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\File;
use oat\oatbox\filesystem\FileSystemService;

class DirectoryFilesystemTest extends GenerisTestCase
{
    use FileSystemMockTrait;

    /**
     * @var MigrationHelper
     */
    protected $fileMigrationHelper;

    /**
     * @var core_kernel_classes_Class
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
        $this->expectException(common_exception_FileSystemError::class);
        $directory->rename('testDirectory.exist');
    }

    public function testGetFullPathFile()
    {
        $file = $this->getTempDirectory()->getFile('fixture');
        $fileSystemId = $file->getFileSystemId();

        $fileSystemService = new FileSystemService([
            FileSystemService::OPTION_FILE_PATH => '/tmp/testing',
            FileSystemService::OPTION_ADAPTERS => [
                $fileSystemId => [
                    'class' => FileSystemService::FLYSYSTEM_LOCAL_ADAPTER,
                    'options' => ['root' => $file->getFileSystemId()],
                ],
            ],
        ]);

        $file = $this->getTempDirectory()->getFile('fixture');

        $result = $fileSystemService->getFileAdapterByFile($file);

        $this->assertEquals($result->getPathPrefix(), 'unit-test/');
    }

    /**
     * @param mixed $path
     *
     * @throws \League\Flysystem\FileExistsException
     * @throws common_Exception
     *
     * @return bool
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
            $this->tempDirectory = $this->getFileSystemMock(['unit-test'])->getDirectory('unit-test');
        }

        return $this->tempDirectory;
    }

    /**
     * @return FileSystemService
     */
    private function getMockFileSystem()
    {
        if ($this->fileSystemService === null) {
            $this->fileSystemService = $this
                ->getServiceLocatorMock([FileSystemService::SERVICE_ID => new FileSystemService()])
                ->get(FileSystemService::SERVICE_ID);
        }

        return $this->fileSystemService;
    }
}

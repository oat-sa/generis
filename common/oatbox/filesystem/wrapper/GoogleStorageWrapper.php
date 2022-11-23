<?php

/**
 * Copyright 2016 Open Assessment Technologies SA
 *
 * This file is part of the Tao AWS tools.
 *
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
 * Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this package.
 * If not, see http://www.gnu.org/licenses/.
 *
 */

namespace oat\oatbox\filesystem\wrapper;

use League\Flysystem\Config;
use League\Flysystem\DirectoryListing;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;
use oat\oatbox\service\ConfigurableService;
use League\Flysystem\FilesystemAdapter;
use oat\oatbox\filesystem\utils\FlyWrapperTrait;
use oat\oatbox\log\LoggerAwareTrait;
use Google\Cloud\Storage\StorageClient;
use League\Flysystem\GoogleCloudStorage\GoogleCloudStorageAdapter;

/**
 * @deprecated Please install `oat-sa/lib-generis-gcp` and use \oat\Gcp\Gcs\GcsFlyWrapper
 * @author Joel Bout
 */
class GoogleStorageWrapper extends ConfigurableService implements FilesystemOperator
{
    use FlyWrapperTrait;
    use LoggerAwareTrait;
    
    const OPTION_BUCKET = 'bucket';

    const OPTION_CLIENT_CONFIG = 'clientConfig';

    private $adapter;
    
    /**
     * @return StorageClient
     */
    private function getClient()
    {
        return new StorageClient($this->getOption(self::OPTION_CLIENT_CONFIG));
    }
    
    /**
     * @return FilesystemAdapter
     */
    public function getAdapter()
    {
        if (is_null($this->adapter)) {
            $client = $this->getClient();
            $bucket = $client->bucket($this->getOption('bucket'));
            $adapter = new GoogleCloudStorageAdapter($bucket);
            $this->adapter = $adapter;
        }
        return $this->adapter;
    }

    public function fileExists(string $path): bool
    {
        return $this->getAdapter()->directoryExists($path);
    }

    public function directoryExists(string $path): bool
    {
        return $this->getAdapter()->directoryExists($path);
    }

    public function deleteDirectory(string $path): void
    {
        $this->getAdapter()->deleteDirectory($path);
    }

    public function createDirectory(string $location, array $configParams = []): void
    {
        $config = new Config($configParams);
        $this->getAdapter()->createDirectory($location, $config);
    }

    public function visibility(string $path): string
    {
        return $this->getAdapter()->visibility($path)->visibility();
    }

    public function mimeType(string $path): string
    {
        return $this->getAdapter()->mimeType($path)->mimeType();
    }

    public function lastModified(string $path): int
    {
        return $this->getAdapter()->lastModified($path)->lastModified();
    }

    public function fileSize(string $path): int
    {
        return $this->getAdapter()->fileSize($path)->fileSize();
    }

    public function move(string $source, string $destination, array $configParams = []): void
    {
        $config = new Config($configParams);
        $this->getAdapter()->move($source, $destination, $config);
    }

    public function __call($name, $arguments)
    {
        return call_user_func($name, $this->getAdapter(), ...$arguments);
    }

    public function has(string $location): bool
    {
        return $this->getAdapter()->fileExists($location);
    }

    public function read(string $location): string
    {
        return $this->getAdapter()->read($location);
    }

    public function readStream(string $location)
    {
        return $this->getAdapter()->readStream($location);
    }

    /**
     * @throws UnableToWriteFile
     * @throws FilesystemException
     */
    public function write(string $location, string $contents, array $config = []): void
    {
        $this->getAdapter()->write($location, $contents, new Config($config));
    }

    /**
     * @param mixed $contents
     *
     * @throws UnableToWriteFile
     * @throws FilesystemException
     */
    public function writeStream(string $location, $contents, array $config = []): void
    {
        $this->getAdapter()->writeStream($location, $contents, new Config($config));
    }

    /**
     * @throws UnableToSetVisibility
     * @throws FilesystemException
     */
    public function setVisibility(string $path, string $visibility): void
    {
        $this->getAdapter()->setVisibility($path, $visibility);
    }

    /**
     * @throws UnableToDeleteFile
     * @throws FilesystemException
     */
    public function delete(string $location): void
    {
        $this->getAdapter()->delete($location);
    }

    /**
     * @throws UnableToCopyFile
     * @throws FilesystemException
     */
    public function copy(string $source, string $destination, array $config = []): void
    {
       $this->getAdapter()->copy($source, $destination, new Config($config));
    }

    public function listContents(string $location, bool $deep = self::LIST_SHALLOW): DirectoryListing
    {
        return new DirectoryListing($this->getAdapter()->listContents($location, $deep));
    }
}

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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\oatbox\filesystem\utils;

use League\Flysystem\DirectoryListing;
use League\Flysystem\FilesystemException as FlyFilesystemException;
use League\Flysystem\FilesystemOperator;
use oat\oatbox\filesystem\FilesystemException;

/**
 * A trait to facilitate creation of filesystem wrappers
 *
 * @author Joel Bout
 */
trait FileSystemWrapperTrait
{
    /**
     * @see FilesystemOperator::has
     * @throws FilesystemException
     */
    public function has(string $location): bool
    {
        return $this->wrapFileSystemOperation(function () use ($location) {
            return $this->getFileSystem()->has($this->getFullPath($location));
        });
    }

    /**
     * @see FilesystemOperator::directoryExists
     * @throws FilesystemException
     */
    public function directoryExists(string $location): bool
    {
        return $this->wrapFileSystemOperation(function () use ($location) {
            return $this->getFileSystem()->directoryExists($this->getFullPath($location));
        });
    }

    /**
     * @see FilesystemOperator::read
     * @throws FilesystemException
     */
    public function read(string $location): string
    {
        return $this->wrapFileSystemOperation(function () use ($location) {
            return $this->getFileSystem()->read($this->getFullPath($location));
        });
    }

    /**
     * @see FilesystemOperator::readStream
     * @throws FilesystemException
     */
    public function readStream($path)
    {
        return $this->wrapFileSystemOperation(function () use ($path) {
            return $this->getFileSystem()->readStream($this->getFullPath($path));
        });
    }

    /**
     * @see FilesystemOperator::listContents
     * @throws FilesystemException
     */
    public function listContents(string $location = '', bool $deep = self::LIST_SHALLOW): DirectoryListing
    {
        return $this->wrapFileSystemOperation(function () use ($location, $deep) {
            return $this->getFileSystem()->listContents($this->getFullPath($location), $deep);
        });
    }

    /**
     * @see FilesystemOperator::write
     * @throws FilesystemException
     */
    public function write(string $location, string $contents, array $config = []): void
    {
        $this->wrapFileSystemOperation(function () use ($location, $contents, $config) {
            $this->getFileSystem()->write($this->getFullPath($location), $contents, $config);
        });
    }

    /**
     * @see FilesystemOperator::writeStream
     * @throws FilesystemException
     */
    public function writeStream(string $location, $contents, array $config = []): void
    {
        $this->wrapFileSystemOperation(function () use ($location, $contents, $config) {
            $this->getFileSystem()->writeStream($this->getFullPath($location), $contents, $config);
        });
    }

    /**
     * @see FilesystemOperator::copy
     * @throws FilesystemException
     */
    public function copy(string $source, string $destination, array $config = []): void
    {
        $this->wrapFileSystemOperation(function () use ($source, $destination, $config) {
            $this->getFileSystem()->copy($this->getFullPath($source), $destination, $config);
        });
    }

    /**
     * @see FilesystemOperator::delete
     * @throws FilesystemException
     */
    public function delete(string $location): void
    {
        $this->wrapFileSystemOperation(function () use ($location) {
            $this->getFileSystem()->delete($this->getFullPath($location));
        });
    }

    /**
     * @see FilesystemOperator::setVisibility
     * @throws FilesystemException
     */
    public function setVisibility(string $path, string $visibility): void
    {
        $this->wrapFileSystemOperation(function () use ($path, $visibility) {
            $this->getFileSystem()->setVisibility($this->getFullPath($path), $visibility);
        });
    }

    /**
     * @see FilesystemOperator::fileExists
     * @throws FilesystemException
     */
    public function fileExists(string $location): bool
    {
        return $this->wrapFileSystemOperation(function () use ($location) {
            return $this->getFileSystem()->fileExists($this->getFullPath($location));
        });
    }

    /**
     * @see FilesystemOperator::lastModified
     * @throws FilesystemException
     */
    public function lastModified(string $path): int
    {
        return $this->wrapFileSystemOperation(function () use ($path) {
            return $this->getFileSystem()->lastModified($this->getFullPath($path));
        });
    }

    /**
     * @see FilesystemOperator::fileSize
     * @throws FilesystemException
     */
    public function fileSize(string $path): int
    {
        return $this->wrapFileSystemOperation(function () use ($path) {
            return $this->getFileSystem()->fileSize($this->getFullPath($path));
        });
    }

    /**
     * @see FilesystemOperator::mimeType
     * @throws FilesystemException
     */
    public function mimeType(string $path): string
    {
        return $this->wrapFileSystemOperation(function () use ($path) {
            return $this->getFileSystem()->mimeType($this->getFullPath($path));
        });
    }

    /**
     * @see FilesystemOperator::visibility
     * @throws FilesystemException
     */
    public function visibility(string $path): string
    {
        return $this->wrapFileSystemOperation(function () use ($path) {
            return $this->getFileSystem()->visibility($this->getFullPath($path));
        });
    }

    /**
     * @see FilesystemOperator::deleteDirectory
     * @throws FilesystemException
     */
    public function deleteDirectory(string $location): void
    {
        $this->wrapFileSystemOperation(function () use ($location) {
            $this->getFileSystem()->deleteDirectory($this->getFullPath($location));
        });
    }

    /**
     * @see FilesystemOperator::createDirectory
     * @throws FilesystemException
     */
    public function createDirectory(string $location, array $config = []): void
    {
        $this->wrapFileSystemOperation(function () use ($location, $config) {
            $this->getFileSystem()->createDirectory($this->getFullPath($location), $config);
        });
    }

    /**
     * @see FilesystemOperator::move
     * @throws FilesystemException
     */
    public function move(string $source, string $destination, array $config = []): void
    {
        $this->wrapFileSystemOperation(function () use ($source, $destination, $config) {
            $this->getFileSystem()->move($this->getFullPath($source), $destination, $config);
        });
    }

    private function wrapFileSystemOperation(callable $operation)
    {
        try {
            return $operation();
        } catch (FlyFilesystemException $e) {
            throw new FilesystemException($e->getMessage(), $e->getCode(), $e);
        }
    }

    abstract protected function getFileSystem(): FilesystemOperator;

    abstract protected function getFullPath($path);
}

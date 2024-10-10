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
use League\Flysystem\FilesystemOperator;

/**
 * A trait to facilitate creation of filesystem wrappers
 *
 * @author Joel Bout
 */
trait FileSystemWrapperTrait
{
    /**
     * @see FilesystemOperator::has
     * @inheritDoc
     */
    public function has(string $location): bool
    {
        return $this->getFileSystem()->has($this->getFullPath($location));
    }

    /**
     * @see FilesystemOperator::directoryExists
     * @inheritDoc
     */
    public function directoryExists(string $location): bool
    {
        return $this->getFileSystem()->directoryExists($this->getFullPath($location));
    }

    /**
     * @see FilesystemOperator::read
     * @inheritDoc
     */
    public function read(string $location): string
    {
        return $this->getFileSystem()->read($this->getFullPath($location));
    }

    /**
     * @see FilesystemOperator::readStream
     * @inheritDoc
     */
    public function readStream($path)
    {
        return $this->getFileSystem()->readStream($this->getFullPath($path));
    }

    /**
     * @see FilesystemOperator::listContents
     * @inheritDoc
     */
    public function listContents(string $location = '', bool $deep = self::LIST_SHALLOW): DirectoryListing
    {
        return $this->getFileSystem()->listContents($this->getFullPath($location), $deep);
    }

    /**
     * @see FilesystemOperator::write
     * @inheritDoc
     */
    public function write(string $location, string $contents, array $config = []): void
    {
        $this->getFileSystem()->write($this->getFullPath($location), $contents, $config);
    }

    /**
     * @see FilesystemOperator::writeStream
     * @inheritDoc
     */
    public function writeStream(string $location, $contents, array $config = []): void
    {
        $this->getFileSystem()->writeStream($this->getFullPath($location), $contents, $config);
    }

    /**
     * @see FilesystemOperator::copy
     * @inheritDoc
     */
    public function copy(string $source, string $destination, array $config = []): void
    {
        $this->getFileSystem()->copy($this->getFullPath($source), $destination);
    }

    /**
     * @see FilesystemOperator::delete
     * @inheritDoc
     */
    public function delete(string $location): void
    {
        $this->getFileSystem()->delete($this->getFullPath($location));
    }

    /**
     * @see FilesystemOperator::setVisibility
     * @inheritDoc
     */
    public function setVisibility(string $path, string $visibility): void
    {
        $this->getFileSystem()->setVisibility($this->getFullPath($path), $visibility);
    }

    /**
     * @see FilesystemOperator::fileExists
     * @inheritDoc
     */
    public function fileExists(string $location): bool
    {
        return $this->getFileSystem()->fileExists($this->getFullPath($location));
    }

    /**
     * @see FilesystemOperator::lastModified
     * @inheritDoc
     */
    public function lastModified(string $path): int
    {
        return $this->getFileSystem()->lastModified($this->getFullPath($path));
    }

    /**
     * @see FilesystemOperator::fileSize
     * @inheritDoc
     */
    public function fileSize(string $path): int
    {
        return $this->getFileSystem()->fileSize($this->getFullPath($path));
    }

    /**
     * @see FilesystemOperator::mimeType
     * @inheritDoc
     */
    public function mimeType(string $path): string
    {
        return $this->getFileSystem()->mimeType($this->getFullPath($path));
    }

    /**
     * @see FilesystemOperator::visibility
     * @inheritDoc
     */
    public function visibility(string $path): string
    {
        return $this->getFileSystem()->visibility($this->getFullPath($path));
    }

    /**
     * @see FilesystemOperator::deleteDirectory
     * @inheritDoc
     */
    public function deleteDirectory(string $location): void
    {
        $this->getFileSystem()->deleteDirectory($this->getFullPath($location));
    }

    /**
     * @see FilesystemOperator::createDirectory
     * @inheritDoc
     */
    public function createDirectory(string $location, array $config = []): void
    {
        $this->getFileSystem()->createDirectory($this->getFullPath($location), $config);
    }

    /**
     * @see FilesystemOperator::move
     * @inheritDoc
     */
    public function move(string $source, string $destination, array $config = []): void
    {
        $this->getFileSystem()->move($this->getFullPath($source), $destination, $config);
    }

    abstract protected function getFileSystem(): FilesystemOperator;

    abstract protected function getFullPath($path);
}

<?php

namespace oat\oatbox\filesystem\utils;

use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Config;

/**
 * A trait to facilitate creation of adapter wrappers
 *
 * @author Joel Bout
 */
trait FlyWrapperTrait
{
    /**
     * @see FilesystemAdapter::write()
     * @inheritDoc
     */
    public function write(string $path, string $contents, Config $config): void
    {
        $this->getAdapter()->write($path, $contents, $config);
    }

    /**
     * @see FilesystemAdapter::writeStream()
     * @inheritDoc
     */
    public function writeStream(string $path, $contents, Config $config): void
    {
        $this->getAdapter()->writeStream($path, $contents, $config);
    }

    /**
     * @see FilesystemAdapter::move()
     * @inheritDoc
     */
    public function move(string $source, string $destination, Config $config): void
    {
        $this->getAdapter()->move($source, $destination, $config);
    }

    /**
     * @see FilesystemAdapter::copy()
     * @inheritDoc
     */
    public function copy(string $source, string $destination, Config $config): void
    {
        $this->getAdapter()->copy($source, $destination, $config);
    }

    /**
     * @see FilesystemAdapter::delete()
     * @inheritDoc
     */
    public function delete(string $path): void
    {
        $this->getAdapter()->delete($path);
    }

    /**
     * @see FilesystemAdapter::deleteDirectory()
     * @inheritDoc
     */
    public function deleteDirectory(string $path): void
    {
        $this->getAdapter()->deleteDirectory($path);
    }

    /**
     * @see FilesystemAdapter::createDirectory()
     * @inheritDoc
     */
    public function createDirectory(string $path, Config $config): void
    {
        $this->getAdapter()->createDirectory($path, $config);
    }

    /**
     * @see FilesystemAdapter::setVisibility()
     * @inheritDoc
     */
    public function setVisibility(string $path, string $visibility): void
    {
        $this->getAdapter()->setVisibility($path, $visibility);
    }

    /**
     * @see FilesystemAdapter::fileExists()
     * @inheritDoc
     */
    public function fileExists(string $path): bool
    {
        return $this->getAdapter()->fileExists($path);
    }

    /**
     * @see FilesystemAdapter::directoryExists()
     * @inheritDoc
     */
    public function directoryExists(string $path): bool
    {
        return $this->getAdapter()->directoryExists($path);
    }

    /**
     * @see FilesystemAdapter::read()
     * @inheritDoc
     */
    public function read(string $path): string
    {
        return $this->getAdapter()->read($path);
    }

    /**
     * @see FilesystemAdapter::readStream()
     * @inheritDoc
     */
    public function readStream($path)
    {
        return $this->getAdapter()->readStream($path);
    }

    /**
     * @see FilesystemAdapter::listContents()
     * @inheritDoc
     */
    public function listContents(string $path, bool $deep = false): iterable
    {
        return $this->getAdapter()->listContents($path, $deep);
    }

    /**
     * @see FilesystemAdapter::fileSize()
     * @inheritDoc
     */
    public function fileSize(string $path): FileAttributes
    {
        return $this->getAdapter()->fileSize($path);
    }

    /**
     * @see FilesystemAdapter::mimeType()
     * @inheritDoc
     */
    public function mimeType(string $path): FileAttributes
    {
        return $this->getAdapter()->mimeType($path);
    }

    /**
     * @see FilesystemAdapter::lastModified()
     * @inheritDoc
     */
    public function lastModified(string $path): FileAttributes
    {
        return $this->getAdapter()->lastModified($path);
    }

    /**
     * @see FilesystemAdapter::visibility()
     * @inheritDoc
     */
    public function visibility(string $path): FileAttributes
    {
        return $this->getAdapter()->visibility($path);
    }

    /**
     * Return the adapter implementation
     *
     * @return FilesystemAdapter
     */
    abstract public function getAdapter();
}

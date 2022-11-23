<?php

namespace oat\oatbox\filesystem\utils;

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
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemAdapter::write()
     */
    public function write($path, $contents, Config $config)
    {
        $this->getAdapter()->write($path, $contents, $config);

        return $this->getAdapter()->fileExists($path);
    }
    
    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemAdapter::writeStream()
     */
    public function writeStream($path, $resource, Config $config)
    {
        $this->getAdapter()->writeStream($path, $resource, $config);

        return $this->getAdapter()->fileExists($path);
    }
    
    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemAdapter::write()
     */
    public function update($path, $contents, Config $config)
    {
        $this->getAdapter()->write($path, $contents, $config);

        return $this->getAdapter()->fileExists($path);
    }
    
    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemAdapter::writeStream()
     */
    public function updateStream($path, $resource, Config $config)
    {
        $this->getAdapter()->writeStream($path, $resource, $config);

        return $this->getAdapter()->fileExists($path);
    }
    
    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemAdapter::rename()
     */
    public function rename($path, $newpath, $config = [])
    {
        $this->getAdapter()->move($path, $newpath, $config);

        return $this->getAdapter()->fileExists($newpath);
    }
    
    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemAdapter::copy()
     */
    public function copy($path, $newpath, $config = [])
    {
        $this->getAdapter()->copy($path, $newpath, $config);
    }
    
    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemAdapter::delete()
     */
    public function delete($path)
    {
        $this->getAdapter()->delete($path);

        return !$this->getAdapter()->fileExists($path);
    }
    
    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemAdapter::deleteDir()
     */
    public function deleteDir($dirname)
    {
        $this->getAdapter()->deleteDirectory($dirname);

        return !$this->getAdapter()->directoryExists($dirname);
    }
    
    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemAdapter::createDir()
     */
    public function createDir($dirname, Config $config)
    {
        $this->getAdapter()->createDirectory($dirname, $config);

        return $this->getAdapter()->directoryExists($dirname);
    }
    
    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemAdapter::setVisibility()
     */
    public function setVisibility($path, $visibility)
    {
        $this->getAdapter()->setVisibility($path, $visibility);
        return $this->getAdapter()->visibility($path);
    }
    
    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\ReadInterface::has()
     */
    public function has($path)
    {
        return $this->getAdapter()->fileExists($path);
    }
    
    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemAdapter::read()
     */
    public function read($path)
    {
        return $this->getAdapter()->read($path);
    }
    
    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemAdapter::readStream()
     */
    public function readStream($path)
    {
        return $this->getAdapter()->readStream($path);
    }
    
    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemAdapter::listContents()
     */
    public function listContents($directory = '', $recursive = false)
    {
        return $this->getAdapter()->listContents($directory, $recursive);
    }
    
    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemAdapter::getMetadata()
     */
    public function getMetadata($path)
    {
        $list = iterator_to_array($this->getAdapter()->listContents($path));
        return (array)$list[0];
    }
    
    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemAdapter::getSize()
     */
    public function getSize($path)
    {
        return $this->getAdapter()->fileSize($path);
    }
    
    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemAdapter::getMimetype()
     */
    public function getMimetype($path)
    {
        return $this->getAdapter()->mimeType($path);
    }
    
    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemAdapter::getTimestamp()
     */
    public function getTimestamp($path)
    {
        $list = iterator_to_array($this->getAdapter()->listContents($path));
        return (array)$list[0]['createdAt'] ?? (array)$list[0]['timestamp'];
    }
    
    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemAdapter::getVisibility()
     */
    public function getVisibility($path)
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

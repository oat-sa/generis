<?php
namespace oat\oatbox\filesystem\utils;

use League\Flysystem\AdapterInterface;
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
     * @see \League\Flysystem\AdapterInterface::write()
     */
    public function write($path, $contents, Config $config)
    {
        return $this->getAdapter()->write($this->getFullPath($path), $contents, $config);
    }

    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\AdapterInterface::writeStream()
     */
    public function writeStream($path, $resource, Config $config)
    {
        return $this->getAdapter()->writeStream($this->getFullPath($path), $resource, $config);
    }

    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\AdapterInterface::update()
     */
    public function update($path, $contents, Config $config)
    {
        return $this->getAdapter()->update($this->getFullPath($path), $contents, $config);
    }

    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\AdapterInterface::updateStream()
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->getAdapter()->updateStream($this->getFullPath($path), $resource, $config);
    }

    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\AdapterInterface::rename()
     */
    public function rename($path, $newpath)
    {
        return $this->getAdapter()->rename($this->getFullPath($path), $newpath);
    }

    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\AdapterInterface::copy()
     */
    public function copy($path, $newpath)
    {
        $this->getAdapter()->copy($this->getFullPath($path), $newpath);
    }

    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\AdapterInterface::delete()
     */
    public function delete($path)
    {
        return $this->getAdapter()->delete($this->getFullPath($path));
    }

    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\AdapterInterface::deleteDir()
     */
    public function deleteDir($dirname)
    {
        return $this->getAdapter()->deleteDir($this->getFullPath($dirname));
    }

    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\AdapterInterface::createDir()
     */
    public function createDir($dirname, Config $config)
    {
        return $this->getAdapter()->createDir($this->getFullPath($dirname), $config);
    }

    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\AdapterInterface::setVisibility()
     */
    public function setVisibility($path, $visibility)
    {
        return $this->getAdapter()->setVisibility($this->getFullPath($path), $visibility);
    }

    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\ReadInterface::has()
     */
    public function has($path)
    {
        return $this->getAdapter()->has($this->getFullPath($path));
    }

    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\ReadInterface::read()
     */
    public function read($path)
    {
        return $this->getAdapter()->read($this->getFullPath($path));
    }

    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\ReadInterface::readStream()
     */
    public function readStream($path)
    {
        return $this->getAdapter()->readStream($this->getFullPath($path));
    }

    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\ReadInterface::listContents()
     */
    public function listContents($directory = '', $recursive = false)
    {
        return $this->getAdapter()->listContents($this->getFullPath($directory), $recursive);
    }

    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\ReadInterface::getMetadata()
     */
    public function getMetadata($path)
    {
        return $this->getAdapter()->getMetadata($this->getFullPath($path));
    }

    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\ReadInterface::getSize()
     */
    public function getSize($path)
    {
        return $this->getAdapter()->getSize($this->getFullPath($path));
    }

    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\ReadInterface::getMimetype()
     */
    public function getMimetype($path)
    {
        return $this->getAdapter()->getMimetype($this->getFullPath($path));
    }

    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\ReadInterface::getTimestamp()
     */
    public function getTimestamp($path)
    {
        return $this->getAdapter()->getTimestamp($this->getFullPath($path));
    }

    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\ReadInterface::getVisibility()
     */
    public function getVisibility($path)
    {
        return $this->getAdapter()->getVisibility($this->getFullPath($path));
    }

    /**
     * Return the adapter implementation
     * 
     * @return AdapterInterface
     */
    abstract public function getAdapter();

    abstract public function getFullPath($path);
}
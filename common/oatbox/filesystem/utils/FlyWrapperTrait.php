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
     *
     * @see \League\Flysystem\AdapterInterface::write()
     *
     * @param mixed $path
     * @param mixed $contents
     */
    public function write($path, $contents, Config $config)
    {
        return $this->getAdapter()->write($path, $contents, $config);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\AdapterInterface::writeStream()
     *
     * @param mixed $path
     * @param mixed $resource
     */
    public function writeStream($path, $resource, Config $config)
    {
        return $this->getAdapter()->writeStream($path, $resource, $config);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\AdapterInterface::update()
     *
     * @param mixed $path
     * @param mixed $contents
     */
    public function update($path, $contents, Config $config)
    {
        return $this->getAdapter()->update($path, $contents, $config);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\AdapterInterface::updateStream()
     *
     * @param mixed $path
     * @param mixed $resource
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->getAdapter()->updateStream($path, $resource, $config);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\AdapterInterface::rename()
     *
     * @param mixed $path
     * @param mixed $newpath
     */
    public function rename($path, $newpath)
    {
        return $this->getAdapter()->rename($path, $newpath);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\AdapterInterface::copy()
     *
     * @param mixed $path
     * @param mixed $newpath
     */
    public function copy($path, $newpath)
    {
        $this->getAdapter()->copy($path, $newpath);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\AdapterInterface::delete()
     *
     * @param mixed $path
     */
    public function delete($path)
    {
        return $this->getAdapter()->delete($path);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\AdapterInterface::deleteDir()
     *
     * @param mixed $dirname
     */
    public function deleteDir($dirname)
    {
        return $this->getAdapter()->deleteDir($dirname);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\AdapterInterface::createDir()
     *
     * @param mixed $dirname
     */
    public function createDir($dirname, Config $config)
    {
        return $this->getAdapter()->createDir($dirname, $config);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\AdapterInterface::setVisibility()
     *
     * @param mixed $path
     * @param mixed $visibility
     */
    public function setVisibility($path, $visibility)
    {
        return $this->getAdapter()->setVisibility($path, $visibility);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\ReadInterface::has()
     *
     * @param mixed $path
     */
    public function has($path)
    {
        return $this->getAdapter()->has($path);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\ReadInterface::read()
     *
     * @param mixed $path
     */
    public function read($path)
    {
        return $this->getAdapter()->read($path);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\ReadInterface::readStream()
     *
     * @param mixed $path
     */
    public function readStream($path)
    {
        return $this->getAdapter()->readStream($path);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\ReadInterface::listContents()
     *
     * @param mixed $directory
     * @param mixed $recursive
     */
    public function listContents($directory = '', $recursive = false)
    {
        return $this->getAdapter()->listContents($directory, $recursive);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\ReadInterface::getMetadata()
     *
     * @param mixed $path
     */
    public function getMetadata($path)
    {
        return $this->getAdapter()->getMetadata($path);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\ReadInterface::getSize()
     *
     * @param mixed $path
     */
    public function getSize($path)
    {
        return $this->getAdapter()->getSize($path);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\ReadInterface::getMimetype()
     *
     * @param mixed $path
     */
    public function getMimetype($path)
    {
        return $this->getAdapter()->getMimetype($path);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\ReadInterface::getTimestamp()
     *
     * @param mixed $path
     */
    public function getTimestamp($path)
    {
        return $this->getAdapter()->getTimestamp($path);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\ReadInterface::getVisibility()
     *
     * @param mixed $path
     */
    public function getVisibility($path)
    {
        return $this->getAdapter()->getVisibility($path);
    }

    /**
     * Return the adapter implementation
     *
     * @return AdapterInterface
     */
    abstract public function getAdapter();
}

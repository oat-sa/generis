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
 */

namespace oat\oatbox\filesystem\utils;

use League\Flysystem\Filesystem;
use League\Flysystem\Handler;
use League\Flysystem\PluginInterface;

/**
 * A trait to facilitate creation of filesystem wrappers
 *
 * @author Joel Bout
 */
trait FileSystemWrapperTrait
{
    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::has()
     *
     * @param mixed $path
     */
    public function has($path)
    {
        return $this->getFileSystem()->has($this->getFullPath($path));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::read()
     *
     * @param mixed $path
     */
    public function read($path)
    {
        return $this->getFileSystem()->read($this->getFullPath($path));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::readStream()
     *
     * @param mixed $path
     */
    public function readStream($path)
    {
        return $this->getFileSystem()->readStream($this->getFullPath($path));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::listContents()
     *
     * @param mixed $directory
     * @param mixed $recursive
     */
    public function listContents($directory = '', $recursive = false)
    {
        return $this->getFileSystem()->listContents($this->getFullPath($directory), $recursive);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::getMetadata()
     *
     * @param mixed $path
     */
    public function getMetadata($path)
    {
        return $this->getFileSystem()->getMetadata($this->getFullPath($path));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::getSize()
     *
     * @param mixed $path
     */
    public function getSize($path)
    {
        return $this->getFileSystem()->getSize($this->getFullPath($path));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::getMimetype()
     *
     * @param mixed $path
     */
    public function getMimetype($path)
    {
        return $this->getFileSystem()->getMimetype($this->getFullPath($path));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::getTimestamp()
     *
     * @param mixed $path
     */
    public function getTimestamp($path)
    {
        return $this->getFileSystem()->getTimestamp($this->getFullPath($path));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::getVisibility()
     *
     * @param mixed $path
     */
    public function getVisibility($path)
    {
        return $this->getFileSystem()->getVisibility($this->getFullPath($path));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::write()
     *
     * @param mixed $path
     * @param mixed $contents
     */
    public function write($path, $contents, array $config = [])
    {
        return $this->getFileSystem()->write($this->getFullPath($path), $contents, $config);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::writeStream()
     *
     * @param mixed $path
     * @param mixed $resource
     */
    public function writeStream($path, $resource, array $config = [])
    {
        return $this->getFileSystem()->writeStream($this->getFullPath($path), $resource, $config);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::update()
     *
     * @param mixed $path
     * @param mixed $contents
     */
    public function update($path, $contents, array $config = [])
    {
        return $this->getFileSystem()->update($this->getFullPath($path), $contents, $config);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::updateStream()
     *
     * @param mixed $path
     * @param mixed $resource
     */
    public function updateStream($path, $resource, array $config = [])
    {
        return $this->getFileSystem()->updateStream($this->getFullPath($path), $resource, $config);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::rename()
     *
     * @param mixed $path
     * @param mixed $newpath
     */
    public function rename($path, $newpath)
    {
        return $this->getFileSystem()->rename($this->getFullPath($path), $newpath);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::copy()
     *
     * @param mixed $path
     * @param mixed $newpath
     */
    public function copy($path, $newpath)
    {
        return $this->getFileSystem()->copy($this->getFullPath($path), $newpath);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::delete()
     *
     * @param mixed $path
     */
    public function delete($path)
    {
        return $this->getFileSystem()->delete($this->getFullPath($path));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::deleteDir()
     *
     * @param mixed $dirname
     */
    public function deleteDir($dirname)
    {
        return $this->getFileSystem()->deleteDir($this->getFullPath($dirname));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::createDir()
     *
     * @param mixed $dirname
     */
    public function createDir($dirname, array $config = [])
    {
        return $this->getFileSystem()->createDir($this->getFullPath($dirname), $config);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::setVisibility()
     *
     * @param mixed $path
     * @param mixed $visibility
     */
    public function setVisibility($path, $visibility)
    {
        return $this->getFileSystem()->setVisibility($this->getFullPath($path), $visibility);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::put()
     *
     * @param mixed $path
     * @param mixed $contents
     */
    public function put($path, $contents, array $config = [])
    {
        return $this->getFileSystem()->put($this->getFullPath($path), $contents, $config);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::putStream()
     *
     * @param mixed $path
     * @param mixed $resource
     */
    public function putStream($path, $resource, array $config = [])
    {
        return $this->getFileSystem()->putStream($this->getFullPath($path), $resource, $config);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::readAndDelete()
     *
     * @param mixed $path
     */
    public function readAndDelete($path)
    {
        return $this->getFileSystem()->readAndDelete($this->getFullPath($path));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::get()
     *
     * @param mixed $path
     */
    public function get($path, Handler $handler = null)
    {
        return $this->getFileSystem()->get($this->getFullPath($path), $handler);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \League\Flysystem\FilesystemInterface::addPlugin()
     */
    public function addPlugin(PluginInterface $plugin)
    {
        return $this->getFileSystem()->addPlugin($plugin);
    }

    /**
     * Return the underlying Filesystem
     *
     * @return Filesystem
     */
    abstract protected function getFileSystem();

    abstract protected function getFullPath($path);
}

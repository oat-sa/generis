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
     * @see \League\Flysystem\FilesystemInterface::has()
     */
    public function has($path) {
        return $this->getFileSystem()->has($path);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::read()
     */
    public function read($path) {
        return $this->getFileSystem()->read($path);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::readStream()
     */
    public function readStream($path) {
        return $this->getFileSystem()->readStream($path);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::listContents()
     */
    public function listContents($directory = '', $recursive = false) {
        return $this->getFileSystem()->listContents($directory, $recursive);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::getMetadata()
     */
    public function getMetadata($path) {
        return $this->getFileSystem()->getMetadata($path);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::getSize()
     */
    public function getSize($path) {
        return $this->getFileSystem()->getSize($path);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::getMimetype()
     */
    public function getMimetype($path) {
        return $this->getFileSystem()->getMimetype($path);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::getTimestamp()
     */
    public function getTimestamp($path) {
        return $this->getFileSystem()->getTimestamp($path);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::getVisibility()
     */
    public function getVisibility($path) {
        return $this->getFileSystem()->getVisibility($path);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::write()
     */
    public function write($path, $contents, array $config = []) {
        return $this->getFileSystem()->write($path, $contents, $config);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::writeStream()
     */
    public function writeStream($path, $resource, array $config = []) {
        return $this->getFileSystem()->writeStream($path, $resource, $config);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::update()
     */
    public function update($path, $contents, array $config = []) {
        return $this->getFileSystem()->update($path, $contents, $config);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::updateStream()
     */
    public function updateStream($path, $resource, array $config = []) {
        return $this->getFileSystem()->updateStream($path, $resource, $config);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::rename()
     */
    public function rename($path, $newpath) {
        return $this->getFileSystem()->rename($path, $newpath);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::copy()
     */
    public function copy($path, $newpath) {
        return $this->getFileSystem()->copy($path, $newpath);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::delete()
     */
    public function delete($path) {
        return $this->getFileSystem()->delete($path);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::deleteDir()
     */
    public function deleteDir($dirname) {
        return $this->getFileSystem()->deleteDir($dirname);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::createDir()
     */
    public function createDir($dirname, array $config = []) {
        return $this->getFileSystem()->createDir($dirname, $config);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::setVisibility()
     */
    public function setVisibility($path, $visibility) {
        return $this->getFileSystem()->setVisibility($path, $visibility);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::put()
     */
    public function put($path, $contents, array $config = []) {
        return $this->getFileSystem()->put($path, $contents, $config);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::putStream()
     */
    public function putStream($path, $resource, array $config = []) {
        return $this->getFileSystem()->putStream($path, $resource, $config);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::readAndDelete()
     */
    public function readAndDelete($path) {
        return $this->getFileSystem()->readAndDelete($path);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::get()
     */
    public function get($path, Handler $handler = null) {
        return $this->getFileSystem()->get($path, $handler);
    }


    /**
     * (non-PHPdoc)
     * @see \League\Flysystem\FilesystemInterface::addPlugin()
     */
    public function addPlugin(PluginInterface $plugin) {
        return $this->getFileSystem()->addPlugin($plugin);
    }

    /**
     * Return the underlying Filesystem
     *
     * @return Filesystem
     */
    abstract protected function getFileSystem();
}

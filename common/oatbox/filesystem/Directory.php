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

namespace oat\oatbox\filesystem;

use League\Flysystem\FileExistsException;

class Directory extends FileSystemHandler implements \IteratorAggregate
{
    const ITERATOR_RECURSIVE = '1';
    const ITERATOR_FILE      = '2';
    const ITERATOR_DIRECTORY = '4';

    /**
     * Get a subDirectory of $this (existing or not)
     *
     * @param $path
     * @return Directory
     */
    public function getDirectory($path)
    {
        $subDirectory = new self($this->getFileSystemId(), $this->getFullPath($path));
        $subDirectory->setServiceLocator($this->getServiceLocator());
        return $subDirectory;
    }

    /**
     * Get file located into $this->directory (existing or not)
     *
     * @param $path
     * @return File
     */
    public function getFile($path)
    {
        $file = new File($this->getFileSystemId(), $this->getFullPath($path));
        $file->setServiceLocator($this->getServiceLocator());
        return $file;
    }

    /**
     * Method constraints by IteratorAggregator, wrapper to getFlyIterator
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->getFlyIterator();
    }

    /**
     * Get an iterator of $this directory
     * Flags are combinable like that $this->getFlyIterator(self::ITERATOR_DIRECTORY|self::ITERATOR_DIRECTORY)
     * By default iterator is not recursive and includes directories & files
     *
     * @param null $flags
     * @return \ArrayIterator
     */
    public function getFlyIterator($flags=null)
    {
        if (is_null($flags)) {
            $flags = self::ITERATOR_DIRECTORY | self::ITERATOR_FILE;
        }

        $recursive = ($flags & self::ITERATOR_RECURSIVE);
        $withDirectories = ($flags & self::ITERATOR_DIRECTORY);
        $withFiles = ($flags & self::ITERATOR_FILE);

        $iterator = array();
        $contents = $this->getFileSystem()->listContents($this->getPrefix(), $recursive);

        foreach ($contents as $content) {
            if ($withDirectories && $content['type'] == 'dir') {
                $iterator[] = $this->getDirectory(str_replace($this->getPrefix(), '', $content['path']));
            }

            if ($withFiles && $content['type'] == 'file') {
                $iterator[] = $this->getFile(str_replace($this->getPrefix(), '', $content['path']));
            }
        }

        return new \ArrayIterator($iterator);
    }

    /**
     * Get relative path from $this directory to given content
     *
     * @param Directory|File $content
     * @return mixed
     * @throws \common_Exception
     * @throws \tao_models_classes_FileNotFoundException
     */
    public function getRelPath($content)
    {
        if (! $content instanceof File && ! $content instanceof Directory) {
            throw new \common_Exception('Content for ' . __FUNCTION__ . ' has to be a file or directory object. ' .
                is_object($content) ? get_class($content) : gettype($content) . ' given.');
        }

        return str_replace($this->getPrefix(), '', $content->getPrefix());
    }

    /**
     * Check if current directory exists
     *
     * @return bool
     */
    public function exists()
    {
        return $this->getFileSystem()->has($this->getPrefix());
    }

    /**
     * Delete the current directory
     *
     * @return bool
     */
    public function deleteSelf()
    {
        return $this->getFileSystem()->deleteDir($this->getPrefix());
    }

    /**
     * Return a sanitized full path from main directory
     *
     * @param $path
     * @return string
     */
    protected function getFullPath($path)
    {
        return $this->getPrefix() . '/' . $this->sanitizePath($path);
    }

    /**
     * Rename
     *
     * Rename directory into $path
     *
     * @param $path
     * @return bool
     * @throws \common_exception_FileSystemError
     */
    public function rename($path)
    {
        // This implementation supersedes the Flysystem's one. Indeed, while using connectors
        // such as the Amazon S3 (v3) connector, rename on directories does not work. A custom
        // implementation is then needed.
        $contents = $this->getFileSystem()->listContents($this->getPrefix(), true);

        // Filter files only.
        $filePaths = [];
        foreach ($contents as $content) {
            if ($content['type'] === 'file') {
                $filePaths[]= [
                    'source' => $content['path'],
                    'destination' => str_replace($this->getPrefix(), $path, $content['path'])];
            }
        }

        foreach ($filePaths as $renaming) {
            try {
                if ($this->getFileSystem()->rename($renaming['source'], $renaming['destination']) === false) {
                    throw new \common_exception_FileSystemError("Unable to rename '" . $this->getPrefix() . "' into '${path}'.");
                }
            } catch (FileExistsException $e) {
                throw new \common_exception_FileSystemError("Unable to rename '" . $this->getPrefix() . "' into '${path}'. File already exists.");
            }
        }

        if (!$this->deleteSelf()) {
            throw new \common_exception_FileSystemError("Could not finalize renaming of '" . $this->getPrefix() . "' into '${path}'.");
        }

        return true;
    }
}
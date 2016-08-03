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

class Directory implements \IteratorAggregate
{
    const ITERATOR_RECURSIVE = '1';
    const ITERATOR_FILE      = '2';
    const ITERATOR_DIRECTORY = '4';

    /**
     * @var FileSystem
     */
    protected $fileSystem;

    /**
     * Relative prefix into $this->filesystem
     *
     * @var string
     */
    protected $prefix;

    /**
     * Directory constructor.
     *
     * @param $fileSystem
     * @param $prefix
     */
    public function __construct($fileSystem, $prefix)
    {
        $this->fileSystem = $fileSystem;
        $this->prefix = $this->sanitizePath($prefix);
    }

    /**
     * Get a subDirectory of $this (existing or not)
     *
     * @param $path
     * @return Directory
     */
    public function getDirectory($path)
    {
        return new self($this->getFileSystem(), $this->getFullPath($path));
    }

    /**
     * Get file located into $this->directory (existing or not)
     *
     * @param $path
     * @return File
     */
    public function getFile($path)
    {
        return new File($this->getFileSystem(), $this->getFullPath($path));
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
     * Get the current prefix
     *
     * @return mixed|string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Get the current flysystem, should be not public
     *
     * @return FileSystem
     */
    public function getFileSystem()
    {
        return $this->fileSystem;
    }

    /**
     * Remove the current directory
     *
     * @return bool
     * @throws \common_Exception
     */
    public function selfRemove()
    {
        if (! $this->exists()) {
            throw new \common_Exception('Unable to find dir to delete: "' . $this->getPrefix() . '"');
        }
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
     * Sanitize path:
     *  - by replace \ to / for windows compatibility (only on local)
     *  - trim .
     *  - trim / or \\
     *
     * @param $path
     * @return string
     */
    protected function sanitizePath($path)
    {
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);

        $path = preg_replace('/'.preg_quote('./', '/').'/', '', $path, 1);
        $path = trim($path, '/');

        return $path;
    }

}
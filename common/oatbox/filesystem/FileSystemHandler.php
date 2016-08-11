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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\oatbox\filesystem;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

abstract class FileSystemHandler implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var mixed
     */
    protected $fileSystemId;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var  FileSystem
     */
    protected $fileSystem;

    /**
     * FileSystemHandler constructor.
     *
     * @param $id
     * @param $prefix
     */
    public function __construct($id, $prefix)
    {
        $this->fileSystemId = $id;
        $this->prefix = $this->sanitizePath($prefix);
    }

    /**
     * Get id
     *
     * @return mixed
     */
    public function getFileSystemId()
    {
        return $this->fileSystemId;
    }

    /**
     * Return the prefix e.q. key of file system
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Get current base directory
     *
     * @return FileSystem
     */
    protected function getBaseDirectory()
    {
        if (! $this->fileSystem) {
            $this->fileSystem = $this->getServiceLocator()
                ->get(FileSystemService::SERVICE_ID)
                ->getDirectory($this->getFileSystemId());
        }

        return $this->fileSystem;
    }

    /**
     * Get current fileystem
     *
     * @return FileSystem
     */
    public function getFileSystem()
    {
        if (! $this->fileSystem) {
            $this->fileSystem = $this->getServiceLocator()
                ->get(FileSystemService::SERVICE_ID)
                ->getFileSystem($this->getFileSystemId());
        }

        return $this->fileSystem;
    }

    /**
     * Sanitize path:
     *  - by replace \ to / for windows compatibility
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
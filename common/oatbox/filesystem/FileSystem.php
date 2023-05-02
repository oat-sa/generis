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

namespace oat\oatbox\filesystem;

use common_Exception;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FilesystemInterface;
use oat\oatbox\filesystem\utils\FileSystemWrapperTrait;

/**
 * Class Filesystem
 */
class FileSystem implements FilesystemInterface
{
    use FileSystemWrapperTrait;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * Filesystem constructor.
     *
     * @param $id
     * @param $adapter
     * @param mixed $prefix
     */
    public function __construct($id, FilesystemInterface $flySystem, $prefix)
    {
        $this->id = $id;
        $this->filesystem = $flySystem;
        $this->prefix = $prefix;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @throws common_Exception
     *
     * @return FilesystemInterface
     */
    protected function getFileSystem()
    {
        return $this->filesystem;
    }

    /**
     * Get the Adapter.
     *
     * @return AdapterInterface adapter
     */
    private function getAdapter()
    {
        return $this->getFileSystem()->getAdapter();
    }

    protected function getFullPath($path)
    {
        return $this->prefix . '/' . $path;
    }
}

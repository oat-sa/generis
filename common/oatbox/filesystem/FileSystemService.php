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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\oatbox\filesystem;

use oat\oatbox\service\ConfigurableService;
use League\Flysystem\AdapterInterface;
use common_exception_Error;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use \League\Flysystem\Filesystem as FlyFileSystem;
use League\Flysystem\FilesystemInterface;

 /**
 * A service to reference and retrieve filesystems
 */
class FileSystemService extends ConfigurableService
{
    const SERVICE_ID = 'generis/filesystem';

    const OPTION_FILE_PATH = 'filesPath';

    const OPTION_ADAPTERS = 'adapters';

    const OPTION_DIRECTORIES = 'dirs';

    const FLYSYSTEM_ADAPTER_NS = '\\League\\Flysystem\\Adapter\\';

    const FLYSYSTEM_LOCAL_ADAPTER = 'Local';

    private $filesystems = [];

    /**
     *
     * @param $id
     * @return \oat\oatbox\filesystem\Directory
     */
    public function getDirectory($id)
    {
        return $this->propagate(new Directory($id, ''));
    }

    /**
     * Returns the directory config
     * @return array
     */
    protected function getDirectories()
    {
        return $this->hasOption(self::OPTION_DIRECTORIES)
            ? $this->getOption(self::OPTION_DIRECTORIES)
            : [];
    }

    /**
     * Add a directory reference
     * @param string $id
     * @param string $adapterId
     */
    protected function addDir($id, $adapterId)
    {
        $dirs = $this->getDirectories();
        $dirs[$id] = $adapterId;
        $this->setOption(self::OPTION_DIRECTORIES, $dirs);
    }
    
    /**
     * Returns whenever or not a FS exists
     * @param string $id
     * @return boolean
     */
    public function hasDirectory($id)
    {
        $adapterConfig = $this->getOption(self::OPTION_ADAPTERS);
        $dirConfig = $this->getOption(self::OPTION_DIRECTORIES);
        return isset($adapterConfig[$id]) || isset($dirConfig[$id]);
    }

    /**
     * Get FileSystem by ID
     *
     * Retrieve an existing FileSystem by ID.
     *
     * @param string $id
     * @return FilesystemInterface
     * @throws \common_exception_Error
     * @throws \common_exception_NotFound
     */
    public function getFileSystem($id)
    {
        if (!isset($this->filesystems[$id])) {
            $config = $this->getAdapterConfig($id);
            $adapter = $this->getFlysystemAdapter($config['adapter']);
            $this->filesystems[$id] = new FileSystem($id, new FlyFileSystem($adapter), $config['path']);
        }
        return $this->filesystems[$id];
    }
    
    /**
     * Creates a filesystem using the default implementation (Local)
     * Override this function to create your files elsewhere by default
     *
     * @param string $id
     * @param string $subPath
     * @return FilesystemInterface
     */
    public function createFileSystem($id, $subPath = null)
    {
        $this->addDir($id, 'default');
        return $this->getFileSystem($id);
    }

    /**
     * Create a new local file system
     *
     * @deprecated never rely on a directory being local, use addDir instead
     * @param string $id
     * @return FilesystemInterface
     */
    public function createLocalFileSystem($id)
    {
        $path = $this->getOption(self::OPTION_FILE_PATH) . \helpers_File::sanitizeInjectively($id);
        $this->registerLocalFileSystem($id, $path);
        return $this->getFileSystem($id);
    }
    
    /**
     * Registers a local file system, used for transition
     *
     * @deprecated never rely on a directory being local, use addDir instead
     * @param string $id
     * @param string $path
     * @return boolean
     */
    public function registerLocalFileSystem($id, $path)
    {
        $adapters = $this->hasOption(self::OPTION_ADAPTERS) ? $this->getOption(self::OPTION_ADAPTERS) : [];
        $adapters[$id] = [
            'class' => self::FLYSYSTEM_LOCAL_ADAPTER,
            'options' => ['root' => $path]
        ];
        $this->setOption(self::OPTION_ADAPTERS, $adapters);
        return true;
    }

    /**
     * Remove a filesystem adapter
     *
     * @param string $id
     * @return boolean
     */
    public function unregisterFileSystem($id)
    {
        if (isset($this->filesystems[$id])) {
            unset($this->filesystems[$id]);
        }
        $adapters = $this->getOption(self::OPTION_ADAPTERS);
        if (isset($adapters[$id])) {
            unset($adapters[$id]);
            $this->setOption(self::OPTION_ADAPTERS, $adapters);
            return true;
        } elseif ($this->hasDirectory($id)) {
            $directories = $this->getOption(self::OPTION_DIRECTORIES);
            unset($directories[$id]);
            $this->setOption(self::OPTION_DIRECTORIES, $directories);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns the configuration for an adapter
     * @param string $id
     * @return string[]
     */
    protected function getAdapterConfig($id)
    {
        $dirs = $this->getDirectories();
        if (!isset($dirs[$id])) {
            $config = [
                'adapter' => $id,
                'path' => ''
            ];
        } elseif (is_array($dirs[$id])) {
            $config = $dirs[$id];
        } else {
            $config = [
                'adapter' => $dirs[$id],
                'path' => $id
            ];
        }
        return $config;
    }

    /**
     * inspired by burzum/storage-factory
     *
     * @param string $id
     * @throws \common_exception_NotFound if adapter doesn't exist
     * @throws \common_exception_Error if adapter is not valid
     * @return AdapterInterface
     */
    protected function getFlysystemAdapter($id)
    {
        $fsConfig = $this->getOption(self::OPTION_ADAPTERS);
        if (!isset($fsConfig[$id])) {
            throw new \common_exception_NotFound('Undefined filesystem "' . $id . '"');
        }
        $adapterConfig = $fsConfig[$id];
        // alias?
        while (is_string($adapterConfig)) {
            $adapterConfig = $fsConfig[$adapterConfig];
        }
        $class = $adapterConfig['class'];
        $options = isset($adapterConfig['options']) ? $adapterConfig['options'] : [];

        if (!class_exists($class)) {
            if (class_exists(self::FLYSYSTEM_ADAPTER_NS . $class)) {
                $class = self::FLYSYSTEM_ADAPTER_NS . $class;
            } elseif (class_exists(self::FLYSYSTEM_ADAPTER_NS . $class . '\\' . $class . 'Adapter')) {
                $class = self::FLYSYSTEM_ADAPTER_NS . $class . '\\' . $class . 'Adapter';
            } else {
                throw new common_exception_Error('Unknown Flysystem adapter "' . $class . '"');
            }
        }

        if (!is_subclass_of($class, 'League\Flysystem\AdapterInterface')) {
            throw new common_exception_Error('"' . $class . '" is not a flysystem adapter');
        }
        $adapter = (new \ReflectionClass($class))->newInstanceArgs($options);
        if ($adapter instanceof ServiceLocatorAwareInterface) {
            $adapter->setServiceLocator($this->getServiceLocator());
        }
        return $adapter;
    }
}

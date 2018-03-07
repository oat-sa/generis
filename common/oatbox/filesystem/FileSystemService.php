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
 /**
 * A service to reference and retrieve filesystems
 */
class FileSystemService extends ConfigurableService
{
    const SERVICE_ID = 'generis/filesystem';
    
    const OPTION_FILE_PATH = 'filesPath';
    
    const OPTION_ADAPTERS = 'adapters';
    
    const FLYSYSTEM_ADAPTER_NS = '\\League\\Flysystem\\Adapter\\';
    
    const FLYSYSTEM_LOCAL_ADAPTER = 'Local';
    
    private $filesystems = array();
    
    /**
     * 
     * @param $id
     * @return \oat\oatbox\filesystem\Directory
     */
    public function getDirectory($id)
    {
        $directory = new Directory($id, '');
        $directory->setServiceLocator($this->getServiceLocator());
        return $directory;
    }
    
    /**
     * Returns whenever or not a FS exists
     * @param string $id
     * @return boolean
     */
    public function hasDirectory($id)
    {
        $fsConfig = $this->getOption(self::OPTION_ADAPTERS);
        return isset($fsConfig[$id]);
    }

    /**
     * Get FileSystem by ID
     *
     * Retrieve an existing FileSystem by ID.
     *
     * @param string $id
     * @return FileSystem
     * @throws \common_exception_Error
     * @throws \common_exception_NotFound
     */
    public function getFileSystem($id)
    {
        if (!isset($this->filesystems[$id])) {
            $this->filesystems[$id] = new FileSystem($id, $this->getFlysystemAdapter($id));
        }
        return $this->filesystems[$id];
    }
    
    /**
     * Creates a filesystem using the default implementation (Local)
     * Override this function to create your files elsewhere by default
     *
     * @param string $id
     * @param string $subPath
     * @return FileSystem
     */
    public function createFileSystem($id, $subPath = null)
    {
        $path = $this->getOption(self::OPTION_FILE_PATH).
            (is_null($subPath) ? \helpers_File::sanitizeInjectively($id) : ltrim($subPath, '/'));
        $adapters = $this->hasOption(self::OPTION_ADAPTERS) ? $this->getOption(self::OPTION_ADAPTERS) : array();
        $adapters[$id] = array(
            'class' => self::FLYSYSTEM_LOCAL_ADAPTER,
            'options' => array('root' => $path)
        );
        $this->setOption(self::OPTION_ADAPTERS, $adapters);
        return $this->getFileSystem($id);
    }

    /**
     * Create a new local file system
     * 
     * @param string $id
     * @return FileSystem
     */
    public function createLocalFileSystem($id)
    {
        $path = $this->getOption(self::OPTION_FILE_PATH).\helpers_File::sanitizeInjectively($id);
        $this->registerLocalFileSystem($id, $path);
        return $this->getFileSystem($id);
    }
    
    /**
     * Registers a local file system, used for transition
     * 
     * @param string $id
     * @param string $path
     * @return boolean
     */
    public function registerLocalFileSystem($id, $path)
    {
        $adapters = $this->hasOption(self::OPTION_ADAPTERS) ? $this->getOption(self::OPTION_ADAPTERS) : array();
        $adapters[$id] = array(
            'class' => self::FLYSYSTEM_LOCAL_ADAPTER,
            'options' => array('root' => $path)
        );
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
        $adapters = $this->getOption(self::OPTION_ADAPTERS);
        if (isset($adapters[$id])) {
            unset($adapters[$id]);
            if (isset($this->filesystems[$id])) {
                unset($this->filesystems[$id]);
            }
            $this->setOption(self::OPTION_ADAPTERS, $adapters);
            return true;
        } else {
            return false;
        }
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
            throw new \common_exception_NotFound('Undefined filesystem "'.$id.'"');
        }
        $adapterConfig = $fsConfig[$id];
        // alias?
        while (is_string($adapterConfig)) {
            $adapterConfig = $fsConfig[$adapterConfig];
        }
        $class = $adapterConfig['class'];
        $options = isset($adapterConfig['options']) ? $adapterConfig['options'] : array();
        
        if (!class_exists($class)) {
            if (class_exists(self::FLYSYSTEM_ADAPTER_NS.$class)) {
                $class = self::FLYSYSTEM_ADAPTER_NS.$class;
            } elseif (class_exists(self::FLYSYSTEM_ADAPTER_NS.$class.'\\'.$class.'Adapter')) {
                $class = self::FLYSYSTEM_ADAPTER_NS.$class.'\\'.$class.'Adapter';
            } else {
                throw new common_exception_Error('Unknown Flysystem adapter "'.$class.'"');
            }
        }
        
        if (!is_subclass_of($class, 'League\Flysystem\AdapterInterface')) {
            throw new common_exception_Error('"'.$class.'" is not a flysystem adapter');
        }
        $adapter = (new \ReflectionClass($class))->newInstanceArgs($options);
        if ($adapter instanceof ServiceLocatorAwareInterface) {
            $adapter->setServiceLocator($this->getServiceLocator());
        }
        return $adapter;
    }
}

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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\oatbox\service;

use oat\oatbox\Configurable;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * The simple placeholder ServiceManager
 * @author Joel Bout <joel@taotesting.com>
 */
class ServiceManager implements ServiceLocatorInterface
{
    private static $instance;

    /**
     * @deprecated Use the chain of ServiceLocatorAware/ServiceManagerAware instead
     *
     * @return mixed
     * @throws \common_exception_NotAcceptable
     */
    public static function getServiceManager()
    {
        if (is_null(self::$instance)) {
            throw new \common_exception_NotAcceptable('Service Manager should be already instantiate by Bootstrap');
        }
        return self::$instance;
    }

    /**
     * @deprecated Use the chain of ServiceLocatorAware/ServiceManagerAware instead
     *
     * @param ServiceManager $serviceManager
     */
    public static function setServiceManager(ServiceManager $serviceManager)
    {
        self::$instance = $serviceManager;
    }

    /**
     * @var array List of service already loaded
     */
    private $services = array();
    
    /**
     * @var \common_persistence_KeyValuePersistence The persistence where configurations are stored
     */
    private $configService;

    /**
     * ServiceManager constructor.
     *
     * @param \common_persistence_KeyValuePersistence $configService
     */
    public function __construct(\common_persistence_KeyValuePersistence $configService)
    {
        $this->configService = $configService;
    }

    /**
     * Returns the service configured for the serviceKey
     * or throws a ServiceNotFoundException
     *
     * @param string $serviceKey
     * @return mixed
     */
    public function get($serviceKey)
    {
        if (! isset($this->services[$serviceKey])) {
            $service = $this->getConfig()->get($serviceKey);
            if ($service === false) {
                throw new ServiceNotFoundException($serviceKey);
            }
            $this->propagate($service);
            $this->services[$serviceKey] = $service;
        }
        return $this->services[$serviceKey];
    }

    /**
     * Get a service associated to the given key into configuration service
     * Key has to be composed of two parts
     *
     * @param array|string $serviceKey
     * @return bool
     */
    public function has($serviceKey)
    {
        if (isset($this->services[$serviceKey])) {
            return true;
        }
        $parts = explode('/', $serviceKey, 2);
        if (count($parts) < 2) {
            return false;
        }
        return $this->getConfig()->exists($serviceKey);
    }

    /**
     * Registers a service, overwritting a potentially already
     * existing service.
     * 
     * @param string $serviceKey
     * @param ConfigurableService $service
     * @throws \common_Exception
     */
    public function register($serviceKey, ConfigurableService $service)
    {
        $parts = explode('/', $serviceKey, 2);
        if (count($parts) < 2) {
            throw new \common_Exception('Invalid servicekey '.$serviceKey);
        }
        $this->services[$serviceKey] = $this->propagate($service);
        $success = $this->getConfig()->set($serviceKey, $service);
        if (! $success) {
            throw new \common_exception_Error('Unable to write ' . $serviceKey);
        }
    }

    /**
     * Unregister a config by deleting it through config service
     *
     * @param $serviceKey
     * @return mixed
     */
    public function unregister($serviceKey)
    {
        unset($this->services[$serviceKey]);
        return $this->getConfig()->del($serviceKey);
    }

    /**
     * Get the configuration service
     *
     * @return \common_persistence_KeyValuePersistence
     */
    protected function getConfig()
    {
        return $this->configService;
    }
    
    /**
     * Propagate service dependencies
     *
     * @param  $service
     * @return mixed
     */
    public function propagate($service)
    {
        if(is_object($service) &&  ($service instanceof ServiceManagerAwareInterface)){
            $service->setServiceLocator($this);
        } elseif(is_object($service) &&  ($service instanceof ServiceLocatorAwareInterface)){
            $service->setServiceLocator($this);
        }

        return $service;
    }

    /**
     * Service or sub-service factory
     *
     * @param $className
     * @param array $options
     * @return mixed
     */
    public function build($className , array $options = [] )
    {
        if (is_a($className, Configurable::class, true)) {
            $service = new $className($options);
            $this->propagate($service);
            return $service;
        }

        throw new ServiceNotFoundException($className);
    }
}

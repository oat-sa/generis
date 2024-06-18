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
 * Copyright (c) 2015-2021 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\oatbox\service;

use oat\generis\model\DependencyInjection\ContainerBuilder;
use oat\generis\model\DependencyInjection\ContainerStarter;
use oat\oatbox\Configurable;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Psr\Container\ContainerInterface;

/**
 * The simple placeholder ServiceManager
 * @author Joel Bout <joel@taotesting.com>
 */
class ServiceManager implements ServiceLocatorInterface, ContainerInterface
{
    private static $instance;

    /** @var ContainerStarter */
    private $containerStarter;

    /**
     * @return \oat\oatbox\service\ServiceManager
     * @deprecated Pass service locator instead of relying on static function
     */
    public static function getServiceManager()
    {
        if (is_null(self::$instance)) {
            self::$instance = new ServiceManager(\common_ext_ConfigDriver::singleton());
        }
        return self::$instance;
    }

    public static function setServiceManager(ServiceManager $serviceManager)
    {
        self::$instance = $serviceManager;
    }

    private $services = [];

    /**
     * @var \common_persistence_KeyValuePersistence
     */
    private $configService;

    public function __construct($configService)
    {
        $this->configService = $configService;
    }

    /**
     * Returns the service configured for the serviceKey
     * or throws a ServiceNotFoundException
     *
     * @param string $serviceKey
     * @return ConfigurableService
     * @throws ServiceNotFoundException
     * @see ContainerInterface::get()
     * @deprecated Use $this->getContainer()->get()
     */
    public function get($serviceKey)
    {
        $serviceId = $this->getServiceId($serviceKey);
        if (!isset($this->services[$serviceId])) {
            $this->services[$serviceId] = $this->load($serviceId, $serviceKey);
        }
        return $this->services[$serviceId];
    }

    /**
     * Extract the service id from the provided parameter
     * @param string $serviceKey
     * @return string
     */
    private function getServiceId($serviceKey)
    {
        return ((interface_exists($serviceKey) || class_exists($serviceKey)) && defined($serviceKey . '::SERVICE_ID'))
            ? $serviceKey::SERVICE_ID
            : (string)$serviceKey
        ;
    }

    /**
     * Loads the service referenced by id
     *
     * @param string $serviceId
     * @param string $serviceKey
     * @throws ServiceNotFoundException
     * @return ConfigurableService
     */
    private function load($serviceId, $serviceKey)
    {
        $service = $this->getConfig()->get($serviceId);
        if ($service === false) {
            $service = $this->tryAutowiring($serviceId, $serviceKey);
        }

        if ($service instanceof ServiceFactoryInterface) {
            $service = $service($this);
        }

        if (is_object($service) && ($service instanceof ServiceLocatorAwareInterface)) {
            $service->setServiceLocator($this);
        }
        return $service;
    }

    /**
     * Try to initialize the class without parameters
     *
     * @param string $serviceId
     * @param string $serviceKey
     * @throws ServiceNotFoundException
     * @return ConfigurableService
     */
    private function tryAutowiring($serviceId, $serviceKey)
    {
        if (!class_exists($serviceKey) || !is_subclass_of($serviceKey, ConfigurableService::class)) {
            throw new ServiceNotFoundException($serviceId);
        }
        return new $serviceKey();
    }

    /**
     * (non-PHPdoc)
     * @see ContainerInterface::has()
     * @deprecated Use $this->getContainer()->has()
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
     *3
     * @param string $serviceKey
     * @param ConfigurableService $service
     * @throws \common_Exception
     * @deprecated New services must be registered using Dependency Injection Container
     */
    public function register($serviceKey, ConfigurableService $service)
    {
        $parts = explode('/', $serviceKey, 2);
        if (count($parts) < 2) {
            throw new \common_Exception('Invalid servicekey ' . $serviceKey);
        }
        $this->propagate($service);
        $this->services[$serviceKey] = $service;
        $success = $this->getConfig()->set($serviceKey, $service);
        if (!$success) {
            throw new \common_exception_Error('Unable to write ' . $serviceKey);
        }
    }

    /**
     * @deprecated New services must be registered using Dependency Injection Container
     */
    public function unregister($serviceKey)
    {
        unset($this->services[$serviceKey]);
        return $this->getConfig()->del($serviceKey);
    }

    /**
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
     *
     * @deprecated - If class uses ServiceManagerAwareTrait use $this->propagate($service)
     * @deprecated New services must be registered using Dependency Injection Container
     */
    public function propagate($service)
    {
        if (is_object($service) &&  ($service instanceof ServiceLocatorAwareInterface)) {
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
     * @deprecated New services must be registered using Dependency Injection Container
     */
    public function build($className, array $options = [])
    {
        if (is_a($className, Configurable::class, true)) {
            $service = new $className($options);
            $this->propagate($service);
            return $service;
        }

        throw new ServiceNotFoundException($className);
    }

    /**
     * Prevents accidental serialisation of the services
     * @return array
     */
    public function __sleep()
    {
        return [];
    }

    /**
     * Dynamically overload a service without persisting it
     *
     * @param $serviceKey
     * @param ConfigurableService $service
     * @deprecated New services must be registered using Dependency Injection Container
     */
    public function overload($serviceKey, ConfigurableService $service)
    {
        $this->services[$serviceKey] = $service;
    }

    /**
     * @TODO Container will be removed from here as soon as we do not need ServiceManager anymore.
     */
    public function getContainer(): ContainerInterface
    {
        return $this->getContainerStarter()->getContainer();
    }

    public function rebuildContainer(): void
    {
        // if container was already built on same request, it has to be restarted before rebuild
        // to avoid fatal error of compiled container modification
        $this->containerStarter = null;
        $this->getContainerStarter()->getContainerBuilder()->forceBuild();
        // after container rebuild this cached starter needs a reset, to avoid using container already cached in memory
        $this->containerStarter = null;
    }

    /**
     * @TODO ContainerBuilder will be removed from here as soon as we do not need ServiceManager anymore.
     */
    public function getContainerBuilder(): ContainerBuilder
    {
        return $this->getContainerStarter()->getContainerBuilder();
    }

    /**
     * @TODO ContainerStarter will be removed from here as soon as we do not need ServiceManager anymore.
     */
    private function getContainerStarter(): ContainerStarter
    {
        if (!$this->containerStarter) {
            $this->containerStarter = new ContainerStarter($this);
        }

        return $this->containerStarter;
    }
}

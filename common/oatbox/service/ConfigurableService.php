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
use oat\oatbox\service\exception\InvalidService;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Configurable base service
 *
 * inspired by Solarium\Core\Configurable by Bas de Nooijer
 * https://github.com/basdenooijer/solarium/blob/master/library/Solarium/Core/Configurable.php
 *
 * @author Joel Bout <joel@taotesting.com>
 */
abstract class ConfigurableService extends Configurable implements ServiceLocatorAwareInterface
{

    private $subServices = [];

    use ServiceLocatorAwareTrait;

    public function setServiceManager(ServiceManager $serviceManager)
    {
        return $this->setServiceLocator($serviceManager);
    }

    /**
     *
     * @return \oat\oatbox\service\ServiceManager
     */
    public function getServiceManager()
    {
        return $this->getServiceLocator();
    }

    /**
     * Get a subservice from the current service
     *
     * @param $id
     * @param string $interface
     * @throws ServiceNotFoundException
     * @return object
     */
    public function getSubService($id, $interface = null)
    {
        if (! isset($this->subServices[$id])) {
            if ($this->hasOption($id)) {
                $service = $this->buildService($this->getOption($id), $interface);
                if ($service) {
                    $this->subServices[$id] = $service;
                } else {
                    throw new ServiceNotFoundException($id);
                }
            } else {
                throw new ServiceNotFoundException($id);
            }
        }
        return $this->subServices[$id];
    }

    /**
     * @param $serviceDefinition
     * @param string $interfaceName
     *
     * @return ConfigurableService
     * @throws InvalidService
     */
    protected function buildService($serviceDefinition, $interfaceName = null)
    {
        if ($serviceDefinition instanceof ConfigurableService) {
            if (is_null($interfaceName) || is_a($serviceDefinition, $interfaceName)) {
                $this->getServiceManager()->propagate($serviceDefinition);
                return $serviceDefinition;
            } else {
                throw new InvalidService('Service must implements ' . $interfaceName);
            }
        } elseif (is_array($serviceDefinition) && isset($serviceDefinition['class'])) {
            $classname = $serviceDefinition['class'];
            $options = isset($serviceDefinition['options']) ? $serviceDefinition['options'] : [];
            if (is_null($interfaceName) || is_a($classname, $interfaceName, true)) {
                return $this->getServiceManager()->build($classname, $options);
            } else {
                throw new InvalidService('Service must implements ' . $interfaceName);
            }
        } else {
            throw new InvalidService();
        }
    }
}
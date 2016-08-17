<?php

/*
 * This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; under version 2
 *  of the License (non-upgradable).
 *  
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 *  Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\oatbox\service;

use Exception;
use Interop\Container\ContainerInterface;
use oat\oatbox\service\exception\NotFoundException;

/**
 * adapter agregator for  ContainerInterface
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class ServiceInjector extends ConfigurableService implements ContainerInterface
{
    
    protected $services;

    /**
     * configurable service 
     * @param array $options 
     * @param string $dependenciesFactory specific dependencies injection service factory className
     * @param array $dependencies dependencies injection service configuration
     */
    public function __construct(array $options) {
        parent::__construct($options);
        $this->setServices();
        
    }
    /**
     * self factory
     * @return ServiceInjector
     */
    public static function factory(array $config = []) {
        $extensions =  common_ext_ExtensionsManager::singleton()->getEnabledExtensions();

        /* @var $ext \common_ext_Extension */
        foreach ($extensions as $ext) {
            if($ext->hasConfig('dependencies')) {
                $config = array_merge_recursive($config , $ext->getConfig('dependencies'));
            }
        }

        return $serviceManager = new self($config);
    }

        /**
     * configure each service manager
     * use each factory
     * @return $this
     */
    protected function setServices() {
        
        $selfServiceManager = $this->getServiceManager();
        
        if(is_a($selfServiceManager , ContainerInterface::class)) {
            $this->services[] = $selfServiceManager;
        }
        $options = $this->getOptions();

        foreach ($options as $Class => $config) {
            
            $factory                   = new $Class();
            $this->services[]          = $factory($config);
            
        }
        return $this;
        
    }
    
    /**
     * propagate service manager
     * @param type $service
     * @return mixed
     */
    protected function propagation($service) {
        if(is_object($service) && 
                is_a($service, ServiceManagerAwareInterface::class)){
            $service->setServiceLocator($this);
        }
        return $service;
    }

    /**
     * try each service manager with service $name
     * @see ContainerInterface::get
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function get($name) {
        $message = '';
        /**
         * services breakable chain of responsability
         */
        foreach($this->services as $serviceManager) {
            try {
                $service = $serviceManager->get($name);
                /**
                 * if service manager return null or false
                 */
                if(!empty($service)) {
                    return $this->propagation($service);
                }
            } catch (Exception $ex) {
                /**
                 * if service manager throw an exception
                 */
                $message = $ex->getMessage();
            }
        }
        /**
         * because each catched exception must be thrown
         */
         throw new NotFoundException($name , $message);

    }
    /**
     * @see ContainerInterface::has
     * @param string $name
     * @return boolean
     */
    public function has($name) {
        foreach($this->services as $serviceManager) {
            if($serviceManager->has($name)) {
                return true;
            }
        }
        return false;
    }
}

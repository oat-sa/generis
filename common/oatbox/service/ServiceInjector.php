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

/**
 * Description of ConfigurablePackage
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class ServiceInjector extends ConfigurableService implements ContainerInterface
{
    
    protected $services;
    
    protected $options;

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
     * use each factory
     * @return $this
     */
    protected function setServices() {
        $this->services[] = $this->getServiceManager();
        
        foreach ($this->options as $Class => $config) {
            
            $factory                   = new $Class();
            $this->services[]          = $factory($config);
            
        }
        
    }
    
    /**
     * 
     * @param type $service
     * @return type
     */
    public function propagation($service) {
        if(is_object($service) && is_a($service, 'oat\\oatbox\\service\\ServiceManagerAwareInterface')) {
            $service->setServiceLocator($this);
        }
    }

     /**
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    public function get($name) {
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
                 * if serviceManager throw an excepion
                 */
               $service =  false;
            }
        }
        /**
         * because each catched exception must be thrown
         */
        if(isset($ex)) {
            throw $ex;
        }
        return false;
    }
    /**
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

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

/**
 * Description of ConfigurablePackage
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class ConfigurablePackage extends ConfigurableService 
{
    
    protected $factoryNamespace = __NAMESPACE__ . '\\factory\\';

    protected $dependencies;

    /**
     * configurable service 
     * @param array $options 
     * @param string $dependenciesFactory specific dependencies injection service factory className
     * @param array $dependencies dependencies injection service configuration
     */
    public function __construct(array $options = [] , $dependenciesFactory = '' ,array $dependencies = []) {
        $this->setDependencies($dependenciesFactory , $dependencies);
        parent::__construct($options);
    }
    
    /**
     * create your specific dependencies injection service 
     * @param string $dependenciesFactory specific dependencies injection service factory className
     * @param array $dependencies dependencies injection service configuration
     */
    public function setDependencies($dependenciesFactory , array $dependencies) {
        $namespace = $this->factoryNamespace;
        if(!empty($dependenciesFactory))  {
            $factory = new $namespace . $dependenciesFactory;
            $this->dependencies =  $factory($this , $dependencies);
        }
        return $this;
    }
    
    /**
     * return specific dependencies injection service
     * @return mixed
     */
    public function getDependencies() {
        return $this->dependencies;
    }
    
}

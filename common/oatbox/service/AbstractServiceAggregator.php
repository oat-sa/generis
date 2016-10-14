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

use oat\oatbox\service\exception\InvalidService;
use oat\oatbox\service\exception\UnknownServiceException;

/**
 * 
 * 
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
abstract class AbstractServiceAggregator extends ConfigurableService {
    
    /**
     * store of subservices instances
     * @var array
     */
    protected $subServices = [];
    
    /**
     * interface subservices must implement
     * @var string 
     */
    protected $subServiceInterface = '';
    
    /**
     * return a configured instance of CreateOrReuseInterface 
     * @param string $id
     * @return object
     */
    public function getSubService($id) {
        if(array_key_exists($id, $this->subServices)) {
            return $this->subServices[$id];
        }
        return $this->createSubService($id);
    }
    
    /**
     * service factory
     * @param string $id
     * @return object
     */
    protected function createSubService($id) {
        if($this->hasOption($id)) {
            $serviceOption       = $this->getOption($id);
            $classname           = $serviceOption['class'];
            $options             = $serviceOption['options'];
            if(is_a($classname, $this->subServiceInterface, true)) {
                $serviceInstance     = $this->getServiceManager()->build($classname, $options);
                $this->subServices[$id]  = $serviceInstance;
                return $serviceInstance;
            }
            throw new InvalidService('Service must implements ' . $this->subServiceInterface);
        }
        throw new UnknownServiceException('service ' . $id . 'isn\'t configure');
    }
    
    /**
     * return if service is configured
     * @param string $id
     * @return boolean
     */
    public function hasSubService($id) {
        return $this->hasOption($id);
    }
    
}

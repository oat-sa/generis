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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA
 * 
 */

namespace oat\generis\model\Resource;

use oat\oatbox\service\ConfigurableService;

class CreateOrReuseService extends ConfigurableService 
{
    
    const SERVICE_ID = 'generis/createOrReuse';


    /**
     * list of stored service
     * @var array
     */
    protected $service = [];

    /**
     * return a configured instance of CreateOrReuseInterface 
     * @param string $id
     * @return CreateOrReuseInterface
     */
    public function getService($id) {
        if(array_key_exists($id, $this->service)) {
            return $this->service[$id];
        }
        return $this->createService($id);
    }
    
    /**
     * service factory
     * @param string $id
     * @return CreateOrReuseInterface
     */
    protected function createService($id) {
        if($this->hasOption($id)) {
            $serviceOption       = $this->getOption($id);
            $classname           = $serviceOption['class'];
            $options             = $serviceOption['options'];
            $serviceInstance     = new $classname($options);
            $this->service[$id]  = $serviceInstance;
            return $serviceInstance;
        }
        throw new exception\UnknownServiceException('service ' . $id . 'isn\'t configure');
    }
    
    /**
     * return if service is configured
     * @param string $id
     * @return boolean
     */
    protected function hasService($id) {
        return $this->hasOption($id);
    }
    
}


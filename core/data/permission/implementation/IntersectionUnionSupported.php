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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

namespace oat\generis\model\data\permission\implementation;

use oat\generis\model\data\permission\PermissionInterface;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\user\User;

/**
 * Implementation to get permissions only from implementations that support the rights
 *
 * @access public
 * @author Antoine Robin, <antoine@taotesting.com>
 */
class IntersectionUnionSupported extends ConfigurableService
    implements PermissionInterface
{


    /**
     * @param PermissionInterface $service
     * @return $this
     */
    public function add(PermissionInterface $service)
    {
        $registered = false;
        $options = $this->getOption('inner');
        foreach ($options as $impl){
            if($impl == $service){
                $registered = true;
                break;
            }
        }

        if(!$registered){
            $options[] = $service;
            $this->setOption('inner', $options);
        }

        return $this;

    }

    /**
     * @return PermissionInterface[]
     */
    protected function getInner() {
        $results = [];
        foreach ($this->getOption('inner') as $impl) {
            $impl->setServiceLocator($this->getServiceLocator());
            $results[] = $impl;
        }
        return $results;
    }
    
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\PermissionInterface::getPermissions()
     */
    public function getPermissions(User $user, array $resourceIds) {

        $results = array();
        $allRights = $this->getSupportedRights();

        foreach ($this->getInner() as $impl) {
            //Get rights not supported by implementation
            $notSupported = array_diff($allRights, $impl->getSupportedRights());
            $resourceRights = $impl->getPermissions($user, $resourceIds);
            $resourcesRights = [];
            foreach ($resourceRights as $uri => $resourceRight){
                $resourcesRights[$uri] = array_merge($notSupported, $resourceRight);
            }
            $results[] = $resourcesRights;
        }

        $rights = array();
        foreach ($resourceIds as $id) {
            $intersect = null;
            foreach ($results as $modelResult) {
                $intersect = is_null($intersect)
                    ? $modelResult[$id]
                    : array_intersect($intersect, $modelResult[$id]);
            }
            $rights[$id] = array_values($intersect);
        }
        
        return $rights;
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\PermissionInterface::onResourceCreated()
     */
    public function onResourceCreated(\core_kernel_classes_Resource $resource) {
        foreach ($this->getInner() as $impl) {
            $impl->onResourceCreated($resource);
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\PermissionInterface::getSupportedPermissions()
     */
    public function getSupportedRights() {
        $models = $this->getInner();
        $first = array_pop($models);
        $supported = $first->getSupportedRights();
        while (!empty($models)) {
            $model = array_pop($models);
            $supported = array_merge($supported, $model->getSupportedRights());
        }

        return array_values(array_unique($supported));
    }
}
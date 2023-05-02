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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\generis\model\data\permission\implementation;

use core_kernel_classes_Resource;
use oat\generis\model\data\permission\PermissionInterface;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\user\User;

/**
 * Simple permissible Permission model
 *
 * does not require privileges
 * does not grant privileges
 *
 * @access public
 *
 * @author Joel Bout, <joel@taotesting.com>
 */
class Intersection extends ConfigurableService implements PermissionInterface
{
    public function __construct($options = [])
    {
        parent::__construct($options);
    }
    /**
     * @param PermissionInterface[] $persmissionMdels
     */
    public static function spawn($persmissionMdels)
    {
        return new self(['inner' => $persmissionMdels]);
    }

    protected function getInner()
    {
        return $this->getOption('inner');
    }

    /**
     * (non-PHPdoc)
     *
     * @see PermissionInterface::getPermissions()
     */
    public function getPermissions(User $user, array $resourceIds)
    {
        $results = [];

        foreach ($this->getInner() as $impl) {
            $results[] = $impl->getPermissions($user, $resourceIds);
        }

        $rights = [];

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
     *
     * @see PermissionInterface::onResourceCreated()
     */
    public function onResourceCreated(core_kernel_classes_Resource $resource)
    {
        foreach ($this->getInner() as $impl) {
            $impl->onResourceCreated($resource);
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see PermissionInterface::getSupportedPermissions()
     */
    public function getSupportedRights()
    {
        $models = $this->getInner();
        $first = array_pop($models);
        $supported = $first->getSupportedRights();

        while (!empty($models)) {
            $model = array_pop($models);
            $supported = array_intersect($supported, $model->getSupportedRights());
        }

        return array_values($supported);
    }
}

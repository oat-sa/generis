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
 * Copyright (c) 2014-2017 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\oatbox\service;

use Zend\ServiceManager\ServiceLocatorAwareTrait;
/**
 * Class ServiceManagerAwareTrait
 *
 * Trait to transport oat\oatbox\service\ServiceManager
 * It includes tools to register and propagate oat service
 *
 * @package oat\oatbox\service
 * @author Moyon Camille
 */
trait ServiceManagerAwareTrait
{
    use ServiceLocatorAwareTrait;

    /**
     * Get the oat service manager.
     *
     * It should be used for service building, register, build, propagate
     * For reading operation please use $this->getServiceLocator() instead
     *
     * @throws \common_exception_Error
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        $serviceManager = $this->getServiceLocator();
        if (! $serviceManager instanceof ServiceManager) {
            throw new \common_exception_Error('Alternate service locator not compatible with ' . __CLASS__);
        }
        return $serviceManager;
    }

    /**
     * Register a service through ServiceManager
     *
     * @param $serviceKey
     * @param $service
     * @param bool $allowOverride
     */
    public function registerService($serviceKey, ConfigurableService $service, $allowOverride = true)
    {
        if ($allowOverride || ! $this->getServiceLocator()->has($serviceKey)) {
            $this->getServiceManager()->register($serviceKey, $service);
        }
    }
}
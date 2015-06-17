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

use common_ext_ExtensionsManager;
/**
 * The simple placeholder ServiceManager
 * @author Joel Bout <joel@taotesting.com>
 */
class ServiceManager
{
    private static $instance;
    
    public static function getServiceManager()
    {
        if (is_null(self::$instance)) {
            self::$instance = new ServiceManager();
        }
        return self::$instance;
    }
    
    private $services = array();
    
    public function get($serviceKey)
    {
        if (!isset($this->services[$serviceKey])) {
            $parts = explode('/', $serviceKey, 2);
            if (count($parts) < 2) {
                throw new \common_Exception('Invalid servicekey '.$serviceKey);
            }
            list($extId, $configId) = $parts;
            $extension = common_ext_ExtensionsManager::singleton()->getExtensionById($extId);
            $service = $extension->getConfig($configId);
            if ($service instanceof ConfigurableService) {
                $service->setServiceManager($this);
            }
            $this->services[$serviceKey] = $service;
        }
        return $this->services[$serviceKey];
    }
        

}
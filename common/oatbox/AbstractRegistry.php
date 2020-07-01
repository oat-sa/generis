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
 *
 */

namespace oat\oatbox;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceManager;
use common_ext_Extension;

/**
 *
 * Registry allows any extension to store specific arrays of data into extension config file
 *
 * @author Lionel Lecaque <lionel@taotesting.com>
 */
abstract class AbstractRegistry`` extends ConfigurableService
{
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return AbstractRegistry
     */
    public static function getRegistry()
    {
        return ServiceManager::getServiceManager()->get(get_called_class());
    }

    /**
     * Specify in which extensions the config will be stored
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return common_ext_Extension
     */
    abstract protected function getExtension();
    
    /**
     *
     * config file in which the data will be stored
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return string
     */
    abstract protected function getConfigId();

    /**
     *
     * Get the config of dedicated extensions
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    protected function getConfig()
    {
        return $this->getExtension()->getConfig($this->getConfigId());
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param array $map
     */
    protected function setConfig($map)
    {
        $this->getExtension()->setConfig($this->getConfigId(), $map);
    }

    /**
     *
     * Remove a element from the array
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $id
     */
    public function remove($id)
    {
        $map = $this->getMap();
        if (isset($map[$id])) {
            unset($map[$id]);
            $this->setConfig($map);
        }
    }

    /**
     * Get the complete array
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return array
     */
    public function getMap()
    {
        $map = $this->getConfig();
        return is_array($map) ? $map : [];
    }

    /**
     * Add a value to the config with given id
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $id
     * @param string $value
     */
    public function set($id, $value)
    {
        $map = $this->getMap();
        $map[$id] = $value;
        $this->setConfig($map);
    }

    /**
     * Check if the config element exist
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param unknown $id
     */
    public function isRegistered($id)
    {
        return array_key_exists($id, $this->getMap());
    }
    
    
    /**
     *
     * Retrieve the given element from the array in config
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $id
     * @return string
     */
    public function get($id)
    {
        $map = $this->getMap();
        return isset($map[$id]) ? $map[$id] : '';
    }
}

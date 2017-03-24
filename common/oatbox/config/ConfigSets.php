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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\oatbox\config;



/**
 * Configurable base class
 * 
 * inspired by Solarium\Core\Configurable by Bas de Nooijer
 * https://github.com/basdenooijer/solarium/blob/master/library/Solarium/Core/Configurable.php
 *
 * @author Joel Bout <joel@taotesting.com>
 */
trait ConfigSets
{
    /**
     * Sets a specific field of an option
     * 
     * @param string $key
     * @param string $field
     * @param mixed $value
     * @throws \common_exception_InconsistentData
     * @return boolean success
     */
    public function hashSet($key, $field, $value) {
        $option = $this->getHashOption($key);
        $option[$field] = $value;
        return $this->setOption($key, $option);
    }
    
    /**
     * Removes a specific field of an option
     * 
     * @param string $key
     * @param string $field
     * @throws \common_exception_InconsistentData
     * @return boolean
     */
    public function hashRemove($key, $field) {
        $option = $this->getHashOption($key);
        if (!isset($option[$field])) {
            return false;
        } else {
            unset($option[$field]);
            return $this->setOption($key, $option);
        }
    }
    
    /**
     * Returns a specific field of an option
     * 
     * @param string $key
     * @throws \common_exception_InconsistentData
     * @param string $field
     * @return mixed
     */
    public function hashGet($key, $field) {
        $option = $this->getHashOption($key);
        return isset($option[$field]) ? $option[$field] : null;
    }

    /**
     * @param string $name
     * @return mixed
     */
    abstract public function getOption($name);

    /**
     * @param string $name
     * @return boolean
     */
    abstract public function hasOption($name);

    /**
     * @param string $name
     * @param mixed $value
     */
    abstract public function setOption($name, $value);

    /**
     * Retroieve an option and ensure it is an array
     * 
     * @param string $key
     * @throws \common_exception_InconsistentData
     * @return array
     */
    private function getHashOption($key) {
        $option = $this->hasOption($key) ? $this->getOption($key) : [];
        if (!is_array($option)) {
            throw new \common_exception_InconsistentData($key.' is not an array');
        }
        return $option;
    }

}
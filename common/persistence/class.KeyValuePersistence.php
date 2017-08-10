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
 * Copyright (c) 2013-2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Lionel Lecaque  <lionel@taotesting.com>
 * @license GPLv2
 * @package generis
 
 *
 */
class common_persistence_KeyValuePersistence extends common_persistence_Persistence
{
    /**
     * Returns TRUE if $value is successfully set for the identifier $key
     * or FALSE if something went wrong
     *
     * @param $key
     * @param $value
     * @param null $ttl @deprecated
     * @return TRUE or FALSE depending on successfull of writing
     */
    public function set($key, $value, $ttl = null)
    {
        return $this->getDriver()->set($key, $value, $ttl);
    }
    
    /**
     * Returns the value stored for the identifier $key
     * or FALSE if nothing found
     * 
     * @param string $key
     */
    public function get($key)
    {
        return $this->getDriver()->get($key);
    }

    /**
     * Returns TRUE if value is found for the identifier $key
     * or FALSE if nothing found
     *
     * @param string $key
     */
    public function exists($key)
    {
        return $this->getDriver()->exists($key);
    }

    /**
     * Returns TRUE if value has been deleted for the identifier $key
     * or FALSE if deletion went wrong
     *
     * @param string $key
     */
    public function del($key)
    {
        return $this->getDriver()->del($key);
    }

    /**
     * If $driver is Purgable then call the driver purge method
     *
     * @return TRUE or FALSE depending if purge was successfull
     * @throws common_exception_NotImplemented If the driver does not implement purgable interface
     */
    public function purge()
    {
        if ($this->getDriver() instanceof common_persistence_Purgable) {
            return $this->getDriver()->purge();
        } else {
            throw new common_exception_NotImplemented("purge not implemented ");
        }
    }
    
}
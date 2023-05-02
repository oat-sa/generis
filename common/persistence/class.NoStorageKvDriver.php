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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Lionel Lecaque  <lionel@taotesting.com>
 * @license GPLv2
 */

/**
 * Class common_persistence_NoStorageKvDriver
 *
 * @deprecated use \common_persistence_InMemoryKvDriver instead
 *
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class common_persistence_NoStorageKvDriver implements common_persistence_KvDriver, common_persistence_Purgable
{
    /**
     * @see common_persistence_Driver::connect()
     *
     * @param mixed $id
     */
    public function connect($id, array $params)
    {
        return new common_persistence_KeyValuePersistence($params, $this);
    }
    /**
     * @see common_persistence_KvDriver::set()
     *
     * @param mixed $id
     * @param mixed $value
     * @param null|mixed $ttl
     * @param mixed $nx
     */
    public function set($id, $value, $ttl = null, $nx = false)
    {
        return false;
    }

    /**
     * @see common_persistence_KvDriver::get()
     *
     * @param mixed $id
     */
    public function get($id)
    {
        return false;
    }

    /**
     * @see common_persistence_KvDriver::exists()
     *
     * @param mixed $id
     */
    public function exists($id)
    {
        return false;
    }
    /**
     * @see common_persistence_KvDriver::del()
     *
     * @param mixed $id
     */
    public function del($id)
    {
        return true;
    }

    /**
     * Increment existing value
     *
     * @param string $id
     *
     * @return mixed
     */
    public function incr($id)
    {
        return false;
    }

    /**
     * Decrement existing value
     *
     * @param $id
     *
     * @return mixed
     */
    public function decr($id)
    {
        return false;
    }

    /**
     * @see common_persistence_Purgable::purge()
     */
    public function purge()
    {
        return true;
    }
}

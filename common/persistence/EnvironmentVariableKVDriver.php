<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Lionel Lecaque <lionel@taotesting.com>
 * @license GPLv2
 *
 */

namespace oat\generis\persistence;

use common_exception_NoImplementation;
use common_persistence_KeyValuePersistence;
use common_persistence_KvDriver;

class EnvironmentVariableKVDriver implements common_persistence_KvDriver
{

    /**
     *
     * @param $id
     * @param array $params
     * @return common_persistence_KeyValuePersistence
     * @see common_persistence_Driver::connect()
     */
    public function connect($id, array $params)
    {
        return new common_persistence_KeyValuePersistence($params, $this);
    }

    public function set($id, $value, $ttl = null, $nx = false)
    {
        throw new common_exception_NoImplementation(__METHOD__ . '@' . __CLASS__ . 'not implemented');
    }

    public function get($id)
    {
        return $this->exists($id) ? $_ENV[$id] : false;
    }

    public function exists($id)
    {
        return array_key_exists($id, $_ENV);
    }

    public function del($id)
    {
        throw new common_exception_NoImplementation(__METHOD__ . '@' . __CLASS__ . 'not implemented');
    }

    public function incr($id)
    {
        throw new common_exception_NoImplementation(__METHOD__ . '@' . __CLASS__ . 'not implemented');
    }

    public function decr($id)
    {
        throw new common_exception_NoImplementation(__METHOD__ . '@' . __CLASS__ . 'not implemented');
    }
}

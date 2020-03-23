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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
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
     * @inheritDoc
     */
    public function connect($id, array $params)
    {
        return new common_persistence_KeyValuePersistence($params, $this);
    }

    /**
     * @inheritDoc
     */
    public function set($id, $value, $ttl = null, $nx = false): bool
    {
        throw new common_exception_NoImplementation(__METHOD__ . '@' . __CLASS__ . ' not implemented');
    }

    /**
     * @inheritDoc
     */
    public function get($id): string
    {
        return $this->exists($id) ? $_ENV[$id] : false;
    }

    /**
     * @inheritDoc
     */
    public function exists($id): bool
    {
        return array_key_exists($id, $_ENV);
    }

    /**
     * @inheritDoc
     */
    public function del($id): bool
    {
        throw new common_exception_NoImplementation(__METHOD__ . '@' . __CLASS__ . 'not implemented');
    }

    /**
     * @inheritDoc
     */
    public function incr($id): bool
    {
        throw new common_exception_NoImplementation(__METHOD__ . '@' . __CLASS__ . 'not implemented');
    }

    /**
     * @inheritDoc
     */
    public function decr($id): bool
    {
        throw new common_exception_NoImplementation(__METHOD__ . '@' . __CLASS__ . 'not implemented');
    }
}

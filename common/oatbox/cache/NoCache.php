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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA
 *
 */

namespace oat\oatbox\cache;

use oat\oatbox\service\ConfigurableService;

/**
 * Caches data in a key-value store
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 */
class NoCache extends ConfigurableService implements SimpleCache
{
    use MultipleCacheTrait;

    public function set($key, $value, $ttl = null)
    {
        return true;
    }

    public function clear()
    {
        return true;
    }

    public function delete($key)
    {
        return true;
    }

    public function get($key, $default = null)
    {
        return $default;
    }

    public function has($key)
    {
        return false;
    }
}

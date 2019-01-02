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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 *
 */

/**
 * Class common_persistence_InMemoryKvDriver
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class common_persistence_InMemoryKvDriver implements common_persistence_KvDriver, common_persistence_Purgable
{

    /**
     * @var array
     */
    protected $persistence = [];

    /**
     *
     * @see common_persistence_Driver::connect()
     */
    function connect($id, array $params){
        return new common_persistence_KeyValuePersistence($params, $this);
    }

    public function set($id, $value, $ttl = null, $nx = false)
    {
        $this->persistence[$id] = $value;
        return true;
    }

    public function get($id)
    {
        return $this->exists($id) ? $this->persistence[$id] : false;
    }
    
    public function exists($id)
    {
        return array_key_exists($id, $this->persistence);
    }

    public function del($id)
    {
        unset($this->persistence[$id]);
        return true;
    }

    public function purge()
    {
        $this->persistence = [];
    }

    public function incr($id) {
        if (!isset($this->persistence[$id])) {
            $this->persistence[$id] = 0;
        }
        if (!is_int($this->persistence[$id])) {
            throw new common_exception_InconsistentData('Cannot increment non intvalue for '.$id);
        }
        return ++$this->persistence[$id];
    }

    public function decr($id) {
        if (!isset($this->persistence[$id])) {
            $this->persistence[$id] = 0;
        }
        if (!is_int($this->persistence[$id])) {
            throw new common_exception_InconsistentData('Cannot decrement non intvalue for '.$id);
        }
        return --$this->persistence[$id];
    }
}

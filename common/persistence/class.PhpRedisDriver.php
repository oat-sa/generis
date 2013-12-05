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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Lionel Lecaque  <lionel@taotesting.com>
 * @license GPLv2
 * @package 
 * @subpackage 
 *
 */
class common_persistence_PhpRedisDriver implements common_persistence_KvDriver
{

    const DEFAULT_PORT = 6379;

    /**
     * @var Redis
     */
    private $connection;

    /**
     * (non-PHPdoc)
     * @see common_persistence_Driver::connect()
     */
    function connect(array $params)
    {
        $this->connection = new Redis();
        if ($this->connection == false) {
            throw new common_exception_Error("Redis php module not found");
        }
        if (!isset($params['host'])) {
            throw new common_exception_Error('Missing host information for Redis driver');
        }
        $host = $params['host'];
        $port = $params['port'] ? $params['port'] : self::DEFAULT_PORT;
        $this->connection->connect($host, $port);
        if (isset($params['password'])) {
            $this->connection->auth($params['password']);
        }
        return new common_persistence_KeyValuePersistence($params, $this);
    }
    
    public function set($id, $value, $ttl = null)
    {
        if (! is_null($ttl)) {
        return $this->connection->set($id, $value, $ttl);
        } else {
            return $this->connection->set($id, $value);
        }
        
    }
    
    public function get($id) {
        return $this->connection->get($id);
    }
    
    public function exists($id) {
        return $this->connection->exists($id);
    }
    
    public function del($id) {
        return $this->connection->del($id);
    }

    

}

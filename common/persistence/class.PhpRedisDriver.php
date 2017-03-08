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
 
 *
 */
class common_persistence_PhpRedisDriver implements common_persistence_AdvKvDriver
{

    const DEFAULT_PORT    = 6379;
    const DEFAULT_TRY     = 2;
    const DEFAULT_TIMEOUT = 1.0;

    /**
     * @var Redis
     */
    private $connection;

    /**
     * (non-PHPdoc)
     * @see common_persistence_Driver::connect()
     */
    function connect($key, array $params)
    {
        $this->connection = new Redis();
        if ($this->connection == false) {
            throw new common_exception_Error("Redis php module not found");
        }
        if (!isset($params['host'])) {
            throw new common_exception_Error('Missing host information for Redis driver');
        }
        $host    = $params['host'];
        $port    = isset($params['port']) ? $params['port'] : self::DEFAULT_PORT;
        $timeout = isset($params['timeout']) ? $params['timeout'] : self::DEFAULT_TIMEOUT;
        $retry   = isset($params['retry']) ? $params['retry'] : self::DEFAULT_TRY;
        $persist = isset($params['pconnect']) ? $params['pconnect'] : true;
        
        if ($persist) {
            $this->persistentConnection($host, $port , $timeout , $retry );
        } else {
            $this->connection->connect($host, $port);
        }
        if (isset($params['password'])) {
            $this->connection->auth($params['password']);
        }
        return new common_persistence_AdvKeyValuePersistence($params, $this);
    }

    /**
     * @param $host
     * @param $port
     * @param $timeout
     * @param int $tried
     * @return bool
     * @throws common_exception_Error
     */
    protected function persistentConnection($host, $port , $timeout , $retry , $tried = 1) {

        if($tried <= $retry ) {
            if($this->connection->pconnect($host, $port , $timeout)) {
                return true;
            }
            return $this->persistentConnection($host, $port , $timeout , $retry , $tried+1);
        }
        throw new common_exception_Error('failed to connect to Redis Server');
    }

    public function set($key, $value, $ttl = null)
    {
        if (! is_null($ttl)) {
            return $this->connection->set($key, $value, $ttl);
        } else {
            return $this->connection->set($key, $value);
        }
        
    }
    
    public function get($key) {
        return $this->connection->get($key);
    }
    
    public function exists($key) {
        return $this->connection->exists($key);
    }
    
    public function del($key) {
        return $this->connection->del($key);
    }

    //O(N) where N is the number of fields being set.
    public function hmSet($key, $fields) {
        return $this->connection->hmSet($key, $fields);
    }
    //Time complexity: O(1)
    public function hExists($key, $field){
        return (bool) $this->connection->hExists($key, $field);
    }
    //Time complexity: O(1)
    public function hSet($key, $field, $value){
        return $this->connection->hSet($key, $field, $value);
    }
    //Time complexity: O(1)
    public function hGet($key, $field){
        return $this->connection->hGet($key, $field);
    }
    //Time complexity: O(N) where N is the size of the hash.
    public function hGetAll($key){
        return $this->connection->hGetAll($key);
    }
    //Time complexity: O(N)
    public function keys($pattern) {
        return $this->connection->keys($pattern);
    }
    //Time complexity: O(1)
    public function incr($key) {
       return $this->connection->incr($key); 
    }
}

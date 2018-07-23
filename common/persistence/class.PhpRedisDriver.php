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
 * @package
 *
 */
class common_persistence_PhpRedisDriver implements common_persistence_AdvKvDriver, common_persistence_KeyValue_Nx
{

    const DEFAULT_PORT     = 6379;
    const DEFAULT_ATTEMPT  = 3;
    const DEFAULT_TIMEOUT  = 5; // in seconds
    const RETRY_DELAY      = 500000; // Eq to 0.5s

    /**
     * @var Redis
     */
    private $connection;

    /**
     * @var $params
     */
    private $params;

    /**
     * store connection params and try to connect
     * @see common_persistence_Driver::connect()
     */
    function connect($key, array $params)
    {
        $this->params = $params;
        $this->connectionSet($params);

        return new common_persistence_AdvKeyValuePersistence($params, $this);
    }

    /**
     * create a new connection using stored parameters
     * @param array $params
     * @throws common_exception_Error
     */
    function connectionSet(array $params)
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
        $persist = isset($params['pconnect']) ? $params['pconnect'] : true;
        $this->params['attempt'] = isset($params['attempt']) ? $params['attempt'] : self::DEFAULT_ATTEMPT;

        if ($persist) {
            $this->connection->pconnect($host , $port , $timeout);
        } else {
            $this->connection->connect($host , $port , $timeout);
        }
    }

    /**
     * @param $method
     * @param array $params
     * @param $retry
     * @param int $attempt
     * @return mixed
     * @throws Exception
     */
    protected function callWithRetry( $method , array $params , $attempt = 1) {

        $success       = false;
        $lastException = null;
        $result        = false;

        $retry = $this->params['attempt'];

        while (!$success && $attempt <= $retry) {

            try {
                $result = call_user_func_array([$this->connection , $method] , $params);
                $success = true;
            } catch (\Exception $e) {
                $lastException = $e;
                \common_Logger::d('Redis  ' . $method . ' failed ' . $attempt . '/' . $retry . ' :  ' . $e->getMessage());
                if ($e->getMessage() == 'Failed to AUTH connection' && isset($this->params['password'])) {
                    \common_Logger::d('Authenticating Redis connection');
                    $this->connection->auth($this->params['password']);
                }
                $delay = rand(self::RETRY_DELAY , self::RETRY_DELAY*2);
                usleep($delay);
                $this->connectionSet($this->params);
            }
            $attempt++;
        }

        if (!$success) {
            throw $lastException;
        }
        return $result;

    }

    /**
     * (non-PHPdoc)
     * @see common_persistence_KvDriver::set()
     */
    public function set($key, $value, $ttl = null, $nx = false)
    {
        $options = [];
        if (!is_null($ttl)) {
            $options['ex'] = $ttl;
        };
        if ($nx) {
            $options[] = 'nx';
        };
        return $this->callWithRetry('set' , [$key, $value, $options]);
        
    }
    
    public function get($key) {

        return $this->callWithRetry('get' , [$key] );

    }
    
    public function exists($key) {
        return (bool)$this->callWithRetry('exists' , [$key] );
    }
    
    public function del($key) {
        return $this->callWithRetry('del' , [$key] );
    }

    //O(N) where N is the number of fields being set.
    public function hmSet($key, $fields) {
        return $this->callWithRetry('hmSet' , [$key, $fields] );
    }
    //Time complexity: O(1)
    public function hExists($key, $field)
    {
        return (bool)$this->callWithRetry('hExists', [$key, $field]);
    }

    //Time complexity: O(1)
    public function hSet($key, $field, $value){
        return $this->callWithRetry('hSet' , [$key, $field, $value] );
    }
    //Time complexity: O(1)
    public function hGet($key, $field){
        return $this->callWithRetry('hGet' , [$key, $field]);
    }
    //Time complexity: O(N) where N is the size of the hash.
    public function hGetAll($key){
        return $this->callWithRetry('hGetAll' , [$key] );
    }
    //Time complexity: O(N)
    public function keys($pattern) {
        return $this->callWithRetry('keys' , [$pattern]);
    }
    //Time complexity: O(1)
    public function incr($key) {
        return $this->callWithRetry('incr' , [$key] );
    }
    //Time complexity: O(1)
    public function decr($key) {
        return $this->callWithRetry('decr' , [$key] );
    }

}

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
 * Copyright (c) 2013-2023 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Lionel Lecaque  <lionel@taotesting.com>
 * @license GPLv2
 *
 * @package
 * phpcs:disable Squiz.Classes.ValidClassName
 */

class common_persistence_PhpRedisDriver implements common_persistence_AdvKvDriver, common_persistence_KeyValue_Nx
{
    public const DEFAULT_PORT = 6379;
    public const DEFAULT_ATTEMPT = 3;
    public const DEFAULT_TIMEOUT = 5; // in seconds
    public const RETRY_DELAY = 500000; // Eq to 0.5s

    /**
     * @var Redis
     */
    private $connection;

    /**
     * @var
     */
    private $params;

    /**
     * store connection params and try to connect
     *
     * @see common_persistence_Driver::connect()
     *
     * @param mixed $key
     */
    public function connect($key, array $params)
    {
        $this->params = $params;
        $this->connectionSet($params);

        return new common_persistence_AdvKeyValuePersistence($params, $this);
    }

    /**
     * create a new connection using stored parameters
     *
     * @param array $params
     *
     * @throws common_exception_Error
     */
    public function connectionSet(array $params)
    {
        if (!isset($params['host'])) {
            throw new common_exception_Error('Missing host information for Redis driver');
        }

        $port = $params['port'] ?? self::DEFAULT_PORT;
        $timeout = $params['timeout'] ?? self::DEFAULT_TIMEOUT;
        $persist = $params['pconnect'] ?? true;
        $this->params['attempt'] = $params['attempt'] ?? self::DEFAULT_ATTEMPT;

        if (is_array($params['host'])) {
            $this->connectToCluster($params['host'], $timeout, $persist);
        } else {
            $this->connectToSingleNode($params['host'], $port, $timeout, $persist);
        }
    }

    private function connectToSingleNode(string $host, int $port, int $timeout, bool $persist)
    {
        $this->connection = new Redis();

        if ($this->connection == false) {
            throw new common_exception_Error('Redis php module not found');
        }

        if ($persist) {
            $this->connection->pconnect($host, $port, $timeout);
        } else {
            $this->connection->connect($host, $port, $timeout);
        }
    }

    private function connectToCluster(array $host, int $timeout, bool $persist)
    {
        $this->connection = new RedisCluster(null, $host, $timeout, null, $persist);
    }

    /**
     * @param $method
     * @param array $params
     * @param $retry
     * @param int $attempt
     *
     * @throws Exception
     *
     * @return mixed
     */
    protected function callWithRetry($method, array $params, $attempt = 1)
    {
        $success = false;
        $lastException = null;
        $result = false;

        $retry = (int)$this->params['attempt'];

        while (!$success && $attempt <= $retry) {
            try {
                $result = call_user_func_array([$this->connection, $method], $params);
                $success = true;
            } catch (Exception $e) {
                $lastException = $e;

                $this->reconnectOnException($lastException, $method, $attempt, $retry);
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
     *
     * @see common_persistence_KvDriver::set()
     *
     * @param mixed $key
     * @param mixed $value
     * @param null|mixed $ttl
     * @param mixed $nx
     */
    public function set($key, $value, $ttl = null, $nx = false)
    {
        $options = [];

        if (!is_null($ttl)) {
            $options['ex'] = $ttl;
        }

        if ($nx) {
            $options[] = 'nx';
        }

        return $this->callWithRetry('set', [$key, $value, $options]);
    }

    public function get($key)
    {
        return $this->callWithRetry('get', [$key]);
    }

    public function exists($key)
    {
        return (bool)$this->callWithRetry('exists', [$key]);
    }

    public function del($key)
    {
        return $this->callWithRetry('del', [$key]);
    }

    //O(N) where N is the number of fields being set.
    public function hmSet($key, $fields)
    {
        return $this->callWithRetry('hmSet', [$key, $fields]);
    }

    //Time complexity: O(1)
    public function hExists($key, $field)
    {
        return (bool)$this->callWithRetry('hExists', [$key, $field]);
    }

    //Time complexity: O(1)
    public function hSet($key, $field, $value)
    {
        return $this->callWithRetry('hSet', [$key, $field, $value]);
    }

    //Time complexity: O(1)
    public function hGet($key, $field)
    {
        return $this->callWithRetry('hGet', [$key, $field]);
    }

    public function hDel($key, $field): bool
    {
        return (bool)$this->callWithRetry('hDel', [$key, $field]);
    }

    //Time complexity: O(N) where N is the size of the hash.
    public function hGetAll($key)
    {
        return $this->callWithRetry('hGetAll', [$key]);
    }

    //Time complexity: O(N)
    public function keys($pattern)
    {
        return $this->callWithRetry('keys', [$pattern]);
    }

    //Time complexity: O(1)
    public function incr($key)
    {
        return $this->callWithRetry('incr', [$key]);
    }

    //Time complexity: O(1)
    public function decr($key)
    {
        return $this->callWithRetry('decr', [$key]);
    }

    /**
     * @throws RedisException
     * @throws common_exception_Error
     */
    public function scan(int &$iterator = null, string $pattern = null, int $count = 1000): array
    {
        $retry = (int)$this->params['attempt'];
        $attempt = 0;

        while ($attempt <= $retry) {
            try {
                return $this->connection->scan($iterator, $pattern, $count);
            } catch (Exception $exception) {
                $this->reconnectOnException($exception, $method, $attempt, $retry);
            }

            $attempt++;
        }

        if (isset($exception)) {
            throw $exception;
        }

        return [];
    }

    /**
     * @return array|bool
     */
    public function mGet(array $keys)
    {
        return $this->callWithRetry('mGet', [$keys]);
    }

    /**
     * @return bool|mixed
     */
    public function mDel(array $keys)
    {
        return $this->callWithRetry('del', [$keys]);
    }

    /**
     * @return bool|mixed
     */
    public function mSet(array $keyValues)
    {
        return $this->callWithRetry('mSet', [$keyValues]);
    }

    /**
     * @return Redis
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @throws RedisException
     * @throws common_exception_Error
     *
     * @return void
     */
    private function reconnectOnException(Exception $exception, string $method, int $attempt, int $retry): void
    {
        common_Logger::d(
            sprintf(
                'Redis %s failed %s/%s:  %s',
                $method,
                $attempt,
                $retry,
                $exception->getMessage(),
            )
        );

        if ($exception->getMessage() == 'Failed to AUTH connection' && isset($this->params['password'])) {
            common_Logger::d('Authenticating Redis connection');

            $this->connection->auth($this->params['password']);
        }

        $delay = rand(self::RETRY_DELAY, self::RETRY_DELAY * 2);

        usleep($delay);

        $this->connectionSet($this->params);
    }
}

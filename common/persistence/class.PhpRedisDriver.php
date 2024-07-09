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
 * @package
 * phpcs:disable Squiz.Classes.ValidClassName
 */

class common_persistence_PhpRedisDriver implements common_persistence_AdvKvDriver, common_persistence_KeyValue_Nx
{
    public const DEFAULT_PORT = 6379;
    public const DEFAULT_ATTEMPT = 3;
    public const DEFAULT_TIMEOUT = 5; // in seconds
    public const RETRY_DELAY = 500000; // Eq to 0.5s

    private const DEFAULT_PREFIX_SEPARATOR = ':';

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
    public function connect($key, array $params)
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
    public function connectionSet(array $params)
    {
        if (!isset($params['host'])) {
            throw new common_exception_Error('Missing host information for Redis driver');
        }

        $port = isset($params['port']) ? $params['port'] : self::DEFAULT_PORT;
        $timeout = isset($params['timeout']) ? $params['timeout'] : self::DEFAULT_TIMEOUT;
        $persist = isset($params['pconnect']) ? $params['pconnect'] : true;
        $this->params['attempt'] = isset($params['attempt']) ? $params['attempt'] : self::DEFAULT_ATTEMPT;

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
            throw new common_exception_Error("Redis php module not found");
        }
        if ($persist) {
            $this->connection->pconnect($host, $port, $timeout);
        } else {
            $this->connection->connect($host, $port, $timeout);
        }
        if (isset($this->params['database_index'])) {
            if (!$this->connection->select($this->params['database_index'])) {
                $this->connection->close();
                throw new common_exception_Error(
                    "Failed to select Redis database"
                );
            }
        }
    }

    private function connectToCluster(array $host, int $timeout, bool $persist)
    {
        if (isset($this->params['database_index'])) {
            throw new common_exception_Error(
                "Redis Cluster can only support a single database, 'database_index' parameter is invalid."
            );
        }
        $this->connection = new RedisCluster(null, $host, $timeout, null, $persist);
    }

    /**
     * @param $method
     * @param array $params
     * @param $retry
     * @param int $attempt
     * @return mixed
     * @throws Exception
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
     * @see common_persistence_KvDriver::set()
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
        return $this->callWithRetry('set', [$this->prefixKey($key), $value, $options]);
    }

    public function get($key)
    {
        return $this->callWithRetry('get', [$this->prefixKey($key)]);
    }

    public function exists($key)
    {
        return (bool)$this->callWithRetry('exists', [$this->prefixKey($key)]);
    }

    public function del($key)
    {
        return $this->callWithRetry('del', [$this->prefixKey($key)]);
    }

    //O(N) where N is the number of fields being set.
    public function hmSet($key, $fields)
    {
        return $this->callWithRetry('hmSet', [$this->prefixKey($key), $fields]);
    }

    //Time complexity: O(1)
    public function hExists($key, $field)
    {
        return (bool)$this->callWithRetry('hExists', [$this->prefixKey($key), $field]);
    }

    //Time complexity: O(1)
    public function hSet($key, $field, $value)
    {
        return $this->callWithRetry('hSet', [$this->prefixKey($key), $field, $value]);
    }

    //Time complexity: O(1)
    public function hGet($key, $field)
    {
        return $this->callWithRetry('hGet', [$this->prefixKey($key), $field]);
    }

    public function hDel($key, $field): bool
    {
        return (bool)$this->callWithRetry('hDel', [$this->prefixKey($key), $field]);
    }

    //Time complexity: O(N) where N is the size of the hash.
    public function hGetAll($key)
    {
        return $this->callWithRetry('hGetAll', [$this->prefixKey($key)]);
    }

    //Time complexity: O(N)
    public function keys($pattern)
    {
        return $this->callWithRetry('keys', [$this->prefixKey($pattern)]);
    }

    //Time complexity: O(1)
    public function incr($key)
    {
        return $this->callWithRetry('incr', [$this->prefixKey($key)]);
    }

    //Time complexity: O(1)
    public function decr($key)
    {
        return $this->callWithRetry('decr', [$this->prefixKey($key)]);
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
                return $this->connection->scan($iterator, $this->prefixKey($pattern), $count);
            } catch (Exception $exception) {
                $this->reconnectOnException($exception, 'scan', $attempt, $retry);
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
        return $this->callWithRetry('mGet', [$this->prefixKeys($keys)]);
    }

    /**
     * @return bool|mixed
     */
    public function mDel(array $keys)
    {
        return $this->callWithRetry('del', [$this->prefixKeys($keys)]);
    }

    /**
     * @return bool|mixed
     */
    public function mSet(array $keyValues)
    {
        return $this->callWithRetry('mSet', [$this->prefixKeys($keyValues, true)]);
    }

    /**
     * @return Redis
     */
    public function getConnection()
    {
        return $this->connection;
    }

    protected function getPrefix(array $params): ?string
    {
        $prefix = null;

        if (!empty($this->params['prefix'])) {
            $prefix = $this->params['prefix'];
        }

        return $prefix;
    }

    /**
     * @param string|int|null $key
     * @return string|int|null
     */
    private function prefixKey($key)
    {
        $prefix = $this->getPrefix($this->params);

        if ($prefix === null) {
            return $key;
        }

        return $prefix . ($this->params['prefixSeparator'] ?? self::DEFAULT_PREFIX_SEPARATOR) . $key;
    }

    private function prefixKeys(array $keys, bool $keyValueMode = false): array
    {
        if ($this->getPrefix($this->params) !== null) {
            $prefixedKeys = [];
            foreach (array_values($keys) as $i => $element) {
                if ($keyValueMode) {
                    $prefixedKeys[] = $i % 2 == 0 ? $this->prefixKey($element) : $element;
                } else {
                    $prefixedKeys[] = $this->prefixKey($element);
                }
            }

            return $prefixedKeys;
        }

        return $keys;
    }

    /**
     * @return void
     * @throws RedisException
     * @throws common_exception_Error
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

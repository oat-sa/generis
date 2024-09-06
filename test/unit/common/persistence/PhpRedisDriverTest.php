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
 * Copyright (c) 2024 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\generis\test\unit\common\persistence;

use common_persistence_PhpRedisDriver;
use PHPUnit\Framework\TestCase;

class PhpRedisDriverTest extends TestCase
{
    private common_persistence_PhpRedisDriver $driver;

    private object $connection;

    public function setUp(): void
    {
        $driver = new common_persistence_PhpRedisDriver();

        $this->connection = new class
        {
            public array $calls = [];

            public function __call($name, $arguments)
            {
                $this->calls[$name] = $arguments;
                if ($name === 'scan') {
                    return [];
                }

                return 'data';
            }
        };

        $this->setDriverProperty($driver, 'connection', $this->connection);

        $this->driver = $driver;
    }

    public function testKeyPrefixIsNotAddedIfPrefixIsNotSet()
    {
        $this->setDriverProperty($this->driver, 'params', ['attempt' => 2]);

        $this->driver->set('key', 'value');

        $this->assertEquals('key', $this->getLastCallKey('set'));
    }

    public function testKeyPrefixIsAddedIfPrefixIsSet()
    {
        $this->setDriverProperty($this->driver, 'params', ['attempt' => 2, 'prefix' => '25']);

        $this->driver->set('key1', 'value');

        $this->assertEquals('25:key1', $this->getLastCallKey('set'));
    }

    public function testKeyPrefixHasNotDefaultSeparatorIfSeparatorIsSet()
    {
        $this->setDriverProperty($this->driver, 'params', [
            'attempt' => 2,
            'prefix' => '25',
            'prefixSeparator' => '-'
        ]);

        $this->driver->set('key2', 'value');

        $this->assertEquals('25-key2', $this->getLastCallKey('set'));
    }

    public function testKeysHavePrefixWithoutKeyValueMode()
    {
        $this->setDriverProperty($this->driver, 'params', ['attempt' => 2, 'prefix' => '26']);

        $this->driver->mGet(['key1', 'key2']);

        $this->assertEquals(['26:key1', '26:key2'], $this->getLastCallKey('mGet'));
    }

    public function testKeysHavePrefixWithKeyValueMode()
    {
        $this->setDriverProperty($this->driver, 'params', ['attempt' => 2, 'prefix' => '25']);

        $this->driver->mSet(['key1', 'value1', 'key2', 'value2']);

        $this->assertEquals(['25:key1', 'value1', '25:key2', 'value2'], $this->getLastCallKey('mSet'));
    }

    public function testKeyPrefixIsAddedToAllMethods()
    {
        $this->setDriverProperty($this->driver, 'params', ['attempt' => 2, 'prefix' => 25]);

        $methods = [
            'set' => ['key', 'value'],
            'get' => ['key'],
            'exists' => ['key'],
            'del' => ['key'],
            'hmSet' => ['key', 'field'],
            'hExists' => ['key', 'field'],
            'hSet' => ['key', 'field', 'value'],
            'hGet' => ['key', 'field'],
            'hDel' => ['key', 'field'],
            'keys' => ['key'],
            'incr' => ['key'],
            'decr' => ['key'],
        ];

        foreach ($methods as $method => $methodParams) {
            $this->driver->$method(...$methodParams);
            $this->assertEquals('25:key', $this->getLastCallKey($method));
        }
    }

    public function testKeyPrefixIsAddedForScanMethod()
    {
        $this->setDriverProperty($this->driver, 'params', ['attempt' => 2, 'prefix' => 'pref']);

        $iterator = null;
        $this->driver->scan($iterator, '*pattern*');

        $this->assertEquals('pref:*pattern*', $this->driver->getConnection()->calls['scan'][1]);
    }

    private function getLastCallKey(string $method)
    {
        return $this->driver->getConnection()->calls[$method][0];
    }

    private function setDriverProperty($driver, string $propertyName, $propertyValue): void
    {
        $reflection = new \ReflectionClass($driver);

        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($driver, $propertyValue);
    }
}

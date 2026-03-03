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
 * Foundation, Inc., 31 Milk St # 960789 Boston, MA 02196 USA.
 *
 * Copyright (c) 2026 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\generis\test\unit\common\persistence;

use common_Exception;
use common_persistence_SqlKvDriver;
use common_persistence_SqlPersistence;
use common_persistence_sql_Platform;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;

/**
 * Unit tests for SqlKvDriver::set(), incr(), and decr().
 * Asserts that Doctrine DBAL parameter binding uses keys without leading colons
 * (e.g. 'data', 'time', 'id') and verifies success/error behavior across supported platforms.
 */
class SqlKvDriverTest extends TestCase
{
    /**
     * Builds an SqlKvDriver with injected mock persistence and platform.
     * Uses reflection so we can assert params passed to exec() without requiring a real DB.
     *
     * @param string $platformName One of: mysql, oracle, postgresql, gcp-spanner, or other for "else" branch
     * @return array{0: common_persistence_SqlKvDriver, 1: MockObject, 2: MockObject}
     */
    private function createDriverWithMockPersistence(string $platformName): array
    {
        $platform = $this->createMock(common_persistence_sql_Platform::class);
        $platform->method('getName')->willReturn($platformName);
        $platform->method('limitStatement')->willReturnCallback(
            static function ($stmt, $limit, $offset = 0) {
                return $stmt . ' LIMIT ' . (int)$limit;
            }
        );

        $persistence = $this->createMock(common_persistence_SqlPersistence::class);
        $persistence->method('getPlatForm')->willReturn($platform);

        $driver = new common_persistence_SqlKvDriver();
        $ref = new ReflectionClass($driver);
        $propPersistence = $ref->getProperty('sqlPersistence');
        $propPersistence->setAccessible(true);
        $propPersistence->setValue($driver, $persistence);
        $propId = $ref->getProperty('sqlPersistenceId');
        $propId->setAccessible(true);
        $propId->setValue($driver, 'test');
        $propGc = $ref->getProperty('garbageCollection');
        $propGc->setAccessible(true);
        $propGc->setValue($driver, 0);

        return [$driver, $persistence, $platform];
    }

    /**
     * Asserts that an array of params uses keys without leading colon (Doctrine DBAL expectation).
     */
    private function assertParamKeysHaveNoLeadingColon(array $params): void
    {
        foreach (array_keys($params) as $key) {
            $this->assertStringNotContainsString(
                ':',
                (string)$key,
                'Parameter keys must not have leading colon for DBAL binding. Got: ' . $key
            );
        }
    }

    // ------------------------- set() -------------------------

    public function testSetMysqlPassesParamsWithoutLeadingColonAndReturnsTrue(): void
    {
        [$driver, $persistence] = $this->createDriverWithMockPersistence('mysql');

        /** @var array<string, mixed> $capturedParams */
        $capturedParams = [];
        $persistence->expects($this->once())
            ->method('exec')
            ->with(
                $this->isType('string'),
                $this->callback(function ($params) use (&$capturedParams) {
                    $capturedParams = is_array($params) ? $params : [];
                    return true;
                })
            )
            ->willReturn(1);

        $result = $driver->set('key1', 'value1', null, false);

        $this->assertTrue($result);
        $this->assertNotEmpty($capturedParams);
        $this->assertParamKeysHaveNoLeadingColon($capturedParams);
        $this->assertArrayHasKey('data', $capturedParams);
        $this->assertArrayHasKey('time', $capturedParams);
        $this->assertArrayHasKey('id', $capturedParams);
        $this->assertSame('key1', $capturedParams['id']);
    }

    public function testSetPostgresqlPassesParamsWithoutLeadingColonExecThenInsertOnZeroRows(): void
    {
        [$driver, $persistence] = $this->createDriverWithMockPersistence('postgresql');

        $execParams = null;
        $persistence->expects($this->once())
            ->method('exec')
            ->willReturnCallback(function ($stmt, $params, $types = []) use (&$execParams) {
                $execParams = is_array($params) ? $params : [];
                $this->assertParamKeysHaveNoLeadingColon($execParams);
                return 0;
            });
        $persistence->expects($this->once())
            ->method('insert')
            ->with(
                'kv_store',
                $this->anything(),
                $this->anything()
            )
            ->willReturn(1);

        $result = $driver->set('key2', 'value2', 3600, false);

        $this->assertTrue($result);
        $this->assertNotEmpty($execParams, 'exec() should have been called with params');
        $this->assertArrayHasKey('data', $execParams);
        $this->assertArrayHasKey('time', $execParams);
        $this->assertArrayHasKey('id', $execParams);
    }

    public function testSetPostgresqlPassesParamsWithoutLeadingColonExecSuccessNoInsert(): void
    {
        [$driver, $persistence] = $this->createDriverWithMockPersistence('postgresql');

        $execParams = [];
        $persistence->expects($this->once())
            ->method('exec')
            ->willReturnCallback(function ($stmt, $params, $types = []) use (&$execParams) {
                $execParams = is_array($params) ? $params : [];
                $this->assertParamKeysHaveNoLeadingColon($execParams);
                return 1;
            });
        $persistence->expects($this->never())->method('insert');

        $result = $driver->set('key3', 'value3', null, false);

        $this->assertTrue($result);
        $this->assertSame('key3', $execParams['id']);
    }

    /**
     * Oracle branch currently does not call exec() (only sets $statement); set() returns false.
     * When fixed to call exec(), params must use keys without leading colon.
     */
    public function testSetOracleCurrentlyReturnsFalseWithoutCallingExec(): void
    {
        [$driver, $persistence] = $this->createDriverWithMockPersistence('oracle');

        $persistence->expects($this->never())->method('exec');
        $persistence->expects($this->never())->method('insert');

        $result = $driver->set('key_oracle', 'val', null, false);

        $this->assertFalse($result);
    }

    public function testSetThrowsCommonExceptionOnExecFailure(): void
    {
        [$driver, $persistence] = $this->createDriverWithMockPersistence('mysql');

        $persistence->expects($this->once())
            ->method('exec')
            ->willThrowException(new \Exception('Named parameter "data" does not have a bound value.'));

        $this->expectException(common_Exception::class);
        $this->expectExceptionMessage('Unable to write the key value storage table in the database');

        $driver->set('key_fail', 'value', null, false);
    }

    public function testSetIntValueEncodesAsIntegerNotBase64(): void
    {
        [$driver, $persistence] = $this->createDriverWithMockPersistence('mysql');

        $capturedParams = null;
        $persistence->expects($this->once())
            ->method('exec')
            ->willReturnCallback(function ($stmt, $params) use (&$capturedParams) {
                $capturedParams = $params;
                return 1;
            });

        $driver->set('counter', 42, null, false);

        $this->assertNotEmpty($capturedParams);
        $this->assertSame(42, $capturedParams['data']);
    }

    // ------------------------- incr() -------------------------

    public function testIncrMysqlPassesParamIdWithoutLeadingColonAndReturnsAffectedRows(): void
    {
        [$driver, $persistence] = $this->createDriverWithMockPersistence('mysql');

        $capturedParams = [];
        $persistence->expects($this->once())
            ->method('exec')
            ->with(
                $this->stringContains('kv_value + 1'),
                $this->callback(function ($params) use (&$capturedParams) {
                    $capturedParams = is_array($params) ? $params : [];
                    return true;
                })
            )
            ->willReturn(1);

        $result = $driver->incr('counter_key');

        $this->assertSame(1, $result);
        $this->assertNotEmpty($capturedParams);
        $this->assertParamKeysHaveNoLeadingColon($capturedParams);
        $this->assertArrayHasKey('id', $capturedParams);
        $this->assertSame('counter_key', $capturedParams['id']);
    }

    public function testIncrPostgresqlUsesIntegerCastAndPassesParamWithoutColon(): void
    {
        [$driver, $persistence] = $this->createDriverWithMockPersistence('postgresql');

        $capturedParams = [];
        $persistence->expects($this->once())
            ->method('exec')
            ->with(
                $this->stringContains('kv_value::integer + 1'),
                $this->callback(function ($params) use (&$capturedParams) {
                    $capturedParams = is_array($params) ? $params : [];
                    return true;
                })
            )
            ->willReturn(1);

        $result = $driver->incr('pg_counter');

        $this->assertSame(1, $result);
        $this->assertNotEmpty($capturedParams);
        $this->assertArrayHasKey('id', $capturedParams);
        $this->assertSame('pg_counter', $capturedParams['id']);
    }

    public function testIncrGcpSpannerUsesCastAndPassesParamWithoutColon(): void
    {
        [$driver, $persistence] = $this->createDriverWithMockPersistence('gcp-spanner');

        $capturedParams = [];
        $persistence->expects($this->once())
            ->method('exec')
            ->with(
                $this->stringContains('INT64'),
                $this->callback(function ($params) use (&$capturedParams) {
                    $capturedParams = is_array($params) ? $params : [];
                    return true;
                })
            )
            ->willReturn(1);

        $result = $driver->incr('spanner_counter');

        $this->assertSame(1, $result);
        $this->assertNotEmpty($capturedParams);
        $this->assertArrayHasKey('id', $capturedParams);
        $this->assertParamKeysHaveNoLeadingColon($capturedParams);
    }

    public function testIncrThrowsWhenExecFails(): void
    {
        [$driver, $persistence] = $this->createDriverWithMockPersistence('mysql');

        $persistence->expects($this->once())
            ->method('exec')
            ->willThrowException(new \Exception('Connection lost'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Connection lost');

        $driver->incr('key');
    }

    // ------------------------- decr() -------------------------

    public function testDecrMysqlPassesParamIdWithoutLeadingColonAndReturnsAffectedRows(): void
    {
        [$driver, $persistence] = $this->createDriverWithMockPersistence('mysql');

        $capturedParams = [];
        $persistence->expects($this->once())
            ->method('exec')
            ->with(
                $this->stringContains('kv_value - 1'),
                $this->callback(function ($params) use (&$capturedParams) {
                    $capturedParams = is_array($params) ? $params : [];
                    return true;
                })
            )
            ->willReturn(1);

        $result = $driver->decr('counter_key');

        $this->assertSame(1, $result);
        $this->assertNotEmpty($capturedParams);
        $this->assertParamKeysHaveNoLeadingColon($capturedParams);
        $this->assertArrayHasKey('id', $capturedParams);
        $this->assertSame('counter_key', $capturedParams['id']);
    }

    public function testDecrPostgresqlUsesIntegerCastAndPassesParamWithoutColon(): void
    {
        [$driver, $persistence] = $this->createDriverWithMockPersistence('postgresql');

        $capturedParams = [];
        $persistence->expects($this->once())
            ->method('exec')
            ->with(
                $this->stringContains('kv_value::integer - 1'),
                $this->callback(function ($params) use (&$capturedParams) {
                    $capturedParams = is_array($params) ? $params : [];
                    return true;
                })
            )
            ->willReturn(1);

        $result = $driver->decr('pg_counter');

        $this->assertSame(1, $result);
        $this->assertNotEmpty($capturedParams);
        $this->assertArrayHasKey('id', $capturedParams);
        $this->assertSame('pg_counter', $capturedParams['id']);
    }

    public function testDecrGcpSpannerUsesCastAndPassesParamWithoutColon(): void
    {
        [$driver, $persistence] = $this->createDriverWithMockPersistence('gcp-spanner');

        $capturedParams = [];
        $persistence->expects($this->once())
            ->method('exec')
            ->with(
                $this->stringContains('INT64'),
                $this->callback(function ($params) use (&$capturedParams) {
                    $capturedParams = is_array($params) ? $params : [];
                    return true;
                })
            )
            ->willReturn(1);

        $result = $driver->decr('spanner_counter');

        $this->assertSame(1, $result);
        $this->assertNotEmpty($capturedParams);
        $this->assertArrayHasKey('id', $capturedParams);
        $this->assertParamKeysHaveNoLeadingColon($capturedParams);
    }

    public function testDecrThrowsWhenExecFails(): void
    {
        [$driver, $persistence] = $this->createDriverWithMockPersistence('mysql');

        $persistence->expects($this->once())
            ->method('exec')
            ->willThrowException(new \Exception('Constraint violation'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Constraint violation');

        $driver->decr('key');
    }

    public function testDecrReturnsZeroWhenNoRowsAffected(): void
    {
        [$driver, $persistence] = $this->createDriverWithMockPersistence('mysql');

        $persistence->expects($this->once())
            ->method('exec')
            ->willReturn(0);

        $result = $driver->decr('nonexistent');

        $this->assertSame(0, $result);
    }
}

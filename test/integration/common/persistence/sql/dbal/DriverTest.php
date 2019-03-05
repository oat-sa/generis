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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\generis\test\integration\common\persistence\sql\dbal;


use common_persistence_sql_Platform;
use common_persistence_SqlPersistence;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use oat\generis\test\TestCase;

class TestDbalDriver extends \common_persistence_sql_dbal_Driver  {
    public function setDriverManagerClass($class)
    {
        parent::setDriverManagerClass($class);
    }
}

class TestDbalDriverManager
{
    private static $connection;
    private static $try = 0;
    private static $allowed = 1;

    /**
     * @param $params
     * @param $conf
     * @return mixed
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function getConnection($params, $conf) {
        if (!self::$try || self::$try > self::$allowed) {
            self::$try++;
            throw new DBALException('Testing');
        }
        return self::$connection;
    }

    public static function setConnection($connection) {
        self::$try = 0;
        self::$connection = $connection;
    }

    public static function setLimit($limit)
    {
        self::$try = 0;
        self::$allowed = $limit;
    }
}

/**
 * @package oat\generis\test\unit\common\persistence\sql\dbal
 * @author  Aleh Hutnikau, <hutnikau@1pt.com>
 */
class DriverTest extends TestCase
{
    public function testReconnectionOnException()
    {
        $driver = new TestDbalDriver();

        $connectionMock = $this->prophesize(Connection::class);

        TestDbalDriverManager::setConnection($connectionMock->reveal());
        $driver->setDriverManagerClass(TestDbalDriverManager::class);

        $connection = $driver->connect('test_connection', [
            'driver' => 'pdo_sqlite',
            'user' => null,
            'password' => null,
            'host' => null,
            'dbname' => ':memory:',
        ]);
        $this->assertInstanceOf(common_persistence_SqlPersistence::class, $connection);
        $platform = $driver->getPlatForm();
        $this->assertInstanceOf(common_persistence_sql_Platform::class, $platform);
    }

    /**
     * @expectedException  \Doctrine\DBAL\DBALException
     * @expectedExceptionMessage Testing
     */
    public function testMaxAttemptsToConnect()
    {
        $driver = new TestDbalDriver();

        $connectionMock = $this->prophesize(Connection::class);

        TestDbalDriverManager::setConnection($connectionMock->reveal());
        TestDbalDriverManager::setLimit(0);
        $driver->setDriverManagerClass(TestDbalDriverManager::class);

        $driver->connect('test_connection', [
            'driver' => 'pdo_sqlite',
            'user' => null,
            'password' => null,
            'host' => null,
            'dbname' => ':memory:',
        ]);
    }
}

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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\generis\test\common\persistence;

use \PHPUnit_Framework_TestCase as TestCase;
use common_persistence_PhpRedisDriver as PhpRedisDriver;

class PhpRedisDriverTest extends TestCase
{
    public function testSet()
    {
        $connectionMock = $this->getMockBuilder(\Redis::class)->getMock();
        $connectionMock->method('set')->willReturn(true);

        $driver = new PhpRedisDriver();
        $reflectionDriver = new \ReflectionClass(PhpRedisDriver::class);

        $reflectionConnection = $reflectionDriver->getProperty('connection');
        $reflectionConnection->setAccessible(true);
        $reflectionConnection->setValue($driver, $connectionMock);

        $reflectionParams = $reflectionDriver->getProperty('params');
        $reflectionParams->setAccessible(true);
        $reflectionParams->setValue($driver, ['attempt' => 1]);

        $this->assertTrue($driver->set('foo', 'bar'));
    }

    /**
     * @expectedException \oat\oatbox\persistence\WriteException
     * @expectedExceptionMessage Can't write into redis storage.
     */
    public function testSetException()
    {
        $connectionMock = $this->getMockBuilder(\Redis::class)->getMock();
        $connectionMock->method('set')->willReturn(false);

        $driver = new PhpRedisDriver();
        $reflectionDriver = new \ReflectionClass(PhpRedisDriver::class);

        $reflectionConnection = $reflectionDriver->getProperty('connection');
        $reflectionConnection->setAccessible(true);
        $reflectionConnection->setValue($driver, $connectionMock);

        $reflectionParams = $reflectionDriver->getProperty('params');
        $reflectionParams->setAccessible(true);
        $reflectionParams->setValue($driver, ['attempt' => 1]);

        $driver->set('foo', 'bar');
    }
}

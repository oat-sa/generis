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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\generis\test;

use Prophecy\Argument;
use Zend\ServiceManager\ServiceLocatorInterface;
use common_persistence_Manager;
use common_persistence_sql_dbal_Driver;
use PHPUnit\Framework\TestCase as UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

abstract class TestCase extends UnitTestCase
{

    /**
     * @param array $services
     * @return ServiceLocatorInterface
     */
    public function getServiceLocatorMock(array $services = [])
    {
        $serviceLocatorProphecy = $this->prophesize(ServiceLocatorInterface::class);
        foreach ($services as $key => $service) {
            $serviceLocatorProphecy->get($key)->willReturn($service);
            $serviceLocatorProphecy->has($key)->willReturn(true);
        }
        $serviceLocatorProphecy->has(Argument::any())->willReturn(false);

        return $serviceLocatorProphecy->reveal();
    }

    /**
     * Returns a persistence Manager with a mocked sql persistence
     *
     * @param string $key identifier of the persistence
     * @return common_persistence_Manager
     */
    public function getSqlMock($key)
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('sqlite not found, tests skipped.');
        }
        $driver = new common_persistence_sql_dbal_Driver();
        $persistence = $driver->connect($key, ['connection' => ['url' => 'sqlite:///:memory:']]);
        $pmProphecy = $this->prophesize(common_persistence_Manager::class);
        $pmProphecy->setServiceLocator(Argument::any())->willReturn(null);
        $pmProphecy->getPersistenceById($key)->willReturn($persistence);

        return $pmProphecy->reveal();
    }

    /**
     * Forward compatibility function for PHPUnit 5.4+
     *
     * Returns a test double for the specified class.
     *
     * @param string $originalClassName
     * @return MockObject
     * @throws \PHPUnit_Framework_Exception
     * @since Method available since Release 5.4.0
     */
    protected function createMock($originalClassName): MockObject
    {
        return $this->getMockBuilder($originalClassName)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->getMock();
    }

    /**
     * Forward compatibility function for PHPUnit 5.4+
     *
     * Returns a partial test double for the specified class.
     *
     * @param string $originalClassName
     * @param array $methods
     * @return MockObject
     * @since Method available since Release 5.4.0
     */
    protected function createPartialMock($originalClassName, array $methods = []): MockObject
    {
        return $this->getMockBuilder($originalClassName)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->setMethods($methods)
            ->getMock();
    }
}

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
 * Copyright (c) 2018-2021 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\generis\test;

use stdClass;
use Prophecy\Argument;
use Psr\Container\ContainerInterface;
use oat\oatbox\service\ServiceManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as UnitTestCase;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class TestCase extends UnitTestCase
{
    use SqlMockTrait;

    /**
     * @param array $services
     * @return ServiceLocatorInterface|ServiceManager
     */
    public function getServiceLocatorMock(array $services = [])
    {
        /** @var ContainerInterface $containerProphecy */
        $containerProphecy = $this->prophesize(ContainerInterface::class);

        /** @var ServiceManager $serviceLocatorProphecy */
        $serviceLocatorProphecy = $this->prophesize(ServiceManager::class);
        $serviceLocatorProphecy->getContainer()->willReturn($containerProphecy);

        foreach ($services as $key => $service) {
            $serviceLocatorProphecy->get($key)->willReturn($service);
            $serviceLocatorProphecy->has($key)->willReturn(true);

            $containerProphecy->get($key)->willReturn($service);
            $containerProphecy->has($key)->willReturn(true);
        }

        $serviceLocatorProphecy->has(Argument::any())->willReturn(false);
        $containerProphecy->has(Argument::any())->willReturn(false);

        return $serviceLocatorProphecy->reveal();
    }

    public function createIteratorMock(string $originalClassName, array $items = []): MockObject
    {
        $iteratorData = new stdClass();
        $iteratorData->array = $items;
        $iteratorData->position = 0;

        $iteratorMock = $this->createMock($originalClassName);

        $iteratorMock
            ->expects($this->any())
            ->method('rewind')
            ->will(
                $this->returnCallback(
                    static function() use ($iteratorData) {
                        $iteratorData->position = 0;
                    }
                )
            );

        $iteratorMock
            ->expects($this->any())
            ->method('current')
            ->will(
                $this->returnCallback(
                    static function() use ($iteratorData) {
                        return $iteratorData->array[$iteratorData->position];
                    }
                )
            );

        $iteratorMock
            ->expects($this->any())
            ->method('key')
            ->will(
                $this->returnCallback(
                    static function() use ($iteratorData) {
                        return $iteratorData->position;
                    }
                )
            );

        $iteratorMock
            ->expects($this->any())
            ->method('next')
            ->will(
                $this->returnCallback(
                    static function() use ($iteratorData) {
                        $iteratorData->position++;
                    }
                )
            );

        $iteratorMock
            ->expects($this->any())
            ->method('valid')
            ->will(
                $this->returnCallback(
                    static function() use ($iteratorData) {
                        return isset($iteratorData->array[$iteratorData->position]);
                    }
                )
            );

        $iteratorMock
            ->expects($this->any())
            ->method('count')
            ->will(
                $this->returnCallback(
                    static function() use ($iteratorData) {
                        return sizeof($iteratorData->array);
                    }
                )
            );

        return $iteratorMock;
    }
}

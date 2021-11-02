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

namespace oat\generis\test;

use oat\oatbox\service\ServiceManager;
use Prophecy\Argument;
use Psr\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase as UnitTestCase;

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
}

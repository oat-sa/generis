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
 * Copyright (c) 2018-2022 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\generis\test;

use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use oat\oatbox\service\ServiceManager;
use PHPUnit\Framework\TestCase as UnitTestCase;

/**
 * @deprecated Use \PHPUnit\Framework\TestCase instead.
 *             To reduce number of dependencies, we must not use generis TestCase anymore, since SQL and ServiceLocator
 *             mocks can be used separately via specific traits.
 */
abstract class TestCase extends UnitTestCase
{
    use SqlMockTrait;

    /**
     * @deprecated Use \oat\generis\test\ServiceManagerMockTrait::getServiceManagerMock() instead.
     *             Since PHPUnit does all the work, we no longer have to use Prophecy to reduce dependencies.
     *
     * @param array<string, object> $services
     *
     * @return ServiceManager
     */
    public function getServiceLocatorMock(array $services = [])
    {
        /** @var ContainerInterface|ObjectProphecy $containerProphecy */
        $containerProphecy = $this->prophesize(ContainerInterface::class);

        /** @var ServiceManager|ObjectProphecy $serviceLocatorProphecy */
        $serviceLocatorProphecy = $this->prophesize(ServiceManager::class);
        $serviceLocatorProphecy
            ->getContainer()
            ->willReturn($containerProphecy);

        foreach ($services as $key => $service) {
            $serviceLocatorProphecy
                ->get($key)
                ->willReturn($service);
            $serviceLocatorProphecy
                ->has($key)
                ->willReturn(true);

            $containerProphecy
                ->get($key)
                ->willReturn($service);
            $containerProphecy
                ->has($key)
                ->willReturn(true);
        }

        $serviceLocatorProphecy
            ->has(Argument::any())
            ->willReturn(false);
        $containerProphecy
            ->has(Argument::any())
            ->willReturn(false);

        return $serviceLocatorProphecy->reveal();
    }

    /**
     * @deprecated Use PHPUnit mocks instead.
     *             Since PHPUnit does all the work, we no longer have to use Prophecy to reduce dependencies.
     */
    protected function prophesize($classOrInterface = null): ObjectProphecy
    {
        return parent::prophesize($classOrInterface);
    }
}

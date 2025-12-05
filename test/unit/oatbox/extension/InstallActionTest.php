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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\generis\test\unit\oatbox\extension;

use oat\generis\test\ServiceManagerMockTrait;
use oat\oatbox\event\EventManager;
use oat\oatbox\extension\InstallAction;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class InstallActionTest extends TestCase
{
    use ServiceManagerMockTrait;

    public function testRegisterEvent(): void
    {
        $event = 'testEvent';
        $callBack = function () {
        };

        $instance = $this->getMockForAbstractClass(
            InstallAction::class,
            [],
            '',
            false,
            false,
            true,
            ['getServiceLocator', 'getServiceManager']
        );

        $eventManager = $this->createMock(EventManager::class);
        $eventManager
            ->method('attach')
            ->with($event, $callBack);

        $serviceManagerMock = $this->getServiceManagerMock([
            EventManager::SERVICE_ID => $eventManager,
        ]);
        $serviceManagerMock
            ->method('register')
            ->with(EventManager::SERVICE_ID, $eventManager);

        $instance
            ->expects($this->once())
            ->method('getServiceLocator')
            ->willReturn($serviceManagerMock);

        $instance
            ->expects($this->once())
            ->method('getServiceManager')
            ->willReturn($serviceManagerMock);

        $instance->registerEvent($event, $callBack);
    }

    public function testRegisterService(): void
    {
        $fixtureService = 'test/service';

        $instance = $this->getMockForAbstractClass(
            InstallAction::class,
            [],
            '',
            false,
            false,
            true,
            ['getServiceManager']
        );

        $serviceManagerMock = $this->getServiceManagerMock();

        $configurableServiceMock = $this->createMock(ConfigurableService::class);
        $configurableServiceMock
            ->method('setServiceLocator')
            ->with($serviceManagerMock)
            ->willReturn($configurableServiceMock);

        $serviceManagerMock
            ->method('register')
            ->with($fixtureService, $configurableServiceMock);

        $instance
            ->expects($this->exactly(1))
            ->method('getServiceManager')
            ->willReturn($serviceManagerMock);

        $instance->registerService($fixtureService, $configurableServiceMock);
    }
}

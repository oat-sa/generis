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
use oat\oatbox\extension\UninstallAction;
use oat\oatbox\service\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class UninstallActionTest extends TestCase
{
    use ServiceManagerMockTrait;

    public function testUnregisterEvent(): void
    {
        $event = 'testEvent';
        $callBack = function () {
        };

        $instance = $this->getMockForAbstractClass(
            UninstallAction::class,
            [],
            '',
            false,
            false,
            true,
            ['getServiceLocator', 'getServiceManager']
        );

        $eventManager = $this->createMock(EventManager::class);
        $eventManager
            ->expects($this->once())
            ->method('detach')
            ->with($event, $callBack);

        $serviceManager = $this->getServiceManagerMock([
            EventManager::SERVICE_ID => $eventManager,
        ]);
        $serviceManager
            ->expects($this->once())
            ->method('register')
            ->with(EventManager::SERVICE_ID, $eventManager);

        $instance
            ->expects($this->once())
            ->method('getServiceLocator')
            ->willReturn($serviceManager);

        $instance
            ->expects($this->once())
            ->method('getServiceManager')
            ->willReturn($serviceManager);

        $instance->unregisterEvent($event, $callBack);
    }

    public function testUnregisterService(): void
    {
        $fixtureService = 'test/service';

        $instance = $this->getMockForAbstractClass(
            UninstallAction::class,
            [],
            '',
            false,
            false,
            true,
            ['getServiceManager']
        );

        $serviceManagerMock = $this->getServiceManagerMock();
        $serviceManagerMock
            ->expects($this->once())
            ->method('unregister')
            ->with($fixtureService);

        $instance
            ->expects($this->once())
            ->method('getServiceManager')
            ->willReturn($serviceManagerMock);

        $instance->unregisterService($fixtureService);
    }
}

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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\generis\test\unit\oatbox\event;

use oat\generis\test\MockObject;
use oat\generis\test\TestCase;
use oat\oatbox\event\EventAggregator;
use oat\oatbox\event\EventManager;
use oat\oatbox\event\GenericEvent;
use oat\oatbox\log\LoggerService;
use oat\oatbox\service\ServiceManager;
use Psr\Log\LoggerInterface;

class EventAggregatorTest extends TestCase
{
    /** @var EventManager|MockObject */
    private $eventManager;

    /** @var ServiceManager|MockObject */
    private $serviceLocator;

    /** @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    protected function setUp(): void
    {
        $this->eventManager = $this->createMock(EventManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->serviceLocator = $this->getServiceLocatorMock(
            [
                EventManager::SERVICE_ID => $this->eventManager,
                LoggerService::SERVICE_ID => $this->logger,
            ]
        );
    }

    public function testTriggerEventsWhenNumberOfAggregatedEventsRichesTheMaximum(): void
    {
        $this->eventManager->expects($this->atLeast(2))
            ->method('trigger');
        $this->mockDebugLogger('Triggering 2 aggregated events');

        $eventAggregator = new EventAggregator(['numberOfAggregatedEvents'=>2]);
        $eventAggregator->setServiceLocator($this->serviceLocator);

        $eventAggregator->put('event1', new GenericEvent('wwwww'));
        $eventAggregator->put('event2', new GenericEvent('wwwww'));
    }

    public function testDoNotTriggerEventsWhenNumberOfAggregatedEventsIsLessThanTheMaximum(): void
    {
        $this->eventManager->expects($this->never())
            ->method('trigger');
        $this->logger->expects($this->never())->method('info');

        $eventAggregator = new EventAggregator(['numberOfAggregatedEvents'=>4]);
        $eventAggregator->setServiceLocator($this->serviceLocator);

        $eventAggregator->put('event1', new GenericEvent('wwwww'));
    }

    public function testTriggerAggregatedEvents(): void
    {
        $this->eventManager->expects($this->once())
            ->method('trigger');
        $this->mockDebugLogger('Triggering 1 aggregated events');

        $eventAggregator = new EventAggregator(['numberOfAggregatedEvents'=>3]);
        $eventAggregator->setServiceLocator($this->serviceLocator);

        $eventAggregator->put('event1', new GenericEvent('wwwww'));
        $eventAggregator->triggerAggregatedEvents();
    }

    private function mockDebugLogger(string $message): void
    {
        $this->logger->expects($this->once())
            ->method('info')
            ->with($message);
    }
}

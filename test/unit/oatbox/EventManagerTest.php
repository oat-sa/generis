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
 * Copyright (c) (original work) 2015-2022 Open Assessment Technologies SA
 *
 */

namespace oat\generis\test\unit\oatbox;

use oat\generis\test\ServiceManagerMockTrait;
use oat\oatbox\event\EventManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prediction\CallTimesPrediction;
use oat\oatbox\event\GenericEvent;

class CallableListener
{
    public function invoke($event): void
    {
    }
}

class ServiceListener
{
    public static $called = false;

    public function invoke($event): void
    {
        self::$called = true;
    }
}

class EventManagerTest extends TestCase
{
    use ServiceManagerMockTrait;

    /** @var EventManager */
    private $eventManager;

    public function setUp(): void
    {
        $this->eventManager = new EventManager();

        $this->eventManager->setServiceLocator(
            $this->getServiceManagerMock(
                [
                    ServiceListener::class => new ServiceListener()
                ]
            )
        );
    }

    public function testAttachedListenerIsInvokedWhenEventIsTriggered(): void
    {
        $callable = $this->prophesize(CallableListener::class);
        $callable->invoke(Argument::any())->should(new CallTimesPrediction(1));
        
        $this->eventManager->attach('testEvent', [$callable->reveal(), 'invoke']);
        $this->eventManager->trigger('testEvent');
    }
    
    public function testListenerIsInvokedWhenAttachedToMultipleEvents(): void
    {
        $callable = $this->prophesize(CallableListener::class);
        $callable->invoke(Argument::any())->should(new CallTimesPrediction(2));

        $this->eventManager->attach(['testEvent1','testEvent2'], [$callable->reveal(), 'invoke']);

        $this->eventManager->trigger('testEvent1');
        $this->eventManager->trigger('testEvent2');
    }

    public function testServiceIsInvokedFromContainer(): void
    {
        $event = new GenericEvent('test', ['param1' => '2']);

        $this->eventManager->attach($event->getName(), [ServiceListener::class, 'invoke']);
        $this->eventManager->trigger($event);

        $this->assertTrue(ServiceListener::$called);
    }

    public function testDetachedListenerIsNotInvoked(): void
    {
        $callable = $this->prophesize(CallableListener::class);

        $callable->invoke(Argument::any())->should(new CallTimesPrediction(0));
        $listener = $callable->reveal();

        $this->eventManager->attach(['testEvent'], [$listener, 'invoke']);

        $this->eventManager->detach(['testEvent'], [$listener, 'invoke']);

        $this->eventManager->trigger('testEvent');
    }
}

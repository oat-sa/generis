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
 * Copyright (c) (original work) 2015 Open Assessment Technologies SA
 *
 */

namespace oat\generis\test\unit\oatbox;

use oat\oatbox\event\EventManager;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceManager;
use Prophecy\Argument;
use Prophecy\Prediction\CallTimesPrediction;
use oat\oatbox\event\GenericEvent;
use oat\generis\test\TestCase;

class EmptyClass
{
    public function testfunction($event)
    {
    }
    public function testfunction2($event)
    {
    }
    public function testfunction3($event)
    {
    }
}

class EmptyClassService extends ConfigurableService
{
    public static $called = false;

    public function testfunction($event)
    {
        self::$called = true;
    }
}

class EventManagerTest extends TestCase
{
    public function testInit()
    {
        $eventManager = new EventManager();

        $config = new \common_persistence_KeyValuePersistence([], new \common_persistence_InMemoryKvDriver());
        $eventManager->setServiceLocator(new ServiceManager($config));

        $this->assertInstanceOf(EventManager::class, $eventManager);
        
        return $eventManager;
        //no cleanup required, not persisted
    }

    /**
     * @depends testInit
     */
    public function testAttachOne($eventManager)
    {
        $callable = $this->prophesize(EmptyClass::class);
        $callable->testfunction(Argument::any())->should(new CallTimesPrediction(1));
        
        $eventManager->attach('testEvent', [$callable->reveal(), 'testfunction']);
        $eventManager->trigger('testEvent');
    }
    
    /**
     * @depends testInit
     */
    public function testAttachMultiple($eventManager)
    {
        $callable = $this->prophesize(EmptyClass::class);
        $callable->testfunction(Argument::any())->should(new CallTimesPrediction(2));
    
        $eventManager->attach(['testEvent1','testEvent2'], [$callable->reveal(), 'testfunction']);
        $eventManager->trigger('testEvent1');
        $eventManager->trigger('testEvent2');
        $eventManager->trigger('testEvent3');
    }
    
    /**
     * @depends testInit
     */
    public function testTriggerEventObj($eventManager)
    {
        $genericEvent = new GenericEvent('objEvent', ['param1' => '1']);

        $callable = $this->prophesize(EmptyClass::class);
        $callable->testfunction($genericEvent)->should(new CallTimesPrediction(1));


        $eventManager->attach($genericEvent->getName(), [$callable->reveal(), 'testfunction']);
        $eventManager->trigger($genericEvent);

        $genericEvent2 = new GenericEvent('objEvent2', ['param1' => '2']);
        $eventManager->attach($genericEvent2->getName(), [EmptyClassService::class, 'testfunction']);
        $eventManager->trigger($genericEvent2);
        $this->assertTrue(EmptyClassService::$called);
    }


    /**
     * @depends testInit
     */
    public function testDetach($eventManager)
    {
        $callable = $this->prophesize(EmptyClass::class);

        $callable->testfunction(Argument::any())->should(new CallTimesPrediction(1));
        $callable->testfunction2(Argument::any())->should(new CallTimesPrediction(1));
        $callable->testfunction3(Argument::any())->should(new CallTimesPrediction(1));
        $revelation = $callable->reveal();

        $eventManager->attach(['testEvent'], [$revelation, 'testfunction']);
        $eventManager->attach(['testEvent'], [$revelation, 'testfunction2']);
        $eventManager->attach(['testEvent'], [$revelation, 'testfunction3']);

        $eventManager->trigger('testEvent');

        $eventManager->detach(['testEvent'], [$revelation, 'testfunction']);
        $eventManager->detach(['testEvent'], [$revelation, 'testfunction2']);
        $eventManager->detach(['testEvent'], [$revelation, 'testfunction3']);

        $eventManager->trigger('testEvent');
    }
}

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
use Prophecy\Argument;
use Prophecy\Prediction\CallTimesPrediction;
use oat\oatbox\event\GenericEvent;
use oat\generis\test\TestCase;

class EmptyClass
{
    public function testfunction($event) {
        
    }
    public function testfunction2($event) {

    }
    public function testfunction3($event) {

    }
}

class EventManagerTest extends TestCase
{
    public function testInit()
    {
        $eventManager = new EventManager();
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
        
        $eventManager->attach('testEvent', array($callable->reveal(), 'testfunction'));
        $eventManager->trigger('testEvent');
    }
    
    /**
     * @depends testInit
     */
    public function testAttachMultiple($eventManager)
    {
        $callable = $this->prophesize(EmptyClass::class);
        $callable->testfunction(Argument::any())->should(new CallTimesPrediction(2));
    
        $eventManager->attach(array('testEvent1','testEvent2'), array($callable->reveal(), 'testfunction'));
        $eventManager->trigger('testEvent1');
        $eventManager->trigger('testEvent2');
        $eventManager->trigger('testEvent3');
    }
    
    /**
     * @depends testInit
     */
    public function testTriggerEventObj($eventManager)
    {
        $genericEvent = new GenericEvent('objEvent', array('param1' => '1'));
        
        $callable = $this->prophesize(EmptyClass::class);
        $callable->testfunction($genericEvent)->should(new CallTimesPrediction(1));
        
    
        $eventManager->attach($genericEvent->getName(), array($callable->reveal(), 'testfunction'));
        $eventManager->trigger($genericEvent);
    }
    
    /**
     * @depends testInit
     */
    public function testDetatch($eventManager)
    {
        $callable = $this->prophesize(EmptyClass::class);

        $callable->testfunction(Argument::any())->should(new CallTimesPrediction(1));
        $callable->testfunction2(Argument::any())->should(new CallTimesPrediction(1));
        $callable->testfunction3(Argument::any())->should(new CallTimesPrediction(1));
        $revelation = $callable->reveal();

        $eventManager->attach(array('testEvent'), array($revelation, 'testfunction'));
        $eventManager->attach(array('testEvent'), array($revelation, 'testfunction2'));
        $eventManager->attach(array('testEvent'), array($revelation, 'testfunction3'));

        $eventManager->trigger('testEvent');

        $eventManager->detach(array('testEvent'), array($revelation, 'testfunction'));
        $eventManager->detach(array('testEvent'), array($revelation, 'testfunction2'));
        $eventManager->detach(array('testEvent'), array($revelation, 'testfunction3'));

        $eventManager->trigger('testEvent');
    }
}
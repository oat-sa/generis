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

use oat\oatbox\event\EventManager;
use oat\oatbox\extension\UninstallAction;
use oat\oatbox\service\ServiceManager;
use oat\generis\test\TestCase;
/**
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class UninstallActionTest extends TestCase
{
    
     public function testUnregisterEvent() {
        
        $event    = 'testEvent';
        $callBack = function() {};
        
        $instance = $this->getMockForAbstractClass(
                UninstallAction::class,
                [], '', false, false, true, 
                ['getServiceLocator' , 'getServiceManager']
                );
        
        $prophetServiceManager = $this->prophesize(ServiceManager::class);
        $prophetEventManager   = $this->prophesize(EventManager::class);
        
        $prophetEventManager->detach($event , $callBack)->willReturn(null);
        
        $EventManagerMock      = $prophetEventManager->reveal();
        
        $prophetServiceManager->get(EventManager::CONFIG_ID)->willReturn($EventManagerMock);
        $prophetServiceManager->register(EventManager::CONFIG_ID , $EventManagerMock)->willReturn(null);
        
        $serviceManagerMock    = $prophetServiceManager->reveal();
        
        $instance->expects($this->once())
                ->method('getServiceLocator')
                ->willReturn($serviceManagerMock);
        
        $instance->expects($this->once())
                ->method('getServiceManager')
                ->willReturn($serviceManagerMock);
        
        $instance->unregisterEvent($event , $callBack);
        
    }
    
    public function testUnregisterService() {
        
        $fixtureService = 'test/service';
        
        $instance = $this->getMockForAbstractClass(
                UninstallAction::class,
                [], '', false, false, true, 
                ['getServiceManager']
                );
        
        $prophetServiceManager = $this->prophesize(ServiceManager::class);
        $prophetServiceManager->unregister($fixtureService)->willReturn(null);
        $serviceManagerMock    = $prophetServiceManager->reveal();
        
        $instance->expects($this->once())
                ->method('getServiceManager')
                ->willReturn($serviceManagerMock);
        
        $instance->unregisterService($fixtureService);
    }
    
}

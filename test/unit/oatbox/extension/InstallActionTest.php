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
use oat\oatbox\extension\InstallAction;
use oat\oatbox\service\ServiceManager;
use oat\generis\test\TestCase;
/**
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class InstallActionTest extends TestCase
{
    
    public function testRegisterEvent() {
        
        $event    = 'testEvent';
        $callBack = function() {};
        
        $instance = $this->getMockForAbstractClass(
                InstallAction::class,
                [], '', false, false, true, 
                ['getServiceLocator' , 'getServiceManager']
                );
        
        $prophetServiceManager = $this->prophesize(ServiceManager::class);
        $prophetEventManager   = $this->prophesize(EventManager::class);
        
        $prophetEventManager->attach($event , $callBack)->willReturn(null);
        
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
        
        $instance->registerEvent($event , $callBack);
        
    }
    
    public function testRegisterService() {
        
        $fixtureService = 'test/service';
        
        $instance = $this->getMockForAbstractClass(
                InstallAction::class,
                [], '', false, false, true, 
                ['getServiceManager']
                );
        
        $serviceProphet = $this->prophesize()->willExtend(\oat\oatbox\service\ConfigurableService::class);
        
        $prophetServiceManager = $this->prophesize(ServiceManager::class);
        $serviceManagerMock    = $prophetServiceManager->reveal();
        
        $serviceProphet->setServiceLocator($serviceManagerMock)->willReturn($serviceProphet);
        $serviceMock    = $serviceProphet->reveal();
        
        $prophetServiceManager->register($fixtureService , $serviceMock)->willReturn(null);
        $serviceManagerMock    = $prophetServiceManager->reveal();
        
        $instance->expects($this->exactly(1))
                ->method('getServiceManager')
                ->willReturn($serviceManagerMock);
        
        $instance->registerService($fixtureService , $serviceMock);
    }
    
}

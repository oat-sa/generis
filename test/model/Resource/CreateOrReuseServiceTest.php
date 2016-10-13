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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA
 * 
 */

namespace oat\generis\test\model\Resource;

/**
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class CreateOrReuseServiceTest extends \oat\tao\test\TaoPhpUnitTestRunner {
    
    public function testHasService() {
        
        $fixture  = 'media';
        
        $instance = $this->getMock(\oat\generis\model\Resource\CreateOrReuseService::class , ['hasOption']);
        
        $instance->expects($this->once())
                ->method('hasOption')
                ->with($fixture)
                ->willReturn(true);
        
        $this->assertTrue($instance->hasService($fixture));
                
    }
    
    public function testCreateService() {
        
        $fixture  = 'media';
      
        
        $MockService = $this->getMockForAbstractClass(\oat\generis\model\Resource\AbstractCreateOrReuse::class);
        
        $mockClassName  = get_class($MockService);
        
        $fixtureOptions = 
                [
                    'class' => $mockClassName,
                    'options' => ['toto' , 'titi' , 'tata'],
                ];
        
        $serviceManager = $this->prophesize(\oat\oatbox\service\ServiceManager::class)->reveal();
        
        $instance = $this->getMock(\oat\generis\model\Resource\CreateOrReuseService::class , ['hasOption' , 'getOption' , 'getServiceLocator']);
        
        $instance->expects($this->once())
                ->method('getServiceLocator')
                ->willReturn($serviceManager);
        
        $instance->expects($this->once())
                ->method('hasOption')
                ->with($fixture)
                ->willReturn(true);
        
        $instance->expects($this->once())
                ->method('getOption')
                ->with($fixture)
                ->willReturn($fixtureOptions);
        
        $this->assertInstanceOf($mockClassName, $this->invokeProtectedMethod($instance, 'createService' , [$fixture]));
    }
    
    public function testGetService() {
        $fixture  = 'media';

        $MockService = $this->getMockForAbstractClass(\oat\generis\model\Resource\AbstractCreateOrReuse::class);
        
        $instance = $this->getMock(\oat\generis\model\Resource\CreateOrReuseService::class , ['createService' ]);
        
        $instance->expects($this->once())->method('createService')->with($fixture)->willReturn($MockService);
        
        $this->assertSame($MockService , $instance->getService($fixture));
        $this->setInaccessibleProperty($instance, 'service', [$fixture => $MockService]);
        $this->assertSame($MockService , $instance->getService($fixture));
    }
    
}

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
 *
 */
namespace oat\generis\test\common\oatbox\service;

use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\service\ServiceNotFoundException;

class ServiceManagerTest  extends GenerisPhpUnitTestRunner
{
    /**
     * @var ServiceManager
     */
    protected $instance;

    protected function setUp()
    {
        $this->instance = new ServiceManager(null);
    }


    public function testGet() {

        $fixtureServiceName = 'tao/tao';

        $expectedService    = $this->prophesize(ConfigurableService::class);
        $expectedService->setServiceLocator($this->instance)->willReturn($expectedService);
        $expectedService    = $expectedService->reveal();

        $config = $this->prophesize(\common_persistence_PhpFileDriver::class);

        $configMock = $config->reveal();

        $config->get($fixtureServiceName)->willReturn($expectedService);

        $this->setInaccessibleProperty($this->instance , 'configService' , $configMock);

        $this->assertSame($expectedService , $this->instance->get($fixtureServiceName));

        $services = $this->getInaccessibleProperty($this->instance , 'services');
        $this->assertSame($expectedService , $services[$fixtureServiceName]);
    }

    public function testGetExistingService() {

        $fixtureServiceName = 'tao/tao';

        $expectedService    = $this->prophesize(ConfigurableService::class);

        $expectedService    = $expectedService->reveal();


        $this->setInaccessibleProperty($this->instance , 'services' , [$fixtureServiceName => $expectedService]);

        $this->assertSame($expectedService , $this->instance->get($fixtureServiceName));

    }

    public function testGetFailure() {

        $fixtureServiceName = 'tao/tao';

        $config = $this->prophesize(\common_persistence_PhpFileDriver::class);
        $config->get($fixtureServiceName)->willReturn(false);
        $configMock = $config->reveal();

        $this->setInaccessibleProperty($this->instance , 'configService' , $configMock);
        $this->setExpectedException(ServiceNotFoundException::class);
        $this->instance->get($fixtureServiceName);



    }


    public function testRegister() {
        
    }

    protected function tearDown() {
        $this->instance = null;
    }

}
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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\generis\test\unit\oatbox\service;

use oat\oatbox\service\ServiceManager;
use oat\oatbox\service\ConfigurableService;
use oat\generis\test\TestCase;

/**
 * Class ServiceManager
 * @package oat\generis\test\integration\oatbox\service
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class ServiceManagerTest extends TestCase
{
    public function testGet()
    {
        $config = new \common_persistence_KeyValuePersistence([], new \common_persistence_InMemoryKvDriver());
        $serviceManager = new ServiceManager($config);
        $serviceManager->register(TestServiceInterface1::SERVICE_ID, new TestService1());
        $serviceManager->register(TestService2_2::SERVICE_ID, new TestService2_2());
        $this->assertTrue($serviceManager->get(TestServiceInterface1::SERVICE_ID) instanceof TestService1);
        $this->assertTrue($serviceManager->get(TestServiceInterface1::class) instanceof TestService1);
        $this->assertTrue($serviceManager->get(TestService1::class) instanceof TestService1);
        $this->assertTrue($serviceManager->get(TestService2_2::class) instanceof TestService2_2);
        $this->assertTrue($serviceManager->get(TestService2_2::SERVICE_ID) instanceof TestService2_2);
    }
}

interface TestServiceInterface1
{
    const SERVICE_ID = 'test/TestService1';
}
class TestService1 extends ConfigurableService implements TestServiceInterface1{}
class TestService2 extends ConfigurableService {
    const SERVICE_ID = 'test/TestService2';
}
class TestService2_2 extends TestService2 {}
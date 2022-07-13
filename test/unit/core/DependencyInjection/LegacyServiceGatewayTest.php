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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA;
 *
 * @author Gabriel Felipe Soares <gabriel.felipe.soares@taotesting.com>
 */

declare(strict_types=1);

namespace oat\generis\test\unit\model\DependencyInjection;

use oat\generis\model\DependencyInjection\LegacyServiceGateway;
use oat\generis\test\TestCase;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\session\SessionService;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;

class LegacyServiceGatewayTest extends TestCase
{
    /** @var LegacyServiceGateway */
    private $subject;

    /** @var ServiceManager|MockObject */
    private $serviceManager;

    public function setUp(): void
    {
        $this->serviceManager = $this->createMock(ServiceManager::class);
        $this->subject = new LegacyServiceGateway($this->serviceManager);
    }

    public function testInvokeWithNull(): void
    {
        $this->assertSame($this->serviceManager, $this->subject->__invoke());
    }

    public function testInvokeWithServiceId(): void
    {
        $service = new stdClass();

        $this->serviceManager
            ->method('get')
            ->willReturn($service);

        $this->assertSame($service, $this->subject->__invoke('someId'));
    }

    public function testGet(): void
    {
        $service = new stdClass();

        $this->serviceManager
            ->method('get')
            ->willReturn($service);

        $this->assertSame($service, $this->subject->get('someId'));
    }

    public function testHas(): void
    {
        $this->serviceManager
            ->method('has')
            ->willReturn(true);

        $this->assertTrue($this->subject->has('someId'));
    }

    public function testHasWillReturnTrueForConfigurableService(): void
    {
        $this->serviceManager
            ->method('has')
            ->willReturn(false);

        $this->assertTrue($this->subject->has(SessionService::class));
    }
}

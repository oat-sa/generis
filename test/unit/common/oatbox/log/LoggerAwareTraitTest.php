<?php declare(strict_types=1);
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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 */
namespace oat\generis\test\unit\common\oatbox\log;

use oat\generis\test\MockObject;
use oat\generis\test\TestCase;
use oat\oatbox\log\LoggerAwareTrait;
use oat\oatbox\log\LoggerService;
use oat\oatbox\service\ConfigurableService;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoggerAwareTraitTest extends TestCase
{
    private $subject;

    /** @var ServiceLocatorAwareInterface|MockObject */
    private $serviceLocatorMock;

    /** @var LoggerService|MockObject
     */
    private $loggerServiceMock;

    protected function setUp(): void
    {
        $this->serviceLocatorMock = $this->createMock(ServiceLocatorInterface::class);
        $this->loggerServiceMock = $this->createMock(LoggerService::class);

        $this->subject = new class extends ConfigurableService { use LoggerAwareTrait; };

        $this->subject->setServiceLocator($this->serviceLocatorMock);
    }

    public function testGetLogger(): void
    {
        $this->serviceLocatorMock
            ->expects($this->once())
            ->method('get')
            ->with(LoggerService::SERVICE_ID)
            ->willReturn($this->loggerServiceMock);

        $this->loggerServiceMock
            ->expects($this->once())
            ->method('getLogger')
            ->with(null);

        $this->subject->getLogger();
    }

    public function testGetLoggerWithChannel(): void
    {
        $this->serviceLocatorMock
            ->expects($this->once())
            ->method('get')
            ->with(LoggerService::SERVICE_ID)
            ->willReturn($this->loggerServiceMock);

        $this->loggerServiceMock
            ->expects($this->once())
            ->method('getLogger')
            ->with('foo');

        $this->subject->getLogger('foo');
    }

    /**
     * @dataProvider logLevelProvider
     */
    public function testLog(string $logLevel): void
    {
        $methodName = 'log' . ucfirst($logLevel);
        $loggerMock = $this->createMock(LoggerInterface::class);

        $this->serviceLocatorMock
            ->expects($this->once())
            ->method('get')
            ->with(LoggerService::SERVICE_ID)
            ->willReturn($this->loggerServiceMock);

        $this->loggerServiceMock
            ->expects($this->exactly(2))
            ->method('getLogger')
            ->with(null)
            ->willReturn($loggerMock);

        $loggerMock
            ->expects($this->once())
            ->method($logLevel)
            ->with('test message', ['foo' => 'bar']);

        $this->subject->$methodName('test message', ['foo' => 'bar']);
    }

    /**
     * @dataProvider logLevelProvider
     */
    public function testLogWithChannel(string $logLevel): void
    {
        $methodName = 'log' . ucfirst($logLevel);
        $loggerMock = $this->createMock(LoggerInterface::class);

        $this->serviceLocatorMock
            ->expects($this->once())
            ->method('get')
            ->with(LoggerService::SERVICE_ID)
            ->willReturn($this->loggerServiceMock);

        $this->loggerServiceMock
            ->expects($this->exactly(2))
            ->method('getLogger')
            ->with('foo')
            ->willReturn($loggerMock);

        $loggerMock
            ->expects($this->once())
            ->method($logLevel)
            ->with('test message', ['foo' => 'bar']);

        $this->subject->$methodName('test message', ['foo' => 'bar'], 'foo');
    }


    public function logLevelProvider(): array
    {
        return [
            ['emergency'],
            ['alert'],
            ['critical'],
            ['error'],
            ['warning'],
            ['notice'],
            ['info'],
            ['debug'],
        ];
    }
}

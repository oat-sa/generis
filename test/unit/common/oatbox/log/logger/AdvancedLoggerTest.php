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
 */

declare(strict_types=1);

namespace oat\generis\test\unit\common\oatbox\log\logger;

use oat\oatbox\log\logger\AdvancedLogger;
use PHPUnit\Framework\TestCase;
use oat\oatbox\log\logger\extender\ContextExtenderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

class AdvancedLoggerTest extends TestCase
{
    /** @var MockObject|LoggerInterface */
    private $logger;

    /** @var AdvancedLogger */
    private $sut;

    /** @var ContextExtenderInterface|MockObject */
    private $contextExtender;

    public function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->contextExtender = $this->createMock(ContextExtenderInterface::class);
        $this->sut = new AdvancedLogger($this->logger);
        $this->sut->addContextExtender($this->contextExtender);
    }

    /**
     * @dataProvider levelDataProvider
     */
    public function testLog(string $level): void
    {
        $this->contextExtender
            ->method('extend')
            ->willReturn(
                [
                    'extended' => true,
                ]
            );

        $this->logger
            ->expects($this->once())
            ->method($level)
            ->with(
                'Error description',
                [
                    'extended' => true,
                ]
            );

        $this->sut->{$level}(
            'Error description',
            []
        );
    }

    public function levelDataProvider(): array
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

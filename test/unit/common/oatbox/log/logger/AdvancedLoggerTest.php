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

namespace oat\generis\test\unit\common\oatbox\log\logger;

use Exception;
use oat\oatbox\log\logger\AdvancedLogger;
use oat\generis\test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Throwable;

class AdvancedLoggerTest extends TestCase
{
    /** @var MockObject|LoggerInterface */
    private $logger;

    /** @var AdvancedLogger */
    private $sut;

    public function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->sut = new AdvancedLogger($this->logger);
    }

    public function testLog(): void
    {
        $exception = $this->createException();

        $this->logger
            ->method('critical')
            ->with('bla bla bla');

        $this->sut->critical(
            'Error description',
            [
                AdvancedLogger::CONTEXT_EXCEPTION => $exception
            ]
        );
    }

    private function createException(): Throwable
    {
        return new Exception(
            'Last error',
            200,
            new Exception(
                'Original error',
                100
            )
        );
    }
}

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
use oat\oatbox\session\SessionService;
use oat\oatbox\user\User;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Throwable;

class AdvancedLoggerTest extends TestCase
{
    /** @var MockObject|LoggerInterface */
    private $logger;

    /** @var AdvancedLogger */
    private $sut;

    /** @var SessionService|MockObject */
    private $sessionService;

    public function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->sessionService = $this->createMock(SessionService::class);
        $this->sut = new AdvancedLogger($this->logger, $this->sessionService);
        $this->sut->withServerData(
            [
                'SERVER_ADDR' => '127.0.0.1',
                'SERVER_NAME' => 'localhost',
                'REQUEST_URI' => '/my/endpoint',
                'REQUEST_METHOD' => 'POST',
            ]
        );
    }

    /**
     * @dataProvider levelDataProvider
     */
    public function testLog(string $level): void
    {
        $user = $this->createMock(User::class);
        $user->method('getIdentifier')
            ->willReturn('userUri');

        $this->sessionService
            ->method('getCurrentUser')
            ->willReturn($user);

        $this->logger
            ->expects($this->exactly(1))
            ->method($level)
            ->with(
                'Error description',
                [
                    'contextException' => '"Last error", code: 200, file: "'
                        . __FILE__ . '", line: 116, previous: "Original error", code: 100, file: "'
                        . __FILE__ . '", line: 119',
                    'contextRequestData' => [
                        'serverIp' => '127.0.0.1',
                        'serverName' => 'localhost',
                        'requestUri' => '/my/endpoint',
                        'requestMethod' => 'POST'
                    ],
                    'contextUserData' => [
                        'id' => 'userUri',
                    ],
                ]
            );

        $this->sut->{$level}(
            'Error description',
            [
                AdvancedLogger::CONTEXT_EXCEPTION => $this->createException(),
            ]
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

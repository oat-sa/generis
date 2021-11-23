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

namespace oat\generis\test\unit\common\oatbox\log\logger\extender;

use oat\oatbox\log\logger\AdvancedLogger;
use oat\generis\test\TestCase;
use oat\oatbox\log\logger\extender\ContextExtenderInterface;
use oat\oatbox\log\logger\extender\UserContextExtender;
use oat\oatbox\session\SessionService;
use oat\oatbox\user\User;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

class UserContextExtenderTest extends TestCase
{
    /** @var AdvancedLogger */
    private $sut;

    /** @var SessionService|MockObject */
    private $sessionService;

    public function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->sessionService = $this->createMock(SessionService::class);
        $this->sut = new UserContextExtender($this->sessionService);
    }

    public function testExtend(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getIdentifier')
            ->willReturn('userUri');

        $this->sessionService
            ->method('getCurrentUser')
            ->willReturn($user);

        $this->assertSame(
            [
                ContextExtenderInterface::CONTEXT_USER_DATA => [
                    'id' => 'userUri',
                ],
            ],
            $this->sut->extend(
                [
                    ContextExtenderInterface::CONTEXT_USER_DATA => [],
                ]
            )
        );
    }
}

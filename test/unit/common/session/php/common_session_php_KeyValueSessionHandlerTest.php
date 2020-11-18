<?php

declare(strict_types=1);

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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 *
 */

use oat\generis\test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class common_session_php_KeyValueSessionHandlerTest extends TestCase
{
    /** @var common_persistence_KeyValuePersistence|MockObject */
    private $persistence;

    protected function setUp(): void
    {
        $this->persistence = $this->createMock(common_persistence_KeyValuePersistence::class);
    }

    public function testWriteDefaultTtl(): void
    {
        $defaultTtl = (int)ini_get('session.gc_maxlifetime');

        $this->expectSetTtl($defaultTtl);

        $handler = $this->createHandler();

        $handler->write('id', 'data');
    }

    public function testWriteGivenTtl(): void
    {
        $this->expectSetTtl(10);

        $handler = $this->createHandler(10);

        $handler->write('id', 'data');
    }

    private function expectSetTtl(int $expected): void
    {
        $this->persistence
            ->expects($this->once())
            ->method('set')->with(
                $this->anything(),
                $this->anything(),
                $expected
            );
    }

    private function createHandler(?int $ttl = null): common_session_php_KeyValueSessionHandler
    {
        $handler = $this->getMockBuilder(common_session_php_KeyValueSessionHandler::class)
            ->setConstructorArgs(
                [
                    [
                        common_session_php_KeyValueSessionHandler::OPTION_SESSION_TTL => $ttl
                    ]
                ]
            )
            ->onlyMethods(['getPersistence'])
            ->getMock();

        $handler->method('getPersistence')->willReturn($this->persistence);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $handler;
    }
}

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
 */

namespace oat\generis\test\unit\common\oatbox\log\logger\handler;

use oat\oatbox\log\logger\handler\FluentdHandler;
use oat\generis\test\TestCase;
use Fluent\Logger\FluentLogger;

class FluentdHandlerTest extends TestCase
{
    public function testConstruct()
    {
        $logMessage = ['level' => 100, 'extra' => [], 'context' => [], 'message' => 'foo', 'channel' => 'tao'];
        $logger = $this->getMockBuilder(FluentLogger::class)
            ->getMock();
        $logger->expects($this->once())
            ->method('post');
        $handler = new FluentdHandler($logger);
        $handler->handle($logMessage);

        new FluentdHandler(FluentLogger::class, 100, true, ["127.0.0.1", 24224]);
    }

    public function testConstructThrowsTypeException()
    {
        $this->expectException(\TypeError::class);
        new FluentdHandler(FluentLogger::class . 'foo');
    }
}

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

use oat\generis\test\TestCase;
use oat\oatbox\log\logger\extender\ContextExtenderInterface;
use oat\oatbox\log\logger\extender\RequestContextExtender;

class RequestContextExtenderTest extends TestCase
{
    /** @var RequestContextExtender */
    private $sut;

    public function setUp(): void
    {
        $this->sut = new RequestContextExtender();
        $this->sut->withServerData(
            [
                'SERVER_ADDR' => '127.0.0.1',
                'SERVER_NAME' => 'localhost',
                'REQUEST_URI' => '/my/endpoint',
                'REQUEST_METHOD' => 'POST',
            ]
        );
    }

    public function testExtend(): void
    {
        $this->assertSame(
            [
                ContextExtenderInterface::CONTEXT_REQUEST_DATA => [
                    'serverIp' => '127.0.0.1',
                    'serverName' => 'localhost',
                    'requestUri' => '/my/endpoint',
                    'requestMethod' => 'POST'
                ],
            ],
            $this->sut->extend(
                [
                    ContextExtenderInterface::CONTEXT_REQUEST_DATA => [],
                ]
            )
        );
    }
}

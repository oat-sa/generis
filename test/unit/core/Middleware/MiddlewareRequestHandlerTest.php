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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\generis\test\unit\core\Middleware;

use oat\generis\model\Middleware\MiddlewareMap;
use oat\generis\model\Middleware\MiddlewareRequestHandler;
use oat\generis\test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\MiddlewareInterface;
use Relay\Relay;
use Relay\RelayBuilder;

class MiddlewareRequestHandlerTest extends TestCase
{
    /** @var MiddlewareRequestHandler */
    private $subject;

    /** @var MockObject|ContainerInterface */
    private $container;

    /** @var MockObject|ResponseInterface */
    private $originalResponse;

    /** @var MockObject|ServerRequestInterface */
    private $request;

    /** @var MockObject|RelayBuilder */
    private $relayBuilder;

    /** @var MockObject|UriInterface */
    private $uri;

    /** @var MockObject|Relay */
    private $relay;

    public function setUp(): void
    {
        $this->middlewareMap = [
            '/my/path1' => [
                MiddlewareMap::byRoute('/my/path1')
                    ->andMiddlewareId('middlewarePath1_2')
                    ->andMiddlewareId('middlewarePath1_2')
                    ->andHttpMethod('POST')
                    ->jsonSerialize()
            ],
            '/my/path2' => [
                MiddlewareMap::byRoute('/my/path2')
                    ->andMiddlewareId('middlewarePath2_1')
                    ->andHttpMethod('POST')
                    ->jsonSerialize()
            ]
        ];
        $this->container = $this->createMock(ContainerInterface::class);
        $this->relayBuilder = $this->createMock(RelayBuilder::class);
        $this->relay = $this->createMock(Relay::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->uri = $this->createMock(UriInterface::class);
        $this->originalResponse = $this->createMock(ResponseInterface::class);
        $this->subject = (new MiddlewareRequestHandler($this->container, $this->relayBuilder, $this->middlewareMap))
            ->withOriginalResponse($this->originalResponse);

        $this->request
            ->method('getUri')
            ->willReturn($this->uri);

        $this->request
            ->method('getMethod')
            ->willReturn('POST');

        $this->relay
            ->method('handle')
            ->with($this->request)
            ->willReturn($this->originalResponse);
    }

    public function testHandle(): void
    {
        $middlewarePath1_1 = $this->createMock(MiddlewareInterface::class);
        $middlewarePath1_2 = $this->createMock(MiddlewareInterface::class);

        $queue = [
            $middlewarePath1_1,
            $middlewarePath1_2,
            static function ($request, $next): ResponseInterface {
                return $this->originalResponse;
            }
        ];

        $this->container
            ->method('get')
            ->willReturnCallback(
                static function (string $middlewareId) use (
                    $middlewarePath1_1,
                    $middlewarePath1_2
                ): MiddlewareInterface {
                    $middlewares = [
                        'middlewarePath1_1' => $middlewarePath1_1,
                        'middlewarePath1_2' => $middlewarePath1_2,
                    ];

                    return $middlewares[$middlewareId];
                }
            );

        $this->uri
            ->method('getPath')
            ->willReturn('/my/path1');

        $this->relayBuilder
            ->expects($this->once())
            ->method('newInstance')
            ->with($queue)
            ->willReturn($this->relay);

        $this->subject->handle($this->request);
    }
}

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
 *
 * @author Gabriel Felipe Soares <gabriel.felipe.soares@taotesting.com>
 */

declare(strict_types=1);

namespace oat\generis\test\unit\core\Middleware;

use oat\generis\model\Middleware\MiddlewareMap;
use oat\generis\model\Middleware\MiddlewareRequestHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
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
        $this->container = $this->createMock(ContainerInterface::class);
        $this->relayBuilder = $this->createMock(RelayBuilder::class);
        $this->relay = $this->createMock(Relay::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->uri = $this->createMock(UriInterface::class);
        $this->originalResponse = $this->createMock(ResponseInterface::class);
        $this->subject = new MiddlewareRequestHandler($this->container, $this->relayBuilder, $this->getMiddlewareMap());
        $this->subject->withOriginalResponse($this->originalResponse);

        $this->request
            ->method('getUri')
            ->willReturn($this->uri);

        $this->relay
            ->method('handle')
            ->with($this->request)
            ->willReturn($this->originalResponse);
    }

    /**
     * @dataProvider assertRouteProvider
     */
    public function testAssertRoute(string $path, string $httpMethod, array $middlewares): void
    {
        $middlewaresMocks = [];

        foreach ($middlewares as $middleware) {
            $middlewaresMocks[$middleware] = $this->createMock(MiddlewareInterface::class);
        }

        $queue = array_merge(
            array_values($middlewaresMocks),
            [
                static function ($request, $next): ResponseInterface {
                    return $this->originalResponse;
                }
            ]
        );

        $this->container
            ->expects($this->exactly(count($middlewares)))
            ->method('get')
            ->willReturnCallback(
                static function (string $middlewareId) use ($middlewaresMocks): MiddlewareInterface {
                    return $middlewaresMocks[$middlewareId];
                }
            );

        $this->uri
            ->expects($this->once())
            ->method('getPath')
            ->willReturn($path);

        $this->request
            ->expects($this->once())
            ->method('getMethod')
            ->willReturn($httpMethod);

        $this->relayBuilder
            ->expects($this->once())
            ->method('newInstance')
            ->with($queue)
            ->willReturn($this->relay);

        $this->assertSame($this->originalResponse, $this->subject->handle($this->request));
    }

    public function assertRouteProvider(): array
    {
        return [
            /**
             * PATH 1: Multiple middlewares for different HTTP methods
             */
            [
                '/my/path1',
                'POST',
                [
                    'middlewarePath1_1',
                    'middlewarePath1_2',
                    'middlewarePath1_4',
                ]
            ],
            /**
             * PATH 2: Dynamic path with optional segment
             */
            [
                '/my/path2/foo',
                'POST',
                [
                    'middlewarePath2_1',
                    'middlewarePath2_2',
                ]
            ],
            /**
             * PATH 3: Dynamic path with multiple segment
             */
            [
                '/my/path3/user/1',
                'POST',
                [
                    'middlewarePath3_1',
                    'middlewarePath3_2',
                ]
            ]
        ];
    }

    /**
     * @dataProvider assertNoRouteProvider
     */
    public function testAssertNoRoute(string $path, string $httpMethod): void
    {
        $queue = array_merge(
            [
                static function ($request, $next): ResponseInterface {
                    return $this->originalResponse;
                }
            ]
        );

        $this->container
            ->expects($this->never())
            ->method('get');

        $this->uri
            ->expects($this->once())
            ->method('getPath')
            ->willReturn($path);

        $this->request
            ->expects($this->once())
            ->method('getMethod')
            ->willReturn($httpMethod);

        $this->relayBuilder
            ->expects($this->once())
            ->method('newInstance')
            ->with($queue)
            ->willReturn($this->relay);

        $this->assertSame($this->originalResponse, $this->subject->handle($this->request));
    }

    public function assertNoRouteProvider(): array
    {
        return [
            /**
             * PATH 1: Multiple middlewares for different HTTP methods
             */
            [
                '/my/path1/a',
                'POST'
            ],
            [
                '/my/path1',
                'DELETE'
            ],
            /**
             * PATH 2: Dynamic path with optional segment
             */
            [
                '/my/path2/foo/bar',
                'POST'
            ],
            /**
             * PATH 3: Dynamic path with multiple segment
             */
            [
                '/my/path3/user2/1',
                'POST'
            ]
        ];
    }

    private function getMiddlewareMap(): array
    {
        return [
            /**
             * PATH 1: Multiple middlewares for different HTTP methods
             */
            '/^(POST)\/my\/path1$/' => [
                MiddlewareMap::byRoute('/my/path1')
                    ->andMiddlewareId('middlewarePath1_1')
                    ->andHttpMethod('POST')
                    ->jsonSerialize(),
            ],
            '/^(PUT|POST)\/my\/path1$/' => [
                MiddlewareMap::byRoute('/my/path1')
                    ->andMiddlewareId('middlewarePath1_2')
                    ->andHttpMethod('PUT')
                    ->andHttpMethod('POST')
                    ->jsonSerialize(),
            ],
            '/^(GET)\/my\/path1$/' => [
                MiddlewareMap::byRoute('/my/path1')
                    ->andMiddlewareId('middlewarePath1_3')
                    ->andHttpMethod('GET')
                    ->jsonSerialize()
            ],
            '/^(GET|POST)\/my\/path1$/' => [
                MiddlewareMap::byRoute('/my/path1')
                    ->andMiddlewareId('middlewarePath1_4')
                    ->andHttpMethod('GET')
                    ->andHttpMethod('POST')
                    ->jsonSerialize()
            ],
            /**
             * PATH 2: Dynamic path with optional segment
             */
            '/^(POST)\/my\/path2\/[a-z]{0,}$/' => [
                MiddlewareMap::byRoute('/my/path2/[a-z]{0,}')
                    ->andMiddlewareId('middlewarePath2_1')
                    ->andHttpMethod('POST')
                    ->jsonSerialize()
            ],
            '/^(.*)\/my\/path2\/?[a-z]{0,}$/' => [
                MiddlewareMap::byRoute('/my/path2/?[a-z]{0,}')
                    ->andMiddlewareId('middlewarePath2_2')
                    ->jsonSerialize()
            ],
            /**
             * PATH 3: Dynamic path with multiple segment
             */
            '/^(POST)\/my\/path3\/[a-z]{0,}\/[0-9]{0,}$/' => [
                MiddlewareMap::byRoute('/my/path3/[a-z]{0,}/[0-9]{0,}')
                    ->andMiddlewareId('middlewarePath3_1')
                    ->andHttpMethod('POST')
                    ->jsonSerialize()
            ],
            '/^(.*)\/my\/path3\/[a-z]{0,}\/?[0-9]{0,}$/' => [
                MiddlewareMap::byRoute('/my/path3/[a-z]{0,}/?[0-9]{0,}')
                    ->andMiddlewareId('middlewarePath3_2')
                    ->jsonSerialize()
            ]
        ];
    }
}

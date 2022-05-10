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

use LogicException;
use oat\generis\model\Middleware\MiddlewareMap;
use PHPUnit\Framework\TestCase;

class MiddlewareMapTest extends TestCase
{
    public function testGetters(): void
    {
        $map = MiddlewareMap::byRoute('/foo/bar')
            ->andRoute('/foo/bar/1')
            ->andHttpMethod('POST')
            ->andHttpMethod('GET')
            ->andMiddlewareId('middleware1')
            ->andMiddlewareId('middleware2');

        $jsonContent = [
            'routes' =>
                [
                    '/foo/bar',
                    '/foo/bar/1',
                ],
            'httpMethods' =>
                [
                    'POST',
                    'GET',
                ],
            'middlewares' =>
                [
                    'middleware1',
                    'middleware2',
                ],
        ];

        $this->assertSame(['POST', 'GET'], $map->getHttpMethods());
        $this->assertSame(['/foo/bar', '/foo/bar/1'], $map->getRoutes());
        $this->assertSame(['middleware1', 'middleware2'], $map->getMiddlewaresIds());
        $this->assertEquals($jsonContent, $map->jsonSerialize());
        $this->assertEquals($map, MiddlewareMap::fromJson($jsonContent));
    }

    public function testByRoute(): void
    {
        $map = MiddlewareMap::byRoute('/foo/bar');

        $this->assertSame(['/foo/bar'], $map->getRoutes());
    }

    public function testByRoutes(): void
    {
        $map = MiddlewareMap::byRoutes(['/foo/bar', '/foo/bar/1']);

        $this->assertSame(['/foo/bar', '/foo/bar/1'], $map->getRoutes());
    }

    public function testByMiddlewareId(): void
    {
        $map = MiddlewareMap::byMiddlewareId('middleware1');

        $this->assertSame(['middleware1'], $map->getMiddlewaresIds());
    }

    public function testByMiddlewareIds(): void
    {
        $map = MiddlewareMap::byMiddlewareIds(['middleware1', 'middleware2']);

        $this->assertSame(['middleware1', 'middleware2'], $map->getMiddlewaresIds());
    }

    public function testValidateHttpMethod(): void
    {
        $this->expectExceptionMessage('Invalid HTTP method "GO" for middleware map');
        $this->expectException(LogicException::class);

        MiddlewareMap::byMiddlewareId('middleware1')->andHttpMethod('GO');
    }

    public function testValidateFromJson(): void
    {
        $this->expectExceptionMessage('Wrong middleware json: ["wrong"]');
        $this->expectException(LogicException::class);

        MiddlewareMap::fromJson(['wrong']);
    }
}

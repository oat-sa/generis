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

use common_ext_Extension;
use oat\generis\model\Middleware\MiddlewareConfigInterface;
use oat\generis\model\Middleware\MiddlewareExtensionsMapper;
use oat\generis\model\Middleware\MiddlewareMap;
use oat\oatbox\extension\Manifest;
use PHPUnit\Framework\TestCase;

class MiddlewareExtensionMapperTest extends TestCase
{
    /** @var MiddlewareExtensionsMapper */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new MiddlewareExtensionsMapper();
    }

    public function testMap(): void
    {
        $map = $this->subject->map(
            [
                $this->createExtension([MiddlewareConfigMock::class])
            ]
        );

        $this->assertSame(
            [
                '/^(POST|GET)\/foo\/bar$/',
                '/^(POST|GET)\/foo\/bar\/[0-9]{0,}$/',
                '/^(.*)\/foo\/bar$/',
                '/^(.*)\/foo\/bar\/[0-9]{0,}$/',
                '/^(DELETE)\/foo\/bar$/',
                '/^(DELETE)\/foo\/bar\/[0-9]{0,}$/'
            ],
            array_keys($map)
        );

        $this->assertSame(
            [
                'middleware1',
                'middleware2',
            ],
            $map['/^(POST|GET)\/foo\/bar$/'][0]['middlewares']
        );
        $this->assertSame(
            [
                'middleware1',
                'middleware2',
            ],
            $map['/^(POST|GET)\/foo\/bar\/[0-9]{0,}$/'][0]['middlewares']
        );
        $this->assertSame(
            [
                'middleware3',
            ],
            $map['/^(.*)\/foo\/bar$/'][0]['middlewares']
        );
        $this->assertSame(
            [
                'middleware1',
            ],
            $map['/^(.*)\/foo\/bar$/'][1]['middlewares']
        );
        $this->assertSame(
            [
                'middleware3',
            ],
            $map['/^(.*)\/foo\/bar\/[0-9]{0,}$/'][0]['middlewares']
        );
        $this->assertSame(
            [
                'middleware1',
            ],
            $map['/^(.*)\/foo\/bar\/[0-9]{0,}$/'][1]['middlewares']
        );
        $this->assertSame(
            [
                'middleware2',
            ],
            $map['/^(DELETE)\/foo\/bar$/'][0]['middlewares']
        );
        $this->assertSame(
            [
                'middleware2',
            ],
            $map['/^(DELETE)\/foo\/bar\/[0-9]{0,}$/'][0]['middlewares']
        );
    }

    private function createExtension(array $middlewareConfigs): common_ext_Extension
    {
        $extension = $this->createMock(common_ext_Extension::class);
        $manifest = $this->createMock(Manifest::class);

        $extension->expects($this->once())
            ->method('getManifest')
            ->willReturn($manifest);

        $manifest->expects($this->once())
            ->method('getMiddlewares')
            ->willReturn($middlewareConfigs);

        return $extension;
    }
}

class MiddlewareConfigMock implements MiddlewareConfigInterface
{
    public function __invoke(): array
    {
        return [
            MiddlewareMap::byRoutes(['/foo/bar', '/foo/bar/[0-9]{0,}'])
                ->andHttpMethod('POST')
                ->andHttpMethod('GET')
                ->andMiddlewareId('middleware1')
                ->andMiddlewareId('middleware2'),
            MiddlewareMap::byRoutes(['/foo/bar', '/foo/bar/[0-9]{0,}'])
                ->andMiddlewareId('middleware3'),
            MiddlewareMap::byMiddlewareIds(['middleware1'])
                ->andRoute('/foo/bar')
                ->andRoute('/foo/bar/[0-9]{0,}'),
            MiddlewareMap::byMiddlewareIds(['middleware2'])
                ->andHttpMethod('DELETE')
                ->andRoute('/foo/bar')
                ->andRoute('/foo/bar/[0-9]{0,}'),

        ];
    }
}

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

declare(strict_types=1);

namespace oat\generis\test\unit\model\DependencyInjection;

use common_ext_Extension;
use common_ext_ExtensionsManager;
use oat\generis\model\DependencyInjection\ContainerBuilder;
use oat\generis\model\DependencyInjection\ContainerCache;
use oat\generis\model\DependencyInjection\ContainerServiceProviderInterface;
use oat\generis\test\TestCase;
use oat\oatbox\extension\Manifest;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;

class ContainerBuilderTest extends TestCase
{
    /** @var ContainerBuilder */
    private $subject;

    /** @var common_ext_ExtensionsManager|MockObject */
    private $extensionManager;

    /** @var ContainerCache|MockObject */
    private $cache;

    /** @var string */
    private $tempDir;

    public function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir();
        $this->extensionManager = $this->createMock(common_ext_ExtensionsManager::class);
        $this->cache = $this->createMock(ContainerCache::class);
        $this->subject = new ContainerBuilder(
            $this->tempDir,
            $this->tempDir,
            $this->extensionManager,
            true,
            $this->cache
        );
    }

    public function testBuildFromCache(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $this->cache
            ->expects($this->once())
            ->method('isFresh')
            ->willReturn(true);

        $this->cache
            ->expects($this->once())
            ->method('load')
            ->willReturn($container);

        $this->assertSame($container, $this->subject->build());
    }

    public function testForceBuild(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $mock = $this->createMock(ContainerServiceProviderInterface::class);

        $manifest = $this->createMock(Manifest::class);
        $manifest->method('getContainerServiceProvider')
            ->willReturn([get_class($mock)]);

        $extension = $this->createMock(common_ext_Extension::class);
        $extension->method('getManifest')
            ->willReturn($manifest);

        $this->extensionManager
            ->expects($this->once())
            ->method('getInstalledExtensions')
            ->willReturn([$extension]);

        $this->cache
            ->expects($this->once())
            ->method('forceLoad')
            ->willReturn($container);

        $this->assertSame($container, $this->subject->forceBuild());
    }
}

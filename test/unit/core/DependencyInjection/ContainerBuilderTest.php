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
 * Copyright (c) 2021-2022 (original work) Open Assessment Technologies SA;
 *
 * @author Gabriel Felipe Soares <gabriel.felipe.soares@taotesting.com>
 */

declare(strict_types=1);

namespace oat\generis\test\unit\model\DependencyInjection;

use common_ext_Extension;
use common_ext_ExtensionsManager;
use oat\generis\model\DependencyInjection\ContainerBuilder;
use oat\generis\model\DependencyInjection\ContainerCache;
use oat\generis\model\DependencyInjection\ContainerServiceProviderInterface;
use oat\generis\model\Middleware\MiddlewareExtensionsMapper;
use oat\oatbox\extension\Manifest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
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

    /** @var MiddlewareExtensionsMapper|MockObject */
    private $middlewareExtensionsMapper;

    /** @var MockObject|ContainerInterface */
    private $legacyContainer;

    /** @var string */
    private $installationDir;

    /** @var string */
    private $installationFile;

    public function setUp(): void
    {
        $this->extensionManager = $this->createMock(common_ext_ExtensionsManager::class);
        $this->middlewareExtensionsMapper = $this->createMock(MiddlewareExtensionsMapper::class);

        $this->legacyContainer = $this->createMock(ContainerInterface::class);
        $this->legacyContainer->method('get')
            ->willReturn($this->extensionManager);

        $this->tempDir = sys_get_temp_dir();
        $this->installationDir = $this->tempDir . '/generis';
        $this->installationFile = $this->installationDir . '/installation.conf.php';

        if (!file_exists($this->installationDir)) {
            mkdir($this->installationDir);
        }

        touch($this->installationFile);

        $this->cache = $this->createMock(ContainerCache::class);
        $this->subject = new ContainerBuilder(
            $this->tempDir,
            $this->legacyContainer,
            true,
            $this->cache,
            $this->middlewareExtensionsMapper,
            $this->tempDir
        );
    }

    public function testDoNotBuildIfApplicationIsNotInstalled(): void
    {
        unlink($this->installationFile);

        $this->assertSame($this->legacyContainer, $this->subject->build());
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

        $this->middlewareExtensionsMapper
            ->expects($this->once())
            ->method('map')
            ->willReturn([]);

        $this->cache
            ->expects($this->once())
            ->method('forceLoad')
            ->willReturn($container);

        $this->assertSame($container, $this->subject->forceBuild());
    }
}

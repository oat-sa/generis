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
 * @author Gabriel Felipe Soares <gabriel.felipe.soares@taotesting.com>
 */

declare(strict_types=1);

namespace oat\generis\test\unit\model\DependencyInjection;

use oat\generis\model\DependencyInjection\ContainerBuilder;
use oat\generis\model\DependencyInjection\ContainerCache;
use oat\generis\test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

class ContainerCacheTest extends TestCase
{
    /** @var ContainerCache */
    private $subject;

    /** @var ContainerBuilder|MockObject */
    private $containerBuilder;

    /** @var MockObject|PhpDumper */
    private $phpDumper;

    /** @var MockObject|ConfigCache */
    private $configCache;

    public function setUp(): void
    {
        $this->containerBuilder = $this->createMock(ContainerBuilder::class);
        $this->configCache = $this->createMock(ConfigCache::class);
        $this->phpDumper = $this->createMock(PhpDumper::class);
        $this->subject = new ContainerCache(
            __DIR__ . '/DummyCachedContainer.php',
            $this->containerBuilder,
            $this->configCache,
            $this->phpDumper,
            true,
            DummyCachedContainer::class
        );
    }

    public function testLoadFromCache(): void
    {
        $this->configCache->expects($this->once())
            ->method('isFresh')
            ->willReturn(true);

        $this->configCache->expects($this->never())
            ->method('write');

        $this->containerBuilder->expects($this->never())
            ->method('getResources');

        $this->containerBuilder->expects($this->never())
            ->method('compile');

        $this->phpDumper->expects($this->never())
            ->method('dump');

        $this->assertInstanceOf(DummyCachedContainer::class, $this->subject->load());
    }

    public function testForceLoad(): void
    {
        $cacheValue = '';
        $resources = [];

        $this->configCache->expects($this->once())
            ->method('write')
            ->with($cacheValue);

        $this->containerBuilder->expects($this->once())
            ->method('getResources')
            ->willReturn($resources);

        $this->containerBuilder->expects($this->once())
            ->method('compile')
            ->willReturn(false);

        $this->phpDumper->expects($this->once())
            ->method('dump')
            ->willReturn($cacheValue);

        $this->assertInstanceOf(DummyCachedContainer::class, $this->subject->forceLoad());
    }

    public function testIsFresh(): void
    {
        $this->configCache->expects($this->once())
            ->method('isFresh')
            ->willReturn(true);

        $this->assertTrue($this->subject->isFresh());
    }
}

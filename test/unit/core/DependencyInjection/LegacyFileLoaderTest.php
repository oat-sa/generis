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

use oat\generis\model\DependencyInjection\ContainerBuilder;
use oat\generis\model\DependencyInjection\LegacyFileLoader;
use oat\generis\test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\Alias;

class LegacyFileLoaderTest extends TestCase
{
    /** @var LegacyFileLoader */
    private $subject;

    /** @var ContainerBuilder|MockObject */
    private $containerBuilder;

    /** @var FileLocatorInterface|MockObject */
    private $fileLocator;

    public function setUp(): void
    {
        $this->containerBuilder = $this->createMock(ContainerBuilder::class);
        $this->fileLocator = $this->createMock(FileLocatorInterface::class);
        $this->subject = new LegacyFileLoader($this->containerBuilder, $this->fileLocator);
    }

    public function testLoad(): void
    {
        $alias = $this->createMock(Alias::class);

        $this->containerBuilder
            ->expects($this->exactly(1))
            ->method('setAlias')
            ->willReturn($alias);

        $this->containerBuilder
            ->expects($this->atLeast(2))
            ->method('setDefinition');

        $injectableService = '<?php
        use oat\oatbox\service\ServiceFactoryInterface;
        use Zend\ServiceManager\ServiceLocatorInterface;
        
        return new class implements ServiceFactoryInterface {
            public function __invoke(ServiceLocatorInterface $serviceLocator) {}
        };';

        $legacyService = '<?php
        return new \oat\oatbox\config\ConfigurationService();';

        $tmpDir = sys_get_temp_dir();
        file_put_contents($tmpDir . '/injectable.conf.php', $injectableService);
        file_put_contents($tmpDir . '/legacy.conf.php', $legacyService);

        $this->fileLocator
            ->expects($this->exactly(1))
            ->method('locate')
            ->willReturn($tmpDir);

        $this->assertNull($this->subject->load('*.conf.php'));
    }

    public function testSupports(): void
    {
        $this->assertTrue($this->subject->supports('something.conf.php'));
        $this->assertFalse($this->subject->supports('*.conf.php'));
    }
}

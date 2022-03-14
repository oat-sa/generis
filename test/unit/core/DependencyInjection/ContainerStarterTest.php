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
use oat\generis\model\DependencyInjection\ContainerStarter;
use oat\generis\test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;

class ContainerStarterTest extends TestCase
{
    /** @var ContainerStarter */
    private $subject;

    /** @var string */
    private $tempDir;

    /** @var MockObject|ContainerInterface */
    private $legacyContainer;

    public function setUp(): void
    {
        $this->legacyContainer = $this->createMock(ContainerInterface::class);

        $this->tempDir = sys_get_temp_dir();
        $this->subject = new ContainerStarter(
            $this->legacyContainer,
            $this->tempDir,
            $this->tempDir
        );
    }

    public function testGetContainerBuilder(): void
    {
        $this->assertInstanceOf(ContainerBuilder::class, $this->subject->getContainerBuilder());
    }
}

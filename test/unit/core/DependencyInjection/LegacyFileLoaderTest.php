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
        $this->markTestIncomplete('TODO');

        $this->subject->load();
    }

    public function testSupports(): void
    {
        $this->markTestIncomplete('TODO');

        $this->subject->supports();
    }
}

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

use common_ext_ExtensionsManager;
use oat\generis\model\DependencyInjection\ContainerBuilder;
use oat\generis\test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ContainerBuilderTest extends TestCase
{
    /** @var ContainerBuilder */
    private $subject;

    /** @var common_ext_ExtensionsManager|MockObject */
    private $extensionManager;

    public function setUp(): void
    {
        $this->extensionManager = $this->createMock(common_ext_ExtensionsManager::class);
        $this->subject = new ContainerBuilder('', '', $this->extensionManager);
    }

    public function testBuild(): void
    {
        $this->markTestIncomplete('TODO');

        $this->subject->build();
    }

    public function testForceBuild(): void
    {
        $this->markTestIncomplete('TODO');

        $this->subject->forceBuild();
    }
}

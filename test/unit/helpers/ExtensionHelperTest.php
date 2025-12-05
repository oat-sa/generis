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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\generis\test\unit\helpers;

use common_exception_Error;
use common_ext_Extension;
use helpers_ExtensionHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ExtensionHelperTest extends TestCase
{
    public function testSortByDependencies(): void
    {
        $ext1 = $this->mockExtension('ext1', []);
        $ext2 = $this->mockExtension('ext2', ['ext1']);
        $ext3 = $this->mockExtension('ext3', ['ext2']);
        $sorted = helpers_ExtensionHelper::sortByDependencies([$ext2,$ext3,$ext1]);
        $this->assertEquals([$ext1,$ext2,$ext3], array_values($sorted));
    }

    public function testCyclicDependencies(): void
    {
        $this->expectException(common_exception_Error::class);
        $ext1 = $this->mockExtension('ext1', ['ext2']);
        $ext2 = $this->mockExtension('ext2', ['ext3']);
        $ext3 = $this->mockExtension('ext3', ['ext1']);
        $sorted = helpers_ExtensionHelper::sortByDependencies([$ext2,$ext3,$ext1]);
    }

    public function testMissingDependencies(): void
    {
        $this->expectException(common_exception_Error::class);
        $ext1 = $this->mockExtension('ext1', []);
        $ext2 = $this->mockExtension('ext2', ['ext4']);
        $ext3 = $this->mockExtension('ext3', ['ext2','ext4']);

        helpers_ExtensionHelper::sortByDependencies([$ext2,$ext3,$ext1]);
    }

    /**
     * Mock an extension with its dependencies
     */
    private function mockExtension(string $id, array $dependencies = []): common_ext_Extension|MockObject
    {
        $ext = $this->createMock(common_ext_Extension::class);
        $ext
            ->method('getId')
            ->willReturn($id);
        $ext
            ->method('getDependencies')
            ->willReturn(array_fill_keys($dependencies, '>=0'));

        return $ext;
    }
}

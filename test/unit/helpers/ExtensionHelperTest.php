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

use oat\generis\test\TestCase;

class ExtensionHelperTest extends TestCase
{
    public function testSortByDependencies()
    {
        $ext1 = $this->mockExtension('ext1', []);
        $ext2 = $this->mockExtension('ext2', ['ext1']);
        $ext3 = $this->mockExtension('ext3', ['ext2']);
        $sorted = \helpers_ExtensionHelper::sortByDependencies([$ext2,$ext3,$ext1]);
        $this->assertEquals([$ext1,$ext2,$ext3], array_values($sorted));
    }

    /**
     * @expectedException common_exception_Error
     */
    public function testCyclicDependencies()
    {
        $ext1 = $this->mockExtension('ext0', ['ext1']);
        $ext1 = $this->mockExtension('ext1', ['ext2']);
        $ext2 = $this->mockExtension('ext2', ['ext3']);
        $ext3 = $this->mockExtension('ext3', ['ext1']);
        $sorted = \helpers_ExtensionHelper::sortByDependencies([$ext2,$ext3,$ext1]);
    }

    /**
     * @expectedException common_exception_Error
     */
    public function testMissingDependencies()
    {
        $ext1 = $this->mockExtension('ext1', []);
        $ext2 = $this->mockExtension('ext2', ['ext4']);
        $ext3 = $this->mockExtension('ext3', ['ext2','ext4']);
        $sorted = \helpers_ExtensionHelper::sortByDependencies([$ext2,$ext3,$ext1]);
    }

    /**
     * Moch an extension with its dependencies
     * @param string $id
     * @param array $dependencies
     * @return \common_ext_Extension::class
     */
    protected function mockExtension($id, $dependencies = [])
    {
        $prophet = $this->prophesize();
        $ext = $this->prophesize(\common_ext_Extension::class);
        $ext->getId()->willReturn($id);
        $ext->getDependencies()->willReturn(array_fill_keys($dependencies, '>=0'));
        return $ext->reveal();
    }
}

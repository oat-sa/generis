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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

declare(strict_types=1);

namespace oat\generis\test\unit\extension;

use oat\generis\test\TestCase;
use oat\oatbox\extension\ComposerInfo;
use oat\oatbox\extension\exception\ManifestException;

/**
 * Class ComposerInfoTest
 * @package oat\oatbox\extension
 */
class ComposerInfoTest extends TestCase
{
    public function testGetAvailableTaoExtensions()
    {
        $instance = new ComposerInfo($this->getSamplesDir());
        $this->assertEquals([
            'oat-sa/extension-tao-foobar' => 'taoFooBar',
            'oat-sa/extension-tao-taoItemBank' => 'taoItemBank'
        ], $instance->getAvailableTaoExtensions());
    }

    public function testExtractExtensionDependencies()
    {
        $instance = new ComposerInfo($this->getSamplesDir());
        $this->assertEquals(['taoItemBank' => '*'], $instance->extractExtensionDependencies());
    }

    public function testGetPackageId()
    {
        $instance = new ComposerInfo($this->getSamplesDir());
        $this->assertEquals('oat-sa/extension-tao-lightweight', $instance->getPackageId());
    }

    private function getSamplesDir()
    {
        return realpath(
            __DIR__ . DIRECTORY_SEPARATOR .
            '..' . DIRECTORY_SEPARATOR .
            '..' . DIRECTORY_SEPARATOR .
            'samples' .
            DIRECTORY_SEPARATOR .
            'manifests'
        );
    }
}

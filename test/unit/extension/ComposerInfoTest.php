<?php

declare(strict_types=1);

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
    public function testGetComposerJson()
    {
        $instance = new ComposerInfo();
        $composerJson = $instance->getComposerJson($this->getSamplesDir());
        $this->assertArrayHasKey('require', $composerJson);
        $this->assertEquals('oat-sa/extension-tao-lightweight', $composerJson['name']);
        $this->assertEquals('tao-extension', $composerJson['type']);
    }

    public function testGetComposerLock()
    {
        $instance = new ComposerInfo();
        $composerLock = $instance->getComposerLock($this->getSamplesDir());
        $this->assertArrayHasKey('packages', $composerLock);
    }

    public function testGetPackageInfo()
    {
        $instance = new ComposerInfo();
        $packageInfo = $instance->getPackageInfo('oat-sa/extension-tao-taoItemBank', $this->getSamplesDir());
        $this->assertArrayHasKey('version', $packageInfo);
        $this->assertEquals('oat-sa/extension-tao-taoItemBank', $packageInfo['name']);
    }

    public function testGetComposerJsonException()
    {
        $instance = new ComposerInfo();
        $this->expectException(ManifestException::class);
        $instance->getComposerJson('foo');
    }

    public function testGetComposerLockException()
    {
        $instance = new ComposerInfo();
        $this->expectException(ManifestException::class);
        $instance->getComposerLock('foo');
    }

    public function testGetPackageInfoException()
    {
        $instance = new ComposerInfo();
        $this->expectException(ManifestException::class);
        $instance->getPackageInfo('foo', $this->getSamplesDir());
    }

    private function getSamplesDir()
    {
        return realpath(__DIR__.DIRECTORY_SEPARATOR.
            '..'.DIRECTORY_SEPARATOR.
            '..'.DIRECTORY_SEPARATOR.
            'samples'.
            DIRECTORY_SEPARATOR.
            'manifests'
        );
    }


}
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

namespace oat\generis\test\integration\helpers;

use oat\generis\test\TestCase;

class FileTest extends TestCase
{
    private $rootDir;

    public function tearDown()
    {
        \tao_helpers_File::delTree($this->rootDir);
    }

    public function setUp()
    {
        $this->rootDir = uniqid(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test-file-inside-directory', true) . DIRECTORY_SEPARATOR;
        mkdir($this->rootDir);

        mkdir($this->rootDir . 'directoryname');
        mkdir($this->rootDir . 'FOO');
        touch($this->rootDir . 'directoryname/filename');
        touch($this->rootDir . 'secure_file');
    }

    public function fileInsideDirectoryDataProvider()
    {
        return [
            ['filename', 'directoryname', true],
            ['./filename', 'directoryname', true],
            ['../secure_file', 'directoryname', false],
            ['../directoryname/filename', 'directoryname', true],
            ['../directoryname/../FOO/../directoryname/filename', 'FOO/../directoryname', true],
            ['../FOO/../directoryname/filename', 'FOO/../directoryname', true],
            ['directoryname/../filename', 'directoryname', false],
            ['NON-EXISTING_FILE', 'directoryname', false],
            ['filename', 'NON-EXISTING_DIRECTORY', false],
        ];
    }

    /**
     * @dataProvider fileInsideDirectoryDataProvider
     */
    public function testIsFileInsideDirectory($filename, $directoryName, bool $isInside)
    {
        $this->assertEquals(\helpers_File::isFileInsideDirectory($filename, $this->rootDir . $directoryName), $isInside);
    }
}

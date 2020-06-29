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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\generis\test\unit\helpers;

use oat\generis\test\TestCase;

class FileTest extends TestCase
{
    private $dir;

    private $subDir;

    private $file1;

    private $file2;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "unittest" . mt_rand();
        mkdir($this->dir);
        $this->subDir = $this->dir . 'sub' ;
        mkdir($this->subDir);
        $this->file1 = $this->dir. DIRECTORY_SEPARATOR . 'test1.txt';
        touch($this->dir. DIRECTORY_SEPARATOR . 'test1.txt');
        $this->file2 = $this->subDir. DIRECTORY_SEPARATOR . 'test2.txt';
        touch($this->subDir. DIRECTORY_SEPARATOR . 'test2.txt');
    }

    public function testInDirectory()
    {
        $this->assertTrue(\helpers_File::isFileInsideDirectory('test1.txt', $this->dir));
        $this->assertFalse(\helpers_File::isFileInsideDirectory('test2.txt', $this->dir));
        $this->assertTrue(\helpers_File::isFileInsideDirectory('test1.txt', $this->dir.DIRECTORY_SEPARATOR));
        $this->assertFalse(\helpers_File::isFileInsideDirectory('sub'.DIRECTORY_SEPARATOR.'test2.txt', $this->dir));
        $this->assertFalse(\helpers_File::isFileInsideDirectory('sub'.DIRECTORY_SEPARATOR.'test2.txt', $this->dir.DIRECTORY_SEPARATOR));

        $this->assertFalse(\helpers_File::isFileInsideDirectory('test1.txt', $this->subDir));
        $this->assertFalse(\helpers_File::isFileInsideDirectory('test1.txt', $this->subDir.DIRECTORY_SEPARATOR));
        $this->assertTrue(\helpers_File::isFileInsideDirectory('test2.txt', $this->subDir));
        $this->assertFalse(\helpers_File::isFileInsideDirectory('sub'.DIRECTORY_SEPARATOR.'test2.txt', $this->subDir));
        $this->assertFalse(\helpers_File::isFileInsideDirectory('sub'.DIRECTORY_SEPARATOR.'test2.txt', $this->subDir.DIRECTORY_SEPARATOR));
    }

    protected function tearDown(): void
    {
        unlink($this->file1);
        unlink($this->file2);
        rmdir($this->subDir);
        rmdir($this->dir);
    }
}

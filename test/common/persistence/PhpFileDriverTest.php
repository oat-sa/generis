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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\generis\test\common\persistence;

use \PHPUnit_Framework_TestCase as TestCase;
use common_persistence_PhpFileDriver as PhpFileDriver;

class PhpFileDriverTest extends TestCase
{
    public function testSet()
    {
        $folder = \tao_helpers_File::createTempDir();
        $driver = new PhpFileDriver();
        $driver->connect('PhpFileDriverTest', ['dir' => $folder]);
        $this->assertTrue($driver->set('foo', 'bar'));
    }

    /**
     * @expectedException \oat\oatbox\persistence\WriteException
     * @expectedExceptionMessage Can't write into php file storage.
     */
    public function testSetException()
    {
        $key = 'foo';
        $folder = \tao_helpers_File::createTempDir();
        $driver = new PhpFileDriver();
        $driver->connect('PhpFileDriverTest', ['dir' => $folder]);

        $reflectionDriver = new \ReflectionClass(PhpFileDriver::class);
        $reflectionGetPath = $reflectionDriver->getMethod('getPath');
        $reflectionGetPath->setAccessible(true);
        $fileName = $reflectionGetPath->invoke($driver, $key);
        $dirName = dirname($fileName);
        mkdir($dirName, 0700, true);
        file_put_contents($fileName, 'bar');
        //make file non writable
        chmod($fileName, 0000);
        $driver->set($key, 'bar');
    }
}

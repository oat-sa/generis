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
 * Copyright (c) 2018  (original work) Open Assessment Technologies SA;
 *
 * @author Alexander Zagovorichev <zagovorichev@1pt.com>
 */

class PhpFileDriverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var common_persistence_PhpFileDriver
     */
    private $driver;

    protected function setUp()
    {
        $this->driver = new common_persistence_PhpFileDriver();
    }

    public function testGetFalse()
    {
        self::assertFalse($this->driver->get(false));
        self::assertFalse($this->driver->get(null));
        self::assertFalse($this->driver->get(''));
        self::assertFalse($this->driver->get('not existent'));
    }

    public function testGet()
    {
        $this->driver->connect(false, [
            'dir' => __DIR__ . '/samples/',
            'humanReadable' => true,
        ]);
        self::assertEquals('content is here', $this->driver->get('test'));
    }

    public function testTtlMode()
    {
        $this->driver->connect(false, [
            'dir' => __DIR__ . '/samples/',
            'humanReadable' => true,
            'ttlMode' => true,
        ]);
        self::assertEquals('Sword', $this->driver->get('testTtl'));
    }

    public function ttlFalseDataProvider ()
    {
        return [
            ['testTtlEmpty'],
            ['testTtlNull'],
            ['testTtlNoExpires'],
            ['testTtlExpired'],
        ];
    }

    /**
     * @dataProvider ttlFalseDataProvider
     */
    public function testFalseTtl($id)
    {
        $this->driver->connect(false, [
            'dir' => __DIR__ . '/samples/',
            'humanReadable' => true,
            'ttlMode' => true,
        ]);
        self::assertFalse($this->driver->get($id));
    }

}

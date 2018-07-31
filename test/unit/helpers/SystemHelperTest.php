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

use oat\generis\Helper\SystemHelper;
use oat\generis\test\TestCase;

class SystemHelperTest extends TestCase
{

    /**
     * @dataProvider sizeProvider
     */
    public function testConversion($phpString, $size)
    {
        $reflection = new \ReflectionClass(SystemHelper::class);
        $method = $reflection->getMethod('toBytes');
        $method->setAccessible(true);
        $bytes = $method->invokeArgs(null, array($phpString));
        $this->assertEquals($size, $bytes);
    }

    public function sizeProvider()
    {
        return [
            ['2k', 0x800],
            ['1m', 0x100000],
            ['1M', 0x100000],
            ['32M',0x2000000],
            ['1G', 0x40000000],
            ['0K', 0],
            ['3000k', 3000 * 1024],
            ['123', 123],
        ];
    }
}

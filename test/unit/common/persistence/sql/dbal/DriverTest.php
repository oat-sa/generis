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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\generis\test\unit\common\persistence\sql\dbal;

use oat\generis\test\TestCase;

/**
 * Class DriverTest
 * @package oat\generis\test\unit\common\persistence\sql\dbal
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class DriverTest extends TestCase
{

    public function testGetPlatForm()
    {
        $driver = new \common_persistence_sql_dbal_Driver();
        $driver->connect('test_connection', ['connection' => ['url' => 'sqlite:///:memory:']]);
        $this->assertTrue($driver->getPlatForm() instanceof \common_persistence_sql_Platform);
    }
}

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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\generis\test\unit\common\persistence;

use common_persistence_KeyValuePersistence;
use common_persistence_NoStorageKvDriver;
use PHPUnit\Framework\TestCase;

class NoStoragePersistenceTest extends TestCase
{
    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testConnect(): common_persistence_KeyValuePersistence
    {
        $driver = new common_persistence_NoStorageKvDriver();
        $persistence = $driver->connect('test', []);
        $this->assertInstanceOf('common_persistence_KeyValuePersistence', $persistence);

        return $persistence;
    }

    /**
     * @depends testConnect
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testSet(common_persistence_KeyValuePersistence $persistence): void
    {
        $this->assertFalse($persistence->set('fakeKeyName', 'value'));
    }

    /**
     * @depends testConnect
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGet(common_persistence_KeyValuePersistence $persistence): void
    {
        $this->assertFalse($persistence->get('fakeKeyName'));
    }


    /**
     * @depends testConnect
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testExists(common_persistence_KeyValuePersistence $persistence): void
    {
        $this->assertFalse($persistence->exists('fakeKeyName'));
    }

    /**
     * @depends testConnect
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testDel(common_persistence_KeyValuePersistence $persistence): void
    {
        $this->assertTrue($persistence->del('fakeKeyName'));
    }

    /**
     * @depends testConnect
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testPurge(common_persistence_KeyValuePersistence $persistence): void
    {
        $this->assertTrue($persistence->purge());
    }
}

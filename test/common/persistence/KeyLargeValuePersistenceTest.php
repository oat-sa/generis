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

namespace oat\generis\test\common\persistence;

class KeyLargeValuePersistenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \common_persistence_KeyLargeValuePersistence
     */
    protected $largeValuePersistence;

    public function setUp()
    {
        $this->largeValuePersistence = new \common_persistence_KeyLargeValuePersistence(
            array(
                \common_persistence_KeyLargeValuePersistence::VALUE_MAX_WIDTH => 100
            ),
            new \common_persistence_InMemoryAdvKvDriver()
        );
    }

    public function tearDown()
    {
        unset($this->largeValuePersistence);
    }

    protected function get100000bytesValue()
    {
        return str_repeat('a', 100000);
    }

    public function testSetGetLargeValue()
    {
        $bigValue = $this->get100000bytesValue();
        $this->largeValuePersistence->set('test', $bigValue);
        $this->assertEquals($bigValue, $this->largeValuePersistence->get('test'));
    }

    public function testDelExistsLarge()
    {
        $bigValue = $this->get100000bytesValue();
        $this->assertFalse($this->largeValuePersistence->exists('test'));
        $this->largeValuePersistence->set('test', $bigValue);
        $this->assertTrue($this->largeValuePersistence->exists('test'));
        $this->assertEquals($bigValue, $this->largeValuePersistence->get('test'));
        $this->assertTrue($this->largeValuePersistence->del('test'));
        $this->assertFalse($this->largeValuePersistence->exists('test'));
        $this->assertEmpty($this->largeValuePersistence->get('test'));
    }

    public function testSetGet()
    {
        $this->largeValuePersistence->set('test', 'fixture');
        $this->assertEquals('fixture', $this->largeValuePersistence->get('test'));
    }

    public function testDelExists()
    {
        $this->assertFalse($this->largeValuePersistence->exists('test'));
        $this->largeValuePersistence->set('test', 'fixture');
        $this->assertTrue($this->largeValuePersistence->exists('test'));
        $this->assertTrue($this->largeValuePersistence->del('test'));
        $this->assertFalse($this->largeValuePersistence->exists('test'));
        $this->assertEmpty($this->largeValuePersistence->get('test'));
    }

    public function testMapMapControl()
    {
        $this->largeValuePersistence = new \common_persistence_KeyLargeValuePersistence(
            array(
                \common_persistence_KeyLargeValuePersistence::VALUE_MAX_WIDTH => 100,
                \common_persistence_KeyLargeValuePersistence::MAP_IDENTIFIER => 'iamamap',
                \common_persistence_KeyLargeValuePersistence::START_MAP_DELIMITER => 'mapbegin',
                \common_persistence_KeyLargeValuePersistence::END_MAP_DELIMITER => 'mapend',

            ),
            new \common_persistence_InMemoryAdvKvDriver()
        );

        $this->testDelExistsLarge();
    }
}
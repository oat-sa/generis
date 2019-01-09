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

namespace oat\generis\test\unit\common\persistence;

use oat\generis\test\TestCase;

class AdvKeyValuePersistenceTest extends TestCase
{
    /**
     * @var \common_persistence_AdvKeyValuePersistence
     */
    protected $largeValuePersistence;

    public function setUp()
    {
        $this->largeValuePersistence = new \common_persistence_AdvKeyValuePersistence(
            array(
                \common_persistence_AdvKeyValuePersistence::MAX_VALUE_SIZE => 100
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

    /**
     */
    public function testLargeHmsetSetWithWrongSize()
    {
        $this->expectException(\common_Exception::class);
        $this->largeValuePersistence = new \common_persistence_AdvKeyValuePersistence(
            array(
                \common_persistence_AdvKeyValuePersistence::MAX_VALUE_SIZE => 'toto'
            ),
            new \common_persistence_InMemoryAdvKvDriver()
        );
        $this->largeValuePersistence->hSet('test', 'fixture', 'value');
    }

    public function testLargeHmsetHget()
    {
        $bigValue = $this->get100000bytesValue();
        $this->largeValuePersistence->hmSet('test', array(
            'fixture' => $bigValue,
            'fixture1' => 'value1',
            'fixture2' => $bigValue,
            'fixture3' => 'value3',
        ));

        $this->assertEquals($bigValue, $this->largeValuePersistence->hGet('test', 'fixture'));
        $this->assertEquals('value1', $this->largeValuePersistence->hGet('test', 'fixture1'));
        $this->assertEquals($bigValue, $this->largeValuePersistence->hGet('test', 'fixture2'));
        $this->assertEquals('value3', $this->largeValuePersistence->hGet('test', 'fixture3'));

        $this->largeValuePersistence->hSet('test', 'fixture', 'value');
        $this->largeValuePersistence->hSet('test', 'fixture1', $bigValue);
        $this->largeValuePersistence->hSet('test', 'fixture2', 'value2');
        $this->largeValuePersistence->hSet('test', 'fixture3', $bigValue);

        $this->assertEquals('value', $this->largeValuePersistence->hGet('test', 'fixture'));
        $this->assertEquals($bigValue, $this->largeValuePersistence->hGet('test', 'fixture1'));
        $this->assertEquals('value2', $this->largeValuePersistence->hGet('test', 'fixture2'));
        $this->assertEquals($bigValue, $this->largeValuePersistence->hGet('test', 'fixture3'));

        $this->assertTrue($this->largeValuePersistence->del('test'));
    }

    public function testHmsetHget()
    {
        $this->largeValuePersistence->hmSet('test', array(
            'fixture' => 'value',
            'fixture1' => 'value1',
            'fixture2' => 'value2',
            'fixture3' => 'value3',
        ));
        $this->assertEquals('value', $this->largeValuePersistence->hGet('test', 'fixture'));
        $this->assertEquals('value1', $this->largeValuePersistence->hGet('test', 'fixture1'));
        $this->assertEquals('value2', $this->largeValuePersistence->hGet('test', 'fixture2'));
        $this->assertEquals('value3', $this->largeValuePersistence->hGet('test', 'fixture3'));

        $this->assertTrue($this->largeValuePersistence->del('test'));
    }

    public function testHgetAllHexists()
    {
        $attributes = array(
            'fixture' => 'value',
            'fixture1' => 'value1',
            'fixture2' => 'value2',
            'fixture3' => 'value3',
        );

        $this->largeValuePersistence->hmSet('test', $attributes);

        $this->assertEquals('value', $this->largeValuePersistence->hGet('test', 'fixture'));
        $this->assertEquals('value1', $this->largeValuePersistence->hGet('test', 'fixture1'));
        $this->assertEquals('value2', $this->largeValuePersistence->hGet('test', 'fixture2'));
        $this->assertEquals('value3', $this->largeValuePersistence->hGet('test', 'fixture3'));

        $this->assertFalse($this->largeValuePersistence->hExists('test', 'none'));
        $this->assertTrue($this->largeValuePersistence->hExists('test', 'fixture'));

        $this->assertTrue($this->largeValuePersistence->hSet('test', 'none', 'noneValue'));
        $this->assertTrue($this->largeValuePersistence->hExists('test', 'none'));

        $this->assertEquals(
            array(
                'fixture' => 'value',
                'fixture1' => 'value1',
                'fixture2' => 'value2',
                'fixture3' => 'value3',
                'none' => 'noneValue',
            ),
            $this->largeValuePersistence->hGetAll('test')
        );

        $this->assertTrue($this->largeValuePersistence->del('test'));
    }

    public function testKeys()
    {
        $bigValue = $this->get100000bytesValue();
        $attributes = array(
            'fixture' => $bigValue,
            'fixture1' => 'value1',
            'fixture2' => $bigValue,
            'fixture3' => 'value3',
        );

        $this->largeValuePersistence->hmSet('test', $attributes);
        $this->largeValuePersistence->hmSet('test1', $attributes);
        $this->largeValuePersistence->hmSet('test2', $attributes);

        $this->assertEquals(['test','test1','test2'] , array_values($this->largeValuePersistence->keys('*')));

        $this->assertTrue($this->largeValuePersistence->del('test'));
        $this->assertTrue($this->largeValuePersistence->del('test1'));
        $this->assertTrue($this->largeValuePersistence->del('test2'));
    }

    public function testIncr()
    {
        $persist = $this->largeValuePersistence;
        $persist->set('testIncr0', 0);
        $persist->set('testIncr1', 0);

        $this->assertEquals(0, $persist->get('testIncr0'));
        $this->assertEquals(0, $persist->get('testIncr1'));
        $this->assertEquals(1, $persist->incr('testIncr0'));
        $this->assertEquals(1, $persist->get('testIncr0'));
        $this->assertEquals(0, $persist->get('testIncr1'));
        $this->assertEquals(2, $persist->incr('testIncr0'));
        $this->assertEquals(2, $persist->get('testIncr0'));
        $this->assertEquals(0, $persist->get('testIncr1'));

        $this->assertEquals(1, $persist->incr('testIncrUnset'));
        $this->assertEquals(1, $persist->get('testIncrUnset'));

        $this->assertEquals(true, $persist->set('testIncr3', 'a'));
        $this->expectException(\common_exception_InconsistentData::class);
        $persist->incr('testIncr3');
    }

    public function testDecr()
    {
        $persist = $this->largeValuePersistence;
        $persist->set('testDecr0', 1);
        $persist->set('testDecr1', 0);

        $this->assertEquals(1, $persist->get('testDecr0'));
        $this->assertEquals(0, $persist->get('testDecr1'));
        $this->assertEquals(0, $persist->decr('testDecr0'));
        $this->assertEquals(0, $persist->get('testDecr0'));
        $this->assertEquals(0, $persist->get('testDecr1'));
        $this->assertEquals(-1, $persist->decr('testDecr0'));
        $this->assertEquals(-1, $persist->get('testDecr0'));
        $this->assertEquals(0, $persist->get('testDecr1'));

        $this->assertEquals(-1, $persist->decr('testDecrUnset'));
        $this->assertEquals(-1, $persist->get('testDecrUnset'));

        $this->assertEquals(true, $persist->set('testDecr3', '-'));
        $this->expectException(\common_exception_InconsistentData::class);
        $persist->decr('testDecr3');
    }

    public function testMapMapControl()
    {
        $this->largeValuePersistence = new \common_persistence_AdvKeyValuePersistence(
            array(
                \common_persistence_KeyValuePersistence::MAX_VALUE_SIZE => 100,
                \common_persistence_KeyValuePersistence::MAP_IDENTIFIER => 'iamamap',
                \common_persistence_KeyValuePersistence::START_MAP_DELIMITER => 'mapbegin',
                \common_persistence_KeyValuePersistence::END_MAP_DELIMITER => 'mapend',

            ),
            new \common_persistence_InMemoryAdvKvDriver()
        );

        $this->testHgetAllHexists();
    }
}
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

namespace oat\generis\test\unit\config;

use oat\generis\test\TestCase;
use oat\oatbox\cache\KeyValueCache;
use oat\generis\persistence\PersistenceManager;
use oat\generis\test\KeyValueMockTrait;

class KeyValueCacheTest extends TestCase
{
    use KeyValueMockTrait;
    /**
     * @var KeyValueCache
     */
    protected $cache;

    /**
     * Configure registry instance
     */
    protected function setUp(): void
    {
        $this->cache = new KeyValueCache([KeyValueCache::OPTION_PERSISTENCE => 'unittest']);
        $serviceLocator = $this->getServiceLocatorMock([
            PersistenceManager::SERVICE_ID => $this->getKeyValueMock('unittest')
        ]);
        $this->cache->setServiceLocator($serviceLocator);
    }

    public function testGet()
    {
        $this->assertEquals(null, $this->cache->get('key1'));
        $this->assertEquals('def', $this->cache->get('key1', 'def'));
    }

    public function testSet()
    {
        $this->assertEquals(null, $this->cache->get('key1'));
        $this->assertEquals(true, $this->cache->set('key1', 'value1'));
        $this->assertEquals('value1', $this->cache->get('key1'));
        $this->assertEquals('value1', $this->cache->get('key1', 'otherValue'));
    }

    public function testDelete()
    {
        $this->assertEquals(12, $this->cache->get('key1', 12));
        $this->assertEquals(true, $this->cache->set('key1', 'value1'));
        $this->assertEquals('value1', $this->cache->get('key1'));
        $this->assertEquals(true, $this->cache->delete('key1'));
        $this->assertEquals(12, $this->cache->get('key1', 12));
    }

    public function testClear()
    {
        $this->assertEquals(true, $this->cache->set('key1', 'value1'));
        $this->assertEquals(true, $this->cache->set(12, 34));
        $this->assertEquals('value1', $this->cache->get('key1'));
        $this->assertEquals(34, $this->cache->get(12));
        $this->assertEquals(true, $this->cache->clear());
        $this->assertEquals(null, $this->cache->get('key1'));
        $this->assertEquals(null, $this->cache->get(12));
    }

    public function testHas()
    {
        $this->assertEquals(false, $this->cache->has('key1'));
        $this->assertEquals(false, $this->cache->has(12));
        $this->assertEquals(true, $this->cache->set('key1', 'value1'));
        $this->assertEquals(true, $this->cache->has('key1'));
        $this->assertEquals(true, $this->cache->set(12, 34));
        $this->assertEquals(true, $this->cache->has(12));
    }

    public function testMultiple()
    {
        $this->assertEquals(['1' => null, '2' => null], $this->cache->getMultiple(['1', '2']));
        $this->assertEquals(['1' => 'a', '2' => 'a'], $this->cache->getMultiple(['1', '2'], 'a'));
        $this->assertEquals(true, $this->cache->set('1', 'value1'));
        $this->assertEquals(['1' => 'value1', '2' => 'a'], $this->cache->getMultiple(['1', '2'], 'a'));
        $this->assertEquals(true, $this->cache->setMultiple(['2' => 'v2', '3' => 'v3']));
        $this->assertEquals(['1' => 'value1', '2' => 'v2', '3' => 'v3'], $this->cache->getMultiple(['1', '2', '3']));
        $this->assertEquals(true, $this->cache->deleteMultiple(['1', '3']));
        $this->assertEquals(['1' => 'x', '2' => 'v2', '3' => 'x'], $this->cache->getMultiple(['1', '2', '3'], 'x'));
        $this->assertEquals(false, $this->cache->has(1));
        $this->assertEquals(true, $this->cache->has(2));
        $this->assertEquals(false, $this->cache->has(3));
    }
}

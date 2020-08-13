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

namespace oat\generis\test;

use Psr\SimpleCache\CacheInterface;
use oat\oatbox\cache\KeyValueCache;
use oat\generis\persistence\PersistenceManager;

trait MockCacheTrait
{
    use MockServiceLocatorTrait;
    use KeyValueMockTrait;

    public function getCache(): CacheInterface
    {
        $cache = new class([KeyValueCache::OPTION_PERSISTENCE => 'cache_kv']) extends KeyValueCache {

            private $hits = 0;
            private $miss = 0;

            public function get($key, $default = null)
            {
                if ($this->has($key)) {
                    $this->hits++;
                } else {
                    $this->miss++;
                }
                return parent::get($key, $default);
            }
            
            public function getHits()
            {
                return $this->hits;
            }

            public function getMisses()
            {
                return $this->miss;
            }
        };
        $cache->setServiceLocator($this->getServiceLocatorMock([
            PersistenceManager::SERVICE_ID => $this->getKeyValueMock('cache_kv')
        ]));
        return $cache;
    }
}

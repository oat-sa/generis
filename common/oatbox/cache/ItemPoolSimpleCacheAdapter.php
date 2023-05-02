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
 */

declare(strict_types=1);

namespace oat\oatbox\cache;

use oat\oatbox\service\ConfigurableService;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;
use Throwable;

class ItemPoolSimpleCacheAdapter extends ConfigurableService implements CacheItemPoolInterface
{
    public const OPTION_CACHE_SERVICE = 'cacheService';
    /** @var CacheItemInterface[] */
    private $deferred;

    /**
     * @inheritdoc
     */
    public function getItem($key)
    {
        $item = $this->getCache()->get($key);

        if ($item) {
            $cacheItem = new CacheItem($key, true);
            $cacheItem->set($item);

            return $cacheItem;
        }

        return new CacheItem($key);
    }

    /**
     * @inheritdoc
     *
     * @return CacheItemInterface[]
     */
    public function getItems(array $keys = [])
    {
        $items = [];

        foreach ($keys as $key) {
            $items[] = $this->getItem($key);
        }

        return $items;
    }

    /**
     * @inheritdoc
     */
    public function hasItem($key)
    {
        return $this->getCache()->has($key);
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        return $this->getCache()->clear();
    }

    /**
     * @inheritdoc
     */
    public function deleteItem($key)
    {
        return $this->getCache()->delete($key);
    }

    /**
     * @inheritdoc
     */
    public function deleteItems(array $keys)
    {
        return $this->getCache()->deleteMultiple($keys);
    }

    /**
     * @inheritdoc
     */
    public function save(CacheItemInterface $item)
    {
        return $this->store($item);
    }

    /**
     * @inheritdoc
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->deferred[$item->getKey()] = $item;

        return true;
    }

    /**
     * @inheritdoc
     */
    public function commit()
    {
        foreach ($this->deferred as $item) {
            if (!$this->store($item)) {
                return false;
            }
        }

        return true;
    }

    private function getCache(): CacheInterface
    {
        if ($this->hasOption(self::OPTION_CACHE_SERVICE)) {
            return $this->getOption(self::OPTION_CACHE_SERVICE);
        }

        return $this->getServiceLocator()->get(SimpleCache::SERVICE_ID);
    }

    private function store(CacheItemInterface $item): bool
    {
        try {
            return $this->getCache()->set($item->getKey(), $item->get());
        } catch (Throwable $exception) {
            $this->getLogger()->error(
                sprintf(
                    'Cache value for %s key has not been saved. %s',
                    $item->getKey(),
                    $exception->getMessage()
                )
            );

            return false;
        }
    }
}

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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA
 */

declare(strict_types=1);

namespace oat\oatbox\cache;

use oat\oatbox\cache\factory\CacheItemPoolFactory;
use oat\oatbox\service\ConfigurableService;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class GcpTokenCacheItemPool extends ConfigurableService implements CacheItemPoolInterface
{
    public const SERVICE_ID = 'generis/GcpTokenCacheItemPool';

    public const OPTION_PERSISTENCE = 'persistence';
    public const OPTION_DISABLE_WRITE = 'disableWrite';
    public const OPTION_TOKEN_CACHE_KEY = 'tokenCacheKey';

    /** @var CacheItemPoolInterface */
    private $cache;

    /** @var bool */
    private $isWritingDisabled;

    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        $key = $this->getCacheKey();

        $this->log(__METHOD__, [$key]);

        $item = $this->getCache()->getItem($key);

        $this->unSerializeIfNecessary($item);

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = [])
    {
        $keys = [$this->getCacheKey()];

        $this->log(__METHOD__, $keys);

        $items = $this->getCache()->getItems($keys);

        foreach ($items as $item) {
            $this->unSerializeIfNecessary($item);
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key)
    {
        $key = $this->getCacheKey();

        $this->log(__METHOD__, [$key]);

        return $this->getCache()->hasItem($key);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->log(__METHOD__);

        return $this->getCache()->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItem($key)
    {
        $key = $this->getCacheKey();

        $this->log(__METHOD__, [$key]);

        return $this->getCache()->deleteItem($key);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys)
    {
        $keys = [$this->getCacheKey()];

        $this->log(__METHOD__, $keys);

        return $this->getCache()->deleteItems($keys);
    }

    /**
     * {@inheritdoc}
     */
    public function save(CacheItemInterface $item)
    {
        $this->log(__METHOD__);

        if ($this->isWritingDisabled()) {
            return true;
        }

        $this->serializeToPersist($item);

        return $this->getCache()->save($item);
    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->log(__METHOD__);

        if ($this->isWritingDisabled()) {
            return true;
        }

        $this->serializeToPersist($item);

        return $this->getCache()->saveDeferred($item);
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        $this->log(__METHOD__);

        if ($this->isWritingDisabled()) {
            return true;
        }

        return $this->getCache()->commit();
    }

    private function log(string $method, array $keys = []): void
    {
        $this->getLogger()->debug(
            sprintf(
                '[%s] Called method %s with key(s) %s',
                __CLASS__,
                $method,
                implode(',', $keys)
            )
        );
    }

    private function isWritingDisabled(): bool
    {
        if ($this->isWritingDisabled === null) {
            $this->isWritingDisabled = (bool)$this->getOption(self::OPTION_DISABLE_WRITE, false);
        }

        return $this->isWritingDisabled;
    }

    private function getCache(): CacheItemPoolInterface
    {
        if ($this->cache === null) {
            $this->cache = $this->createCache();
        }

        return $this->cache;
    }

    private function createCache(): CacheItemPoolInterface
    {
        return $this->getCacheFactory()->create(
            [
                CacheItemPoolFactory::CONFIG_PERSISTENCE => $this->getOption(self::OPTION_PERSISTENCE, 'redis'),
            ]
        );
    }

    private function unSerializeIfNecessary(CacheItemInterface $item): void
    {
        if (empty($item->get())) {
            return;
        }

        $unSerialized = @unserialize($item->get());

        if ($unSerialized !== false) {
            $item->set($unSerialized);
        }
    }

    private function serializeToPersist(CacheItemInterface $item): void
    {
        if (is_array($item->get())) {
            $item->set(serialize($item->get()));
        }
    }

    private function getCacheKey(): string
    {
        return (string)$this->getOption(self::OPTION_TOKEN_CACHE_KEY, 'GCP-TOKEN-SANCTUARY:GOOGLE_AUTH_PHP_GCE');
    }

    private function getCacheFactory(): CacheItemPoolFactory
    {
        return $this->getServiceLocator()->get(CacheItemPoolFactory::class);
    }
}

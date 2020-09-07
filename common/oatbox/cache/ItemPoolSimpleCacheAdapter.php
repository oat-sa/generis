<?php /** @noinspection ALL */

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
    /** @var CacheItemInterface[] */
    private $deferred;

    /**
     * @inheritdoc
     */
    public function getItem($key)
    {
        return $this->getCache()->get($key);
    }

    /**
     * @inheritdoc
     *
     * @return CacheItemInterface[]
     */
    public function getItems(array $keys = [])
    {
        $this->getCache()->setMultiple($keys);
    }

    /**
     * @inheritdoc
     */
    public function hasItem($key)
    {
        $this->getCache()->has($key);
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $this->getCache()->clear();
    }

    /**
     * @inheritdoc
     */
    public function deleteItem($key)
    {
        $this->getCache()->delete($key);
    }

    /**
     * @inheritdoc
     */
    public function deleteItems(array $keys)
    {
        $this->getCache()->deleteMultiple($keys);
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
            $this->store($item);
        }

        return true;
    }

    private function getCache(): CacheInterface
    {
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
        }
    }
}

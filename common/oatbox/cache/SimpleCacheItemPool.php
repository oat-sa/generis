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

namespace oat\taoLti\models\classes\Cache;

use oat\oatbox\cache\SimpleCache;
use oat\oatbox\service\ConfigurableService;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheException;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\CacheItem;

class SimpleCacheItemPool extends ConfigurableService implements CacheItemPoolInterface
{
    /** @var CacheItemInterface[] */
    private $deferred;

    /**
     * {@inheritdoc}
     *
     * @return CacheItemInterface
     */
    public function getItem($key)
    {
        return $this->getCache()->get($key);
    }

    /**
     * {@inheritdoc}
     *
     * @return CacheItemInterface[]
     */
    public function getItems(array $keys = [])
    {
        $this->getCache()->setMultiple($keys);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function hasItem($key)
    {
        $this->getCache()->has($key);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function clear()
    {
        $this->getCache()->clear();
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function deleteItem($key)
    {
        $this->getCache()->delete($key);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function deleteItems(array $keys)
    {
        $this->getCache()->deleteMultiple($keys);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function save(CacheItemInterface $item)
    {
        $this->deferred[$item->getKey()] = $item;

        return $this->commit();
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        if (!$item instanceof CacheItem) {
            return false;
        }
        $this->deferred[$item->getKey()] = $item;

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function commit()
    {
        foreach ($this->deferred as $item) {
            try {
                $this->getCache()->set($item->getKey(), $item->get());
            } catch (CacheException $exception) {
                $this->getLogger()->error(sprintf(
                    'Cache value for %s key has not been saved. %s',
                    $item->getKey(),
                    $exception->getMessage()
                ));
            }
        }
    }

    public function getCache(): CacheInterface
    {
        return $this->getServiceLocator()->get(SimpleCache::SERVICE_ID);
    }
}

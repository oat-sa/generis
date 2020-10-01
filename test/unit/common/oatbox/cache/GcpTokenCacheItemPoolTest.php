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

namespace oat\generis\test\unit\common\oatbox\cache;

use Monolog\Logger;
use oat\generis\test\MockObject;
use oat\generis\test\TestCase;
use oat\oatbox\cache\CacheItem;
use oat\oatbox\cache\factory\CacheItemPoolFactory;
use oat\oatbox\cache\GcpTokenCacheItemPool;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class GcpTokenCacheItemPoolTest extends TestCase
{
    private const TOKEN_CACHE_KEY = 'tokenCacheKey';

    /** @var GcpTokenCacheItemPool */
    private $subject;

    /** @var CacheItemPoolFactory|MockObject */
    private $cacheFactory;

    /** @var CacheItemPoolInterface|MockObject */
    private $cache;

    /** @var Logger */
    private $logger;

    public function setUp(): void
    {
        $this->cacheFactory = $this->createMock(CacheItemPoolFactory::class);
        $this->logger = $this->createMock(Logger::class);
        $this->cache = $this->createMock(CacheItemPoolInterface::class);

        $this->subject = new GcpTokenCacheItemPool(
            [
                GcpTokenCacheItemPool::OPTION_TOKEN_CACHE_KEY => self::TOKEN_CACHE_KEY,
                GcpTokenCacheItemPool::OPTION_DISABLE_WRITE => false,
                GcpTokenCacheItemPool::OPTION_PERSISTENCE => 'redis',
            ]
        );
        $this->subject->setLogger($this->logger);
        $this->subject->setServiceLocator(
            $this->getServiceLocatorMock(
                [
                    CacheItemPoolFactory::class => $this->cacheFactory,
                ]
            )
        );

        $this->cacheFactory
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->cache);
    }

    public function testGetItem(): void
    {
        $cacheItem = $this->createCacheItem();

        $this->cache
            ->expects($this->once())
            ->method('getItem')
            ->with(self::TOKEN_CACHE_KEY)
            ->willReturn($cacheItem);

        $result = $this->subject->getItem('key');

        $this->assertInstanceOf(CacheItem::class, $result);
    }

    public function testGetItems(): void
    {
        $cacheItem = $this->createCacheItem();

        $this->cache
            ->expects($this->once())
            ->method('getItems')
            ->with([self::TOKEN_CACHE_KEY])
            ->willReturn([$cacheItem]);

        $result = $this->subject->getItems(['key']);

        $this->assertIsArray($result);
        $this->assertInstanceOf(CacheItem::class, $result[0]);
    }

    public function testHasItem(): void
    {
        $this->cache
            ->expects($this->once())
            ->method('hasItem')
            ->with(self::TOKEN_CACHE_KEY)
            ->willReturn(true);

        $this->assertTrue($this->subject->hasItem('key'));
    }

    public function testClear(): void
    {
        $this->cache
            ->expects($this->once())
            ->method('clear')
            ->willReturn(true);

        $this->assertTrue($this->subject->clear());
    }

    public function testDeleteItem(): void
    {
        $this->cache
            ->expects($this->once())
            ->method('deleteItem')
            ->with(self::TOKEN_CACHE_KEY)
            ->willReturn(true);

        $this->assertTrue($this->subject->deleteItem('key'));
    }

    public function testDeleteItems(): void
    {
        $this->cache
            ->expects($this->once())
            ->method('deleteItems')
            ->with([self::TOKEN_CACHE_KEY])
            ->willReturn(true);

        $this->assertTrue($this->subject->deleteItems(['key1']));
    }

    public function testSave(): void
    {
        $cacheItem = $this->createCacheItem();

        $this->cache
            ->expects($this->once())
            ->method('save')
            ->with($cacheItem)
            ->willReturn(true);

        $this->assertTrue($this->subject->save($cacheItem));
    }

    public function testSaveDeferred(): void
    {
        $cacheItem = $this->createCacheItem();

        $this->cache
            ->expects($this->once())
            ->method('saveDeferred')
            ->with($cacheItem)
            ->willReturn(true);

        $this->assertTrue($this->subject->saveDeferred($cacheItem));
    }

    public function testCommit(): void
    {
        $this->cache
            ->expects($this->once())
            ->method('commit')
            ->willReturn(true);

        $this->assertTrue($this->subject->commit());
    }

    private function createCacheItem(): CacheItemInterface
    {
        return new CacheItem(self::TOKEN_CACHE_KEY);
    }
}

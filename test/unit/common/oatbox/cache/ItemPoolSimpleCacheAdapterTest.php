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

use Exception;
use Monolog\Logger;
use oat\generis\test\MockObject;
use oat\generis\test\TestCase;
use oat\oatbox\cache\CacheItem;
use oat\oatbox\cache\ItemPoolSimpleCacheAdapter;
use oat\oatbox\cache\SimpleCache;
use Psr\Cache\CacheItemInterface;
use Psr\SimpleCache\CacheInterface;

class ItemPoolSimpleCacheAdapterTest extends TestCase
{
    /** @var ItemPoolSimpleCacheAdapter */
    private $subject;

    /** @var CacheInterface|MockObject */
    private $cacheMock;

    /** @var CacheItemInterface|MockObject */
    private $cacheItemMock;

    /** @var Logger */
    private $loggerMock;

    public function setUp(): void
    {
        $this->cacheMock = $this->createMock(CacheInterface::class);
        $this->cacheItemMock = $this->createMock(CacheItemInterface::class);
        $this->loggerMock = $this->createMock(Logger::class);

        $this->subject = new ItemPoolSimpleCacheAdapter();

        $this->subject->setLogger($this->loggerMock);
        $this->subject->setServiceLocator(
            $this->getServiceLocatorMock(
                [
                    SimpleCache::SERVICE_ID => $this->cacheMock,
                ]
            )
        );
    }

    public function testGetItemThatExistInCache(): void
    {
        $this->cacheMock
            ->expects($this->once())
            ->method('get')
            ->with('key')
            ->willReturn('value');

        $result = $this->subject->getItem('key');
        $this->assertInstanceOf(CacheItem::class, $result);
        $this->assertSame('value', $result->get());
        $this->assertSame('key', $result->getKey());
        $this->assertTrue($result->isHit());
    }

    public function testGetItemThatNotExistInCache(): void
    {
        $this->cacheMock
            ->expects($this->once())
            ->method('get')
            ->with('key')
            ->willReturn(null);

        $result = $this->subject->getItem('key');
        $this->assertInstanceOf(CacheItem::class, $result);
        $this->assertSame('key', $result->getKey());
        $this->assertNull($result->get());
        $this->assertFalse($result->isHit());
    }

    public function testGetItems(): void
    {
        $this->cacheMock
            ->method('get')
            ->withConsecutive(
                ['key1'],
                ['key2']
            )
            ->willReturnOnConsecutiveCalls('value1', 'value2');

        $result = $this->subject->getItems(['key1', 'key2']);

        $this->assertIsArray($result);
        $this->assertInstanceOf(CacheItem::class, $result[0]);
        $this->assertInstanceOf(CacheItem::class, $result[1]);
        $this->assertSame('value1', $result[0]->get());
        $this->assertSame('value2', $result[1]->get());
    }

    public function testHasItem(): void
    {
        $this->cacheMock
            ->expects($this->once())
            ->method('has')
            ->with('key')
            ->willReturn(true);

        $result = $this->subject->hasItem('key');
        $this->assertTrue($result);
    }

    public function testClear(): void
    {
        $this->cacheMock
            ->expects($this->once())
            ->method('clear')
            ->willReturn(true);

        $this->assertTrue($this->subject->clear());
    }

    public function testDeleteItem(): void
    {
        $this->cacheMock
            ->expects($this->once())
            ->method('delete')
            ->with('key')
            ->willReturn(true);

        $this->assertTrue($this->subject->deleteItem('key'));
    }

    public function testDeleteItems(): void
    {
        $this->cacheMock
            ->expects($this->once())
            ->method('deleteMultiple')
            ->with(['key1', 'key2'])
            ->willReturn(true);

        $this->assertTrue($this->subject->deleteItems(['key1', 'key2']));
    }

    public function testSave(): void
    {
        $this->cacheItemMock
            ->expects($this->once())
            ->method('getKey')
            ->willReturn('key');

        $this->cacheItemMock
            ->expects($this->once())
            ->method('get')
            ->willReturn('value');

        $this->cacheMock
            ->expects($this->once())
            ->method('set')
            ->with('key', 'value')
            ->willReturn(true);

        $this->assertTrue($this->subject->save($this->cacheItemMock));
    }

    public function testSaveCatchError(): void
    {
        $this->cacheMock
            ->method('set')
            ->willThrowException(new Exception());

        $this->loggerMock
            ->expects($this->once())
            ->method('error');

        $this->assertFalse($this->subject->save($this->cacheItemMock));
    }

    public function testSaveDeferredAndCommit(): void
    {
        $this->cacheItemMock
            ->expects($this->exactly(4))
            ->method('getKey')
            ->willReturnOnConsecutiveCalls(
                'key1',
                'key2',
                'key1',
                'key2'
            );

        $this->cacheItemMock
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls(
                'value1',
                'value2'
            );

        $this->cacheMock
            ->expects($this->exactly(2))
            ->method('set')
            ->withConsecutive(
                ['key1', 'value1'],
                ['key2', 'value2']
            )->willReturnOnConsecutiveCalls(
                true,
                true
            );

        $this->subject->saveDeferred($this->cacheItemMock);
        $this->subject->saveDeferred($this->cacheItemMock);

        $this->assertTrue($this->subject->commit());
    }

    public function testCommitCatchError(): void
    {
        $this->cacheMock
            ->method('set')
            ->willThrowException(new Exception());

        $this->loggerMock
            ->expects($this->once())
            ->method('error');

        $this->subject->saveDeferred($this->cacheItemMock);

        $this->assertFalse($this->subject->commit());
    }
}

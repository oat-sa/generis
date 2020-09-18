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

use DateInterval;
use DateTime;
use InvalidArgumentException;
use oat\generis\test\TestCase;
use oat\oatbox\cache\CacheItem;
use ReflectionClass;
use ReflectionProperty;

class CacheItemTest extends TestCase
{
    private const KEY = 'key';
    private const EXAMPLE_VALUE = 'example value';

    /** @var CacheItem */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new CacheItem(self::KEY);
    }

    public function testGetKey(): void
    {
        $this->assertSame(self::KEY, $this->subject->getKey());
    }

    public function testGetAndSet(): void
    {
        $this->subject->set(self::EXAMPLE_VALUE);
        $this->assertSame(self::EXAMPLE_VALUE, $this->subject->get());
    }

    public function testIsHit(): void
    {
        $subject = new CacheItem(self::KEY, true);
        $this->assertTrue($subject->isHit());
    }

    public function testExpiresAtInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->subject->expiresAt('string');
    }

    public function testExpireWithNull(): void
    {
        $this->subject->expiresAt(null);

        $this->assertNull(
            $this->getPrivateProperty(CacheItem::class, 'expiry')->getValue($this->subject)
        );
    }

    public function testExpiresAt(): void
    {
        $expiry = new DateTime('tomorrow');
        $this->subject->expiresAt($expiry);

        $this->assertSame(
            $expiry->getTimestamp(),
            $this->getPrivateProperty(CacheItem::class, 'expiry')->getValue($this->subject)
        );
    }

    public function testExpiresAfter3MonthAhead(): void
    {
        $dt = new DateTime('now');
        $dt->add(new DateInterval('P3M'));

        $expected = $dt->format('Y:m:d');

        $this->subject->expiresAfter(new DateInterval('P3M'));

        $expiry = $this->getPrivateProperty(CacheItem::class, 'expiry')->getValue($this->subject);

        $resultDt = new DateTime();
        $resultDt->setTimestamp($expiry);

        $this->assertSame($expected, $resultDt->format('Y:m:d'));
    }

    public function testExpiresAfterWithNull(): void
    {
        $this->subject->expiresAfter(null);

        $this->assertNull(
            $this->getPrivateProperty(CacheItem::class, 'expiry')->getValue($this->subject)
        );
    }

    public function testExpiresAfterWithInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->subject->expiresAfter('string');
    }
    

    public function getPrivateProperty(string $className, string $propertyName): ReflectionProperty
    {
        $reflector = new ReflectionClass($className);
        $property = $reflector->getProperty($propertyName);
        $property->setAccessible(true);

        return $property;
    }
}

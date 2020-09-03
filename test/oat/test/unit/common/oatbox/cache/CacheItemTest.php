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

namespace oat\test\unit\common\oatbox\cache;

use DateInterval;
use DateTime;
use oat\generis\test\TestCase;
use oat\oatbox\cache\CacheItem;

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

    public function testExpiresAt(): void
    {
        $expiry = new DateTime('tomorrow');
        $this->subject->expiresAt($expiry);
        $this->assertFalse($this->subject->isExpired());
    }

    public function testExpiresAtOnExpired(): void
    {
        $expiry = new \DateTime('yesterday');
        $this->subject->expiresAt($expiry);
        $this->assertTrue($this->subject->isExpired());
    }

    public function testExpiresAfter3MonthAhead(): void
    {
        $this->subject->expiresAfter(new DateInterval('P3M'));
        $this->assertFalse($this->subject->isExpired());
    }
}

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

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Psr\Cache\CacheItemInterface;

class CacheItem implements CacheItemInterface
{
    /** @var string */
    private $key;

    /** @var mixed */
    private $value;

    /** @var DateTime */
    private $expiry;

    /** @var bool */
    private $isHit;

    /** @var DateTime */
    private $currentDateTime;

    public function __construct(string $key, bool $hit = false, DateTimeInterface $currentDateTime = null)
    {
        $this->key = $key;
        $this->isHit = $hit;
        $this->currentDateTime = $currentDateTime ?? new DateTimeImmutable();
    }

    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function get(): mixed
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function isHit(): bool
    {
        return $this->isHit;
    }

    /**
     * @inheritDoc
     */
    public function set(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function expiresAt(?DateTimeInterface $expiration): static
    {
        if (null === $expiration) {
            $this->expiry = null;

            return $this;
        }

        $this->expiry = $expiration->getTimestamp();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function expiresAfter(int|\DateInterval|null $time): static
    {
        if (null === $time) {
            $this->expiry = null;

            return $this;
        }

        if (is_int($time)) {
            $time = new DateInterval(sprintf('PT%dS', $time));
        }

        $this->expiry = $this->currentDateTime->add($time)->getTimestamp();

        return $this;
    }
}

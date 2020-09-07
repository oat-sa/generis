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

use DateInterval;
use DateTime;
use InvalidArgumentException;
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

    public function __construct(string $key, $hit = false)
    {
        $this->key = $key;
        $this->isHit = $hit;
    }

    /**
     * @inheritDoc
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function isHit()
    {
        return $this->isHit;
    }

    /**
     * @inheritDoc
     */
    public function set($value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function expiresAt($expiration): self
    {
        if (null !== $expiration && !$expiration instanceof DateTime) {
            throw new InvalidArgumentException(sprintf(
                    'Expiration date must implement DateTimeInterface or be null, "%s" given.',
                    get_debug_type($expiration))
            );
        }

        $this->expiry = $expiration->getTimestamp();

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @param DateInterval|null $time
     */
    public function expiresAfter($time): self
    {
        if (null === $time) {
            $this->expiry = null;
        } elseif ($time instanceof DateInterval) {
            $now = new DateTime('now');
            $this->expiry = $now->add($time)->getTimestamp();
        } else {
            throw new InvalidArgumentException(sprintf(
                    'Expiration date must be a DateInterval or null, "%s" given.',
                    get_debug_type($time))
            );
        }

        return $this;
    }
}

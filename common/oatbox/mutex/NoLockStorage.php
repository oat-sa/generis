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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\oatbox\mutex;

use Symfony\Component\Lock\BlockingStoreInterface;
use Symfony\Component\Lock\Key;

/**
 * Class NoLockStorage
 * @package oat\oatbox\mutex
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class NoLockStorage implements BlockingStoreInterface
{
    /**
     * @inheritdoc
     */
    public function save(Key $key): void
    {
    }

    /**
     * @inheritdoc
     */
    public function waitAndSave(Key $key): void
    {
    }

    /**
     * @inheritdoc
     */
    public function putOffExpiration(Key $key, float $ttl): void
    {
    }

    /**
     * @inheritdoc
     */
    public function delete(Key $key): void
    {
    }

    /**
     * @inheritdoc
     */
    public function exists(Key $key): bool
    {
        return false;
    }
}

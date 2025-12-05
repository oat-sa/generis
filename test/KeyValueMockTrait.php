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
 *
 */

namespace oat\generis\test;

use PHPUnit\Framework\MockObject\MockObject;
use oat\generis\persistence\PersistenceManager;

/**
 * @deprecated Use \oat\generis\test\PersistenceManagerMockTrait.
 *             Since PHPUnit does all the work, we no longer have to use Prophecy to reduce dependencies.
 */
trait KeyValueMockTrait
{
    use PersistenceManagerMockTrait;

    /**
     * @deprecated Use \oat\generis\test\PersistenceManagerMockTrait::getPersistenceManagerMock() instead.
     *             Since PHPUnit does all the work, we no longer have to use Prophecy to reduce dependencies.
     *
     * Returns a key-value persistence on top of a SQL memory mock
     */
    public function getKeyValueMock(string $key): PersistenceManager|MockObject
    {
        return $this->getPersistenceManagerMock(
            $key,
            [self::OPTION_PERSISTENCE_TYPE => self::PERSISTENCE_TYPE_KV]
        );
    }
}

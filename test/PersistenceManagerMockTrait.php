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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\generis\test;

use PHPUnit\Framework\MockObject\MockObject;
use common_persistence_InMemoryKvDriver;
use common_persistence_KeyValuePersistence;
use common_persistence_sql_dbal_Driver;
use oat\generis\persistence\PersistenceManager;

trait PersistenceManagerMockTrait
{
    public const OPTION_PERSISTENCE_TYPE = 'persistenceType';
    public const PERSISTENCE_TYPE_SQL = 'sql';
    public const PERSISTENCE_TYPE_KV = 'kv';

    public function getPersistenceManagerMock(string $key, array $options = []): PersistenceManager|MockObject
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Extension "pdo_sqlite" not loaded, test skipped');
        }

        $persistence = match ($options[self::OPTION_PERSISTENCE_TYPE] ?? self::PERSISTENCE_TYPE_SQL) {
            self::PERSISTENCE_TYPE_KV => new common_persistence_KeyValuePersistence(
                [],
                new common_persistence_InMemoryKvDriver()
            ),
            default => (new common_persistence_sql_dbal_Driver())->connect(
                $key,
                ['connection' => ['url' => 'sqlite:///:memory:']]
            ),
        };

        $persistenceManager = $this->createMock(PersistenceManager::class);
        $persistenceManager
            ->method('setServiceLocator')
            ->with($this->anything());
        $persistenceManager
            ->method('getPersistenceById')
            ->with($key)
            ->willReturn($persistence);

        return $persistenceManager;
    }
}

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

use common_persistence_InMemoryKvDriver;
use common_persistence_Persistence;
use common_persistence_sql_dbal_Driver;
use oat\generis\persistence\PersistenceManager;
use PHPUnit\Framework\MockObject\MockObject;

trait PersistenceManagerMockTrait
{
    /**
     * @deprecated Use PersistenceManagerMockTrait::createPersistenceManagerMock()
     */
    public function getPersistenceManagerMock(string $key): PersistenceManager|MockObject
    {
        return $this->createPersistenceManagerMock([
            $key => $this->createSqlPersistence($key),
        ]);
    }

    public function createPersistenceManagerMock(array $persistences): PersistenceManager|MockObject
    {
        if (empty($persistences)) {
            $this->fail('Persistence manager requires at least one persistence');
        }

        $returnMap = [];

        foreach ($persistences as $key => $persistence) {
            if (!($persistence instanceof MockObject) && !($persistence instanceof common_persistence_Persistence)) {
                $this->fail('Expected an instance of either MockObject or common_persistence_Persistence');
            }

            if (!is_string($key)) {
                $this->fail('Expected persistence key to be a string');
            }

            $returnMap[] = [$key, $persistence];
        }

        $persistenceManager = $this->createMock(PersistenceManager::class);
        $persistenceManager
            ->method('setServiceLocator')
            ->with($this->anything());
        $persistenceManager
            ->method('getPersistenceById')
            ->willReturnMap($returnMap);

        return $persistenceManager;
    }

    public function createSqlPersistence(string $key): common_persistence_Persistence
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Extension "pdo_sqlite" not loaded, test skipped');
        }

        return (new common_persistence_sql_dbal_Driver())->connect(
            $key,
            ['connection' => ['url' => 'sqlite:///:memory:']]
        );
    }

    public function createKVPersistence(string $key): common_persistence_Persistence
    {
        return (new common_persistence_InMemoryKvDriver())->connect($key, []);
    }
}

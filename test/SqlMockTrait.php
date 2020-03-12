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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\generis\test;

use Prophecy\Argument;
use common_persistence_sql_dbal_Driver;
use oat\generis\persistence\PersistenceManager;

trait SqlMockTrait
{
    /**
     * Returns a persistence Manager with a mocked sql persistence
     *
     * @param string $key identifier of the persistence
     * @return PersistenceManager
     */
    public function getSqlMock($key)
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('sqlite not found, tests skipped.');
        }
        $driver = new common_persistence_sql_dbal_Driver();
        $persistence = $driver->connect($key, ['connection' => ['url' => 'sqlite:///:memory:']]);
        $pmProphecy = $this->prophesize(PersistenceManager::class);
        $pmProphecy->setServiceLocator(Argument::any())->willReturn(null);
        $pmProphecy->getPersistenceById($key)->willReturn($persistence);
        return $pmProphecy->reveal();
    }
}

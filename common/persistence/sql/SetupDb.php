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
 * Copyright (c) 2013-2019 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Lionel Lecaque  <lionel@taotesting.com>
 * @license GPLv2
 * @package tao
 */

namespace oat\generis\persistence\sql;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use oat\oatbox\log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;
use Doctrine\DBAL\Exception\ConnectionException;

class SetupDb implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Setup the tables for the database
     * @param \common_persistence_SqlPersistence $p
     * @throws \common_exception_InconsistentData
     */
    public function setupDatabase(\common_persistence_SqlPersistence $p)
    {
        $dbalDriver = $p->getDriver()->getDbalConnection();
        $dbName = $dbalDriver->getDataBase();
        $this->verifyDatabase($p, $dbName);
        $this->cleanDb($p);
    }

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    private function verifyDatabase(\common_persistence_SqlPersistence $p, $dbName)
    {
        $schemaManager = $p->getSchemaManager()->getDbalSchemaManager();
        if (!$this->dbExists($schemaManager, $dbName)) {
            throw new \tao_install_utils_Exception('Unable to find the database, make sure that the db exists and that the db user has the rights to use it.');
        }
    }

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $dbName
     */
    private function dbExists(AbstractSchemaManager $schemaManager, $dbName)
    {
        try {
            return in_array($dbName, $schemaManager->listDatabases());
        } catch (ConnectionException $e) {
            $this->logWarning('Unable to connect to validate dbExists');
            return false;
        }
    }

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    private function cleanDb(\common_persistence_SqlPersistence $p)
    {
        $schema = $p->getSchemaManager()->createSchema();
        $queries = $p->getPlatForm()->toDropSql($schema);
        foreach ($queries as $query) {
            $p->exec($query);
        }
    }
}

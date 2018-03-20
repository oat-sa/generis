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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\generis\test\common\persistence;

use \oat\tao\test\TaoPhpUnitTestRunner;
use common_persistence_SqlKvDriver as SqlKvDriver;
use oat\oatbox\service\ServiceManager;

class SqlKvDriverTest extends TaoPhpUnitTestRunner
{
    public function testSet()
    {
        $this->getServiceManager();
        $driver = new SqlKvDriver();
        $driver->connect('SqlKvDriverTest', ['sqlPersistence' => 'SqlKvDriverTest']);
        $this->assertTrue($driver->set('foo', 'bar'));
    }

    /**
     * @expectedException \oat\oatbox\persistence\WriteException
     * @expectedExceptionMessage Unable to write the key value storage table in the database
     */
    public function testSetException()
    {
        $serviceManager = $this->getServiceManager();
        $driver = new SqlKvDriver();
        $driver->connect('SqlKvDriverTest', ['sqlPersistence' => 'SqlKvDriverTest']);
        /** @var \common_persistence_Manager $persistenceManager */
        $persistenceManager = $serviceManager->get(\common_persistence_Manager::SERVICE_ID);
        $persistence = $persistenceManager->getPersistenceById('SqlKvDriverTest');
        $schemaManager = $persistence->getDriver()->getSchemaManager();
        $schema = $schemaManager->createSchema();
        $fromSchema = clone $schema;
        $table = $schema->getTable('kv_store');
        $table->dropColumn('kv_id');
        $queries = $persistence->getPlatform()->getMigrateSchemaSql($fromSchema, $schema);
        foreach ($queries as $query) {
            $persistence->exec($query);
        }

        $driver->set('foo', 'bar');
    }

    /**
     * @throws
     * @return ServiceManager
     */
    protected function getServiceManager()
    {
        $persistenceManager = $this->getSqlMock('SqlKvDriverTest');
        $config = new \common_persistence_KeyValuePersistence([], new \common_persistence_InMemoryKvDriver());
        $config->set(\common_persistence_Manager::SERVICE_ID, $persistenceManager);
        $serviceManager = new ServiceManager($config);
        ServiceManager::setServiceManager($serviceManager);
        $persistence = $persistenceManager->getPersistenceById('SqlKvDriverTest');

        $schemaManager = $persistence->getDriver()->getSchemaManager();
        $schema = $schemaManager->createSchema();
        $fromSchema = clone $schema;

        $table = $schema->createTable('kv_store');
        $table->addColumn('kv_id', 'string', ['notnull' => null, 'length' => 255]);
        $table->addColumn('kv_value', 'text', ['notnull' => null]);
        $table->addColumn('kv_time', 'integer', ['notnull' => null, 'length' => 30]);
        $table->setPrimaryKey(['kv_id']);

        $queries = $persistence->getPlatform()->getMigrateSchemaSql($fromSchema, $schema);
        foreach ($queries as $query) {
            $persistence->exec($query);
        }

        return $serviceManager;
    }
}

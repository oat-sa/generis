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
 * Copyright (c) 2019-2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

declare(strict_types=1);

namespace oat\generis\persistence;

use oat\oatbox\service\ConfigurableService;
use oat\generis\persistence\sql\SchemaCollection;
use common_persistence_SqlPersistence;
use oat\generis\persistence\sql\SchemaProviderInterface;
use oat\oatbox\service\ServiceNotFoundException;

/**
 * The PersistenceManager is responsible for initializing all persistences
 *
 * @author Lionel Lecaque <lionel@taotesting.com>
 * @license GPLv2
 */
class PersistenceManager extends ConfigurableService
{

    public const SERVICE_ID = 'generis/persistences';

    public const OPTION_PERSISTENCES = 'persistences';

    /**
     * Mapping of drivers to implementations.
     * All SQL drivers except 'dbal' are deprecated
     * @var array
     */
    private const DRIVER_MAP = [
        'dbal' => 'common_persistence_sql_dbal_Driver',
        'dbal_pdo_mysql' => 'common_persistence_sql_dbal_Driver',
        'dbal_pdo_sqlite' => 'common_persistence_sql_dbal_Driver',
        'dbal_pdo_pgsql' => 'common_persistence_sql_dbal_Driver',
        'dbal_pdo_ibm' => 'common_persistence_sql_dbal_Driver',
        'phpredis' => 'common_persistence_PhpRedisDriver',
        'phpfile' => 'common_persistence_PhpFileDriver',
        'SqlKvWrapper' => 'common_persistence_SqlKvDriver',
        'no_storage' => 'common_persistence_InMemoryKvDriver',
        'no_storage_adv' => 'common_persistence_InMemoryAdvKvDriver'
    ];

    /**
     *
     * @var array
     */
    private $persistences = [];

    /**
     * Returns TRUE if the requested persistence exist, otherwise FALSE.
     *
     * @param string $persistenceId
     * @return bool
     */
    public function hasPersistence($persistenceId)
    {
        $persistenceList = $this->getOption(static::OPTION_PERSISTENCES);
        return isset($persistenceList[$persistenceId]);
    }

    /**
     * Registers a new persistence.
     *
     * @param string $persistenceId
     * @param array $persistenceConf
     */
    public function registerPersistence($persistenceId, array $persistenceConf)
    {
        // wrap pdo drivers in dbal
        if (strpos($persistenceConf['driver'], 'pdo_') === 0) {
            $persistenceConf = [
                'driver' => 'dbal',
                'connection' => $persistenceConf
            ];
        }

        if (
            isset($persistenceConf['connection']['driver'])
            && $persistenceConf['connection']['driver'] === 'pdo_mysql'
        ) {
            $persistenceConf['connection']['charset'] = 'utf8';
        }

        $configs = $this->getOption(self::OPTION_PERSISTENCES);
        $configs[$persistenceId] = $persistenceConf;
        $this->setOption(self::OPTION_PERSISTENCES, $configs);
    }

    /**
     *
     * @return \common_persistence_Persistence
     */
    public function getPersistenceById($persistenceId)
    {
        if (!isset($this->persistences[$persistenceId])) {
            $this->persistences[$persistenceId] = $this->createPersistence($persistenceId);
        }
        return $this->persistences[$persistenceId];
    }

    /**
     *
     * @param string $persistenceId
     * @return \common_persistence_Persistence
     * @throws \common_Exception
     */
    private function createPersistence($persistenceId)
    {
        $configs = $this->getOption(self::OPTION_PERSISTENCES);
        if (!isset($configs[$persistenceId])) {
            throw new \common_Exception('Persistence Configuration for persistence ' . $persistenceId . ' not found');
        }
        $config = $configs[$persistenceId];
        $driverString = $config['driver'];

        $driverClassName = isset(self::DRIVER_MAP[$driverString]) ? self::DRIVER_MAP[$driverString] : $driverString;

        if (!class_exists($driverClassName)) {
            throw new \common_exception_Error(
                'Driver ' . $driverString . ' not found, check your database configuration'
            );
        }

        $driver = $this->propagate(new $driverClassName());

        $driverOptionsFeeder = $this->getDriverConfigFeeder();

        if ($driverOptionsFeeder !== null) {
            $config = $driverOptionsFeeder->feed($config);
        }

        return $driver->connect($persistenceId, $config);
    }

    /**
     * Return a collection of all SQL schemas
     */
    public function getSqlSchemas()
    {
        $schemas = new SchemaCollection();
        foreach (array_keys($this->getOption(self::OPTION_PERSISTENCES)) as $id) {
            $persistence = $this->getPersistenceById($id);
            if ($persistence instanceof common_persistence_SqlPersistence) {
                $schemas->addSchema($id, $persistence->getSchemaManager()->createSchema());
            }
        }
        return $schemas;
    }

    /**
     * Adapt the databases to the SQL schemas
     */
    public function applySchemas(SchemaCollection $schemaCollection)
    {
        $this->logInfo('Applying schame changes');
        foreach ($schemaCollection as $id => $schema) {
            $persistence = $this->getPersistenceById($id);
            $fromSchema = $schemaCollection->getOriginalSchema($id);
            $queries = $persistence->getPlatForm()->getMigrateSchemaSql($fromSchema, $schema);
            foreach ($queries as $query) {
                $persistence->exec($query);
            }
        }
    }

    /**
     * Add the schema of a single service, if needed
     */
    public function applySchemaProvider($service): void
    {
        if ($service instanceof SchemaProviderInterface) {
            $schemaCollection = $this->getSqlSchemas();
            $service->provideSchema($schemaCollection);
            $this->applySchemas($schemaCollection);
            $this->logInfo('Applied schema for ' . get_class($service));
        } else {
            $this->logDebug('No schema found for ' . get_class($service));
        }
    }

    private function getDriverConfigFeeder(): ?DriverConfigurationFeeder
    {
        /**
         * This class is directly instantiated directly in some installation process, so the ServiceLocator
         * might not be available, therefore this if is necessary.
         */
        if (!$this->getServiceLocator()) {
            return null;
        }

        try {
            return $this->getServiceLocator()->get(DriverConfigurationFeeder::SERVICE_ID);
        } catch (ServiceNotFoundException $exception) {
            /**
             * This is because PersistenceManager is registered in tao-core and while doing the generis update
             * before the update of tao (which had this class in the migrations).
             */
            return null;
        }
    }
}

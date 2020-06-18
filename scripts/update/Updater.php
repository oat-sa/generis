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
 * Copyright (c) 2014-2017 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\generis\scripts\update;

use common_Exception;
use common_exception_NotImplemented;
use common_ext_ExtensionsManager;
use common_ext_ExtensionUpdater;
use core_kernel_impl_ApiModelOO;
use core_kernel_persistence_smoothsql_SmoothModel;
use EasyRdf_Exception;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Memory\MemoryAdapter;
use oat\generis\model\data\ModelManager;
use oat\generis\model\data\Ontology;
use oat\generis\model\fileReference\FileReferenceSerializer;
use oat\generis\model\fileReference\ResourceFileSerializer;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\generis\model\kernel\uri\UriProvider;
use oat\generis\model\user\AuthAdapter;
use oat\generis\model\user\UserFactoryService;
use oat\generis\persistence\PersistenceManager;
use oat\oatbox\action\ActionService;
use oat\oatbox\cache\KeyValueCache;
use oat\oatbox\cache\SimpleCache;
use oat\oatbox\config\ConfigurationService;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\log\logger\TaoLog;
use oat\oatbox\log\LoggerService;
use oat\oatbox\mutex\LockService;
use oat\oatbox\mutex\NoLockStorage;
use oat\oatbox\service\ServiceNotFoundException;
use oat\oatbox\session\SessionService;
use oat\oatbox\task\implementation\InMemoryQueuePersistence;
use oat\oatbox\task\implementation\SyncQueue;
use oat\oatbox\task\implementation\TaskQueuePayload;
use oat\oatbox\task\Queue;
use oat\oatbox\task\TaskRunner;
use oat\oatbox\user\UserLanguageService;
use oat\taoWorkspace\model\generis\WrapperModel;
use Psr\Log\LoggerInterface;

/**
 * @author Joel Bout <joel@taotesting.com>
 */
class Updater extends common_ext_ExtensionUpdater
{
    /**
     * @param string $initialVersion
     *
     * @return string $versionUpdatedTo
     * @throws common_Exception
     * @throws EasyRdf_Exception
     */
    public function update($initialVersion)
    {
        if ($this->isBetween('0.0.0', '2.11.0')) {
            throw new common_exception_NotImplemented(
                'Updates from versions prior to Tao 3.1 are not longer supported, please update to Tao 3.1 first'
            );
        }
        // in order to prevent update scripts to fail we run some early fixes
        $this->runPreChecks();

        $this->skip('2.12.0', '2.18.0');

        if ($this->isVersion('2.18.0')) {
            $this->getServiceManager()->register(ActionService::SERVICE_ID, new ActionService());
            $this->setVersion('2.19.0');
        }

        if ($this->isVersion('2.19.0')) {
            try {
                $this->getServiceManager()->get(Queue::CONFIG_ID);
            } catch (ServiceNotFoundException $e) {
                $service = new SyncQueue([]);
                $service->setServiceManager($this->getServiceManager());

                $this->getServiceManager()->register(Queue::CONFIG_ID, $service);
            }
            $this->setVersion('2.20.0');
        }


        $this->skip('2.20.0', '2.29.1');
        if ($this->isVersion('2.29.1')) {
            $this->getServiceManager()->register(FileReferenceSerializer::SERVICE_ID, new ResourceFileSerializer());
            $this->setVersion('2.30.0');
        }

        $this->skip('2.30.0', '2.31.6');

        if ($this->isVersion('2.31.6')) {
            $complexSearch = new ComplexSearchService(
                [
                    'shared' => [
                        'search.query.query' => false,
                        'search.query.builder' => false,
                        'search.query.criterion' => false,
                        'search.tao.serialyser' => false,
                        'search.tao.result' => false,
                    ],
                    'invokables' => [
                        'search.query.query' => '\\oat\\search\\Query',
                        'search.query.builder' => '\\oat\\search\\QueryBuilder',
                        'search.query.criterion' => '\\oat\\search\\QueryCriterion',
                        'search.driver.postgres' => '\\oat\\search\\DbSql\\Driver\\PostgreSQL',
                        'search.driver.mysql' => '\\oat\\search\\DbSql\\Driver\\MySQL',
                        'search.driver.tao' => '\\oat\\generis\\model\\kernel\\persistence\\smoothsql\\search\\driver\\TaoSearchDriver',
                        'search.tao.serialyser' => '\\oat\\search\\DbSql\\TaoRdf\\UnionQuerySerialyser',
                        'search.factory.query' => '\\oat\\search\\factory\\QueryFactory',
                        'search.factory.builder' => '\\oat\\search\\factory\\QueryBuilderFactory',
                        'search.factory.criterion' => '\\oat\\search\\factory\\QueryCriterionFactory',
                        'search.tao.gateway' => '\\oat\\generis\\model\\kernel\\persistence\\smoothsql\\search\\GateWay',
                        'search.tao.result' => '\\oat\\generis\\model\\kernel\\persistence\\smoothsql\\search\\TaoResultSet',
                    ],
                    'abstract_factories' => [
                        '\\oat\\search\\Command\\OperatorAbstractfactory',
                    ],
                    'services' => [
                        'search.options' => [
                            'table' => 'statements',
                            'driver' => 'taoRdf',
                        ],
                    ],
                ]
            );

            $this->getServiceManager()->register(ComplexSearchService::SERVICE_ID, $complexSearch);
            $this->setVersion('3.0.0');
        }

        $this->skip('3.0.0', '3.6.1');

        if ($this->isVersion('3.6.1')) {
            $model = ModelManager::getModel();
            if ($model instanceof core_kernel_persistence_smoothsql_SmoothModel) {
                $model->setOption(
                    core_kernel_persistence_smoothsql_SmoothModel::OPTION_SEARCH_SERVICE,
                    ComplexSearchService::SERVICE_ID
                );
                ModelManager::setModel($model);
            }
            $this->setVersion('3.7.0');
        }

        if ($this->isBetween('3.7.0', '3.8.3')) {
            /* @var $modelWrapper WrapperModel */
            $modelWrapper = ModelManager::getModel();
            if ($modelWrapper instanceof WrapperModel) {
                $inner = $modelWrapper->getInnerModel();
                $inner->setOption(
                    core_kernel_persistence_smoothsql_SmoothModel::OPTION_SEARCH_SERVICE,
                    ComplexSearchService::SERVICE_ID
                );

                $workspace = $modelWrapper->getWorkspaceModel();
                $workspace->setOption(
                    core_kernel_persistence_smoothsql_SmoothModel::OPTION_SEARCH_SERVICE,
                    ComplexSearchService::SERVICE_ID
                );

                $wrappedModel = WrapperModel::wrap($inner, $workspace);
                $wrappedModel->setServiceLocator($this->getServiceManager());
                ModelManager::setModel($wrappedModel);
            }
            $this->setVersion('3.8.4');
        }
        $this->skip('3.8.4', '3.9.0');

        if ($this->isVersion('3.9.0')) {
            $fsm = $this->getServiceManager()->get(FileSystemService::SERVICE_ID);
            $fsm->createFileSystem(Queue::FILE_SYSTEM_ID, Queue::FILE_SYSTEM_ID);
            $this->getServiceManager()->register(FileSystemService::SERVICE_ID, $fsm);
            $this->setVersion('3.10.0');
        }

        $this->skip('3.10.0', '3.27.0');

        if ($this->isVersion('3.27.0')) {
            if (!$this->getServiceManager()->has(common_ext_ExtensionsManager::SERVICE_ID)) {
                $this->getServiceManager()->register(
                    common_ext_ExtensionsManager::SERVICE_ID,
                    new common_ext_ExtensionsManager()
                );
            }
            $this->setVersion('3.28.0');
        }

        $this->skip('3.28.0', '3.29.1');

        if ($this->isVersion('3.29.1')) {
            $this->getServiceManager()->register(LoggerService::SERVICE_ID, new LoggerService());
            $this->setVersion('3.30.0');
        }

        $this->skip('3.30.0', '3.34.0');

        /**
         * replaced update script 3.34.0 because of potential config loss
         */
        if ($this->isVersion('3.34.0')) {
            $queue = $this->getServiceManager()->get(Queue::SERVICE_ID);

            if (get_class($queue) === 'oat\Taskqueue\Persistence\RdsQueue') {
                $persistence = $queue->getOption('persistence');
                $queue->setOptions(
                    [
                        'payload' => '\oat\oatbox\task\implementation\TaskQueuePayload',
                        'runner' => '\oat\oatbox\task\TaskRunner',
                        'persistence' => '\oat\Taskqueue\Persistence\TaskSqlPersistence',
                        'config' => ['persistence' => $persistence],
                    ]
                );
            } else {
                $queue->setOptions(
                    [
                        'payload' => TaskQueuePayload::class,
                        'runner' => TaskRunner::class,
                        'persistence' => InMemoryQueuePersistence::class,
                        'config' => [],
                    ]
                );
            }
            $this->getServiceManager()->register(Queue::SERVICE_ID, $queue);
            /**
             * skip because you don't need to fix config
             */
            $this->setVersion('3.35.2');
        }

        /**
         * you are on a bad version needs to be fixed
         */
        $this->skip('3.35.0', '3.35.1');

        /**
         * fix for bad config
         */
        if ($this->isVersion('3.35.1')) {
            $queue = $this->getServiceManager()->get(Queue::SERVICE_ID);
            if (get_class($queue) === 'oat\Taskqueue\Persistence\RdsQueue') {
                $queue->setOptions(
                    [
                        'payload' => '\oat\oatbox\task\implementation\TaskQueuePayload',
                        'runner' => '\oat\oatbox\task\TaskRunner',
                        'persistence' => '\oat\Taskqueue\Persistence\TaskSqlPersistence',
                        'config' => ['persistence' => 'default'],
                    ]
                );
            }
            $this->getServiceManager()->register(Queue::SERVICE_ID, $queue);
            $this->setVersion('3.35.2');
        }

        $this->skip('3.35.2', '4.1.4');

        if ($this->isVersion('4.1.4')) {
            /** Rdf synchronization was moved to version 4.4.1 (see below) because OntologyUpdater is in tao extension */
            //            OntologyUpdater::syncModels();
            $this->setVersion('4.2.0');
        }
        $this->skip('4.2.0', '4.4.0');

        if ($this->isVersion('4.4.0')) {
            $file = __DIR__ . DIRECTORY_SEPARATOR .
                '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
                'core' . DIRECTORY_SEPARATOR .
                'ontology' . DIRECTORY_SEPARATOR .
                'taskqueue.rdf';
            $api = core_kernel_impl_ApiModelOO::singleton();
            $api->importXmlRdf('http://www.tao.lu/Ontologies/taskqueue.rdf', $file);
            $this->setVersion('4.4.1');
        }

        $this->skip('4.4.1', '6.7.0');

        if ($this->isVersion('6.7.0')) {
            $file = __DIR__ . DIRECTORY_SEPARATOR .
                '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
                'core' . DIRECTORY_SEPARATOR .
                'ontology' . DIRECTORY_SEPARATOR .
                'generis.rdf';
            $api = core_kernel_impl_ApiModelOO::singleton();
            $api->importXmlRdf('http://www.tao.lu/Ontologies/generis.rdf', $file);
            $this->setVersion('6.8.0');
        }

        $this->skip('6.8.0', '6.8.1');

        if ($this->isVersion('6.8.1')) {
            if ($this->getExtension()->hasConfig('logger')) {
                $this->getExtension()->unsetConfig('logger');
            }

            $conf = $this->getExtension()->getConfig('log');
            if (!$conf instanceof LoggerInterface) {
                $logger = new LoggerService([
                    LoggerService::LOGGER_OPTION => new TaoLog([
                        TaoLog::OPTION_APPENDERS => $conf,
                    ]),
                ]);
                $header = $this->getExtension()->getConfigHeader('log');
                $logger->setHeader($header);
                $this->getServiceManager()->register(LoggerService::SERVICE_ID, $logger);
            }
            $this->setVersion('6.9.0');
        }

        $this->skip('6.9.0', '6.16.0');

        if ($this->isVersion('6.16.0')) {
            $userFactory = new UserFactoryService([]);
            $this->getServiceManager()->register(UserFactoryService::SERVICE_ID, $userFactory);

            /** @var common_ext_ExtensionsManager $extensionManager */
            $extensionManager = $this->getServiceManager()->get(common_ext_ExtensionsManager::SERVICE_ID);
            $config = $extensionManager->getExtensionById('generis')->getConfig('auth');

            foreach ($config as $index => $adapter) {
                if ($adapter['driver'] === AuthAdapter::class) {
                    $adapter['user_factory'] = UserFactoryService::SERVICE_ID;
                }
                $config[$index] = $adapter;
            }

            $extensionManager->getExtensionById('generis')->setConfig('auth', array_values($config));

            $this->setVersion('6.17.0');
        }

        $this->skip('6.17.0', '7.1.1');

        if ($this->isVersion('7.1.1')) {
            $this->getServiceManager()->register(UserLanguageService::SERVICE_ID, new UserLanguageService());
            $this->setVersion('7.2.0');
        }

        $this->skip('7.2.0', '7.14.2');

        if ($this->isVersion('7.14.2')) {
            $this->getServiceManager()->register(SessionService::SERVICE_ID, new SessionService());
            $modelConfig = $this->getServiceManager()->get(Ontology::SERVICE_ID)->getConfig();
            $className = $modelConfig['class'];
            $ontologyModel = new $className($modelConfig['config']);
            if ($ontologyModel instanceof core_kernel_persistence_smoothsql_SmoothModel) {
                $ontologyModel->setOption(
                    \core_kernel_persistence_smoothsql_SmoothModel::OPTION_CACHE_SERVICE,
                    \common_cache_Cache::SERVICE_ID
                );
            }
            $this->getServiceManager()->register(Ontology::SERVICE_ID, $ontologyModel);
            $this->setVersion('8.0.0');
        }

        $this->skip('8.0.0', '9.1.3');

        if ($this->isVersion('9.1.3')) {
            $this->getServiceManager()->unregister('generis/profiler');
            $this->setVersion('10.0.0');
        }

        $this->skip('10.0.0', '10.1.0');

        if ($this->isBetween('10.1.0', '11.0.0')) {
            /** @var \common_persistence_Manager $persistenceManager */
            $persistenceManager = $this->getServiceManager()->get(\common_persistence_Manager::SERVICE_ID);

            $persistenceManagerConfig = $persistenceManager->getOption('persistences');
            foreach ($persistenceManagerConfig as $persistenceId => $persistenceParams) {
                // wrap pdo drivers in dbal
                if (strpos($persistenceParams['driver'], 'pdo_') === 0) {
                    $persistenceManagerConfig[$persistenceId] = [
                        'driver' => 'dbal',
                        'connection' => $persistenceParams,
                    ];
                }
            }
            $persistenceManager->setOption('persistences', $persistenceManagerConfig);
            $this->getServiceManager()->register(\common_persistence_Manager::SERVICE_ID, $persistenceManager);

            $this->setVersion('11.0.0');
        }

        $this->skip('11.0.0', '11.1.1');

        if ($this->isVersion('11.1.1')) {
            //            $service = new LockService([
            //                LockService::OPTION_PERSISTENCE_CLASS => PdoStore::class,
            //                LockService::OPTION_PERSISTENCE_OPTIONS => 'default',
            //            ]);
            //            $this->getServiceManager()->register(LockService::SERVICE_ID, $service);
            //            $service->install();
            $this->setVersion('11.2.0');
        }

        $this->skip('11.2.0', '11.2.1');

        if ($this->isVersion('11.2.1')) {
            $service = new LockService([
                LockService::OPTION_PERSISTENCE_CLASS => NoLockStorage::class
            ]);
            $this->getServiceManager()->register(LockService::SERVICE_ID, $service);
            $this->setVersion('11.2.2');
        }

        $this->skip('11.2.2', '11.3.1');

        if ($this->isVersion('11.3.1')) {
            /** @var \common_persistence_Manager $persistenceManager */
            $persistenceManager = $this->getServiceManager()->get(\common_persistence_Manager::SERVICE_ID);
            $persistenceManagerConfig = $persistenceManager->getOption('persistences');

            foreach ($persistenceManagerConfig as $persistenceId => $persistenceParams) {
                $persistenceManager->registerPersistence($persistenceId, $persistenceParams);
            }

            $this->getServiceManager()->register(\common_persistence_Manager::SERVICE_ID, $persistenceManager);
            $this->setVersion('11.3.2');
        }

        $this->skip('11.3.2', '11.5.2');

        if ($this->isVersion('11.5.2')) {
            $this->runExtensionScript(RegisterDefaultKvPersistence::class);

            $this->setVersion('11.6.0');
        }

        $this->skip('11.6.0', '12.1.0');

        if ($this->isBetween('12.1.0', '12.2.0')) {
            $fs = $this->getServiceManager()->get(FileSystemService::SERVICE_ID);
            $adapters = $fs->getOption(FileSystemService::OPTION_ADAPTERS);
            if (!isset($adapters['default'])) {
                if (get_class($fs) == FileSystemService::class) {
                    // override default behavior to ensure an adapter and not a directory is created
                    $adapters['default'] = [
                        'class' => Local::class,
                        'options' => ['root' => $fs->getOption(FileSystemService::OPTION_FILE_PATH)]
                    ];
                    $fs->setOption(FileSystemService::OPTION_ADAPTERS, $adapters);
                } else {
                    $fs->createFileSystem('default', '');
                }
                $this->getServiceManager()->register(FileSystemService::SERVICE_ID, $fs);
            }
            $this->setVersion('12.2.0');
        }
        $this->skip('12.2.0', '12.4.0');
        if ($this->isVersion('12.4.0')) {
            $uriService = $this->getServiceManager()->get(UriProvider::SERVICE_ID);
            if ($uriService instanceof ConfigurationService) {
                $innerService = $uriService->getConfig();
                if ($innerService instanceof UriProvider) {
                    $this->getServiceManager()->register(UriProvider::SERVICE_ID, $innerService);
                } else {
                    throw new \common_exception_InconsistentData('Unknown URI Provider found');
                }
            }
            $this->setVersion('12.4.1');
        }
        $this->skip('12.4.1', '12.11.0');

        if ($this->isVersion('12.11.0')) {
            /** @var \common_persistence_Persistence $defaultPersistence */
            $defaultPersistence = $this->getServiceManager()
                ->get(PersistenceManager::SERVICE_ID)
                ->getPersistenceById('default');
            /** @var \common_persistence_sql_SchemaManager $schemaManager */
            $schemaManager = $defaultPersistence->getDriver()->getSchemaManager();
            $schema = $schemaManager->createSchema();
            $fromSchema = clone $schema;
            $schema->dropTable('models');
            $queries = $defaultPersistence->getPlatform()->getMigrateSchemaSql($fromSchema, $schema);
            foreach ($queries as $query) {
                $defaultPersistence->exec($query);
            }
            $this->setVersion('12.12.0');
        }

        $this->skip('12.12.0', '12.20.2');

        if ($this->isVersion('12.20.2')) {
            $file = __DIR__ . DIRECTORY_SEPARATOR .
                '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
                'core' . DIRECTORY_SEPARATOR .
                'ontology' . DIRECTORY_SEPARATOR .
                'generis.rdf';
            $api = core_kernel_impl_ApiModelOO::singleton();
            $api->importXmlRdf('http://www.tao.lu/Ontologies/generis.rdf', $file);

            $this->setVersion('12.21.0');
        }
        $this->skip('12.21.0', '12.22.1');

        if ($this->isVersion('12.22.1')) {
            /** @var FileSystemService $fs */
            $fs = $this->getServiceManager()->get(FileSystemService::SERVICE_ID);
            $adapters = $fs->getOption(FileSystemService::OPTION_ADAPTERS);
            $adapters['memory'] = [ 'class' => MemoryAdapter::class, ];
            $fs->setOption(FileSystemService::OPTION_ADAPTERS, $adapters);
            $this->getServiceManager()->register(FileSystemService::SERVICE_ID, $fs);

            $this->setVersion('12.23.0');
        }

        $this->skip('12.23.0', '12.26.1');

        if ($this->isVersion('12.26.1')) {
            $file = __DIR__ . DIRECTORY_SEPARATOR .
                '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR .
                'core' . DIRECTORY_SEPARATOR .
                'ontology' . DIRECTORY_SEPARATOR .
                'widgetdefinitions.rdf';
            $api = core_kernel_impl_ApiModelOO::singleton();
            $api->importXmlRdf('http://www.tao.lu/datatypes/WidgetDefinitions.rdf', $file);

            $this->setVersion('12.27.0');
        }
    }

    /**
     * Runs required fixes that allow the rest of the updates to run
     */
    protected function runPreChecks(): void
    {
        // 12.22.0 introduced PSR-16 cache required for update process
        if (!$this->getServiceManager()->has(SimpleCache::SERVICE_ID)) {
            $psrCache = new KeyValueCache([KeyValueCache::OPTION_PERSISTENCE => 'cache']);
            $this->getServiceManager()->register(SimpleCache::SERVICE_ID, $psrCache);
        }
    }
}

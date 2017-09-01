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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\generis\scripts\update;

use common_cache_KeyValueCache;
use common_ext_ExtensionsManager;
use common_ext_ExtensionUpdater;
use common_ext_NamespaceManager;
use common_Logger;
use common_persistence_Manager;
use core_kernel_classes_Class;
use core_kernel_impl_ApiModelOO;
use core_kernel_persistence_smoothsql_SmoothModel;
use core_kernel_uri_DatabaseSerialUriProvider;
use core_kernel_uri_UriService;
use oat\generis\model\data\ModelManager;
use oat\generis\model\data\permission\PermissionManager;
use oat\generis\model\fileReference\FileReferenceSerializer;
use oat\generis\model\fileReference\ResourceFileSerializer;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\oatbox\action\ActionService;
use oat\oatbox\event\EventManager;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\log\LoggerService;
use oat\oatbox\service\ServiceNotFoundException;
use oat\oatbox\task\implementation\InMemoryQueuePersistence;
use oat\oatbox\task\implementation\SyncQueue;
use oat\oatbox\task\implementation\TaskQueuePayload;
use oat\oatbox\task\Queue;
use oat\oatbox\task\TaskRunner;
use oat\taoWorkspace\model\generis\WrapperModel;


/**
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class Updater extends common_ext_ExtensionUpdater {
    
    /**
     * @param string $initialVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion) {

        $currentVersion = $initialVersion;
        if ($currentVersion == '2.7') {
        
            $file = dirname(__FILE__).DIRECTORY_SEPARATOR.'widgetdefinitions_2.7.1.rdf';
        
            $api = core_kernel_impl_ApiModelOO::singleton();
            $success = $api->importXmlRdf('http://www.tao.lu/datatypes/WidgetDefinitions.rdf', $file);
            
            if ($success) {
                $currentVersion = '2.7.1';
            } else{
                common_Logger::w('Import failed for '.$file);
            }
        }
        
        if ($currentVersion == '2.7.1') {
        
            $file = dirname(__FILE__).DIRECTORY_SEPARATOR.'widgetdefinitions_2.7.2.rdf';
        
            $api = core_kernel_impl_ApiModelOO::singleton();
            $success = $api->importXmlRdf('http://www.tao.lu/datatypes/WidgetDefinitions.rdf', $file);
        
            if ($success) {
                $currentVersion = '2.7.2';
            } else{
                common_Logger::w('Import failed for '.$file);
            }
        }
        
        if ($currentVersion == '2.7.2') {
            $implClass = common_ext_ExtensionsManager::singleton()->getExtensionById('generis')->getConfig(PermissionManager::CONFIG_KEY);
            if (is_string($implClass)) {
                if (class_exists($implClass)) {
                    $impl = new $implClass();
                    PermissionManager::setPermissionModel($impl);
                    $currentVersion = '2.7.3';
                } else {
                    common_Logger::w('Unexpected permission manager config type: '.gettype($implClass));
                }
            } else {
                common_Logger::w('Unexpected permission manager config type: '.gettype($implClass));
            }
        }
        
        if ($currentVersion == '2.7.3') {
            ModelManager::setModel(new core_kernel_persistence_smoothsql_SmoothModel(array(
                core_kernel_persistence_smoothsql_SmoothModel::OPTION_PERSISTENCE => 'default',
                core_kernel_persistence_smoothsql_SmoothModel::OPTION_READABLE_MODELS => $this->getReadableModelIds(),
                core_kernel_persistence_smoothsql_SmoothModel::OPTION_WRITEABLE_MODELS => array('1'),
                core_kernel_persistence_smoothsql_SmoothModel::OPTION_NEW_TRIPLE_MODEL => '1'
            )));
            $currentVersion = '2.7.4';
        }

        if ($currentVersion == '2.7.4' && defined('GENERIS_URI_PROVIDER')) {
            if (in_array(GENERIS_URI_PROVIDER, array('DatabaseSerialUriProvider', 'AdvKeyValueUriProvider'))) {
                $uriProviderClassName = '\core_kernel_uri_' . GENERIS_URI_PROVIDER;
                $options = array(
                	core_kernel_uri_DatabaseSerialUriProvider::OPTION_PERSISTENCE => 'default',
                    core_kernel_uri_DatabaseSerialUriProvider::OPTION_NAMESPACE => LOCAL_NAMESPACE.'#'
                );
                $provider = new $uriProviderClassName($options);
            } else {
                $uriProviderClassName = '\common_uri_' . GENERIS_URI_PROVIDER;
                $provider = new $uriProviderClassName();
            }
            core_kernel_uri_UriService::singleton()->setUriProvider($provider);
            $currentVersion = '2.7.5';
        }
        
        // service manager support
        if ($currentVersion == '2.7.5' 
            || $currentVersion == '2.7.6' 
            || $currentVersion == '2.7.7'
            || $currentVersion == '2.8.0') {
            $currentVersion = '2.9.0';
        }
        
        if ($currentVersion == '2.9.0') {
            // skip, unused
            //try {
            //    $this->getServiceManager()->get('generis/FsManager');
            //} catch (ServiceNotFoundException $e) {
            //    $FsManager = new \common_persistence_fileSystem_Manager(array(
            //        \common_persistence_fileSystem_Manager::OPTION_FILE_PATH => FILES_PATH
            //    ));
            //
            //    $this->getServiceManager()->register('generis/FsManager', $FsManager);
            //}
            
            // update persistences
            $persistenceConfig = $this->getServiceManager()->get('generis/persistences');
            if (is_array($persistenceConfig)) {
                $service = new common_persistence_Manager(array(
                    common_persistence_Manager::OPTION_PERSISTENCES =>$persistenceConfig
                ));
                $this->getServiceManager()->register('generis/persistences', $service);
            }
            
            // update cache
            try {
                $this->getServiceManager()->get('generis/cache');
            } catch (ServiceNotFoundException $e) {
                $cache = new common_cache_KeyValueCache(array(
                    common_cache_KeyValueCache::OPTION_PERSISTENCE => 'cache'
                ));
                $cache->setServiceManager($this->getServiceManager());
                
                $this->getServiceManager()->register('generis/cache', $cache);
            }
            
            $currentVersion = '2.10.0';
        }
        
        if ($currentVersion == '2.10.0') {
            $eventManager = new EventManager();
            $eventManager->attach(
                'oat\\generis\\model\\data\\event\\ResourceCreated',
                array('oat\\generis\\model\\data\\permission\\PermissionManager', 'catchEvent')
            );
            $this->getServiceManager()->register(EventManager::CONFIG_ID, $eventManager);
            $currentVersion = '2.11.0';
        }
        
        $this->setVersion($currentVersion);
        
        if ($this->isVersion('2.11.0')) {
            $FsManager = new FileSystemService(array(
                FileSystemService::OPTION_FILE_PATH => FILES_PATH,
                FileSystemService::OPTION_ADAPTERS=> array()
            ));
            
            $class = new core_kernel_classes_Class(GENERIS_NS . '#VersionedRepository');
            /** @var \core_kernel_classes_Resource $resource */
            foreach ($class->getInstances(true) as $resource) {
                $path = (string) $resource->getOnePropertyValue(new \core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH));
                $FsManager->registerLocalFileSystem($resource->getUri(), $path);
            }
            $this->getServiceManager()->register(FileSystemService::SERVICE_ID, $FsManager);
            
            $this->setVersion('2.12.0');
        }
        
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
                array(
                    'shared' => array(
                        'search.query.query' => false,
                        'search.query.builder' => false,
                        'search.query.criterion' => false,
                        'search.tao.serialyser' => false,
                        'search.tao.result' => false
                    ),
                    'invokables' => array(
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
                        'search.tao.result' => '\\oat\\generis\\model\\kernel\\persistence\\smoothsql\\search\\TaoResultSet'
                    ),
                    'abstract_factories' => array(
                        '\\oat\\search\\Command\\OperatorAbstractfactory'
                    ),
                    'services' => array(
                        'search.options' => array(
                            'table' => 'statements',
                            'driver' => 'taoRdf'
                        )
                    )
                )
            );
            
            $this->getServiceManager()->register(ComplexSearchService::SERVICE_ID, $complexSearch);
            $this->setVersion('3.0.0');
        }

        $this->skip('3.0.0', '3.6.1');

        if ($this->isVersion('3.6.1')) {
            $model = ModelManager::getModel();
            if ($model instanceof \core_kernel_persistence_smoothsql_SmoothModel) {
                $model->setOption(
                    \core_kernel_persistence_smoothsql_SmoothModel::OPTION_SEARCH_SERVICE,
                    ComplexSearchService::SERVICE_ID
                );
                ModelManager::setModel($model);
            }
            $this->setVersion('3.7.0');
        }
        
        if($this->isBetween('3.7.0', '3.8.3')) {
            
            /* @var $modelWrapper WrapperModel */
            $modelWrapper = ModelManager::getModel();
            if ($modelWrapper instanceof WrapperModel) {
                $inner = $modelWrapper->getInnerModel();
                $inner->setOption(\core_kernel_persistence_smoothsql_SmoothModel::OPTION_SEARCH_SERVICE , ComplexSearchService::SERVICE_ID);

                $workspace = $modelWrapper->getWorkspaceModel();
                $workspace->setOption(core_kernel_persistence_smoothsql_SmoothModel::OPTION_SEARCH_SERVICE , ComplexSearchService::SERVICE_ID);

                $wrapedModel = WrapperModel::wrap($inner, $workspace );
                $wrapedModel->setServiceLocator($this->getServiceManager());
                ModelManager::setModel($wrapedModel);
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
            if (! $this->getServiceManager()->has(common_ext_ExtensionsManager::SERVICE_ID)) {
                $this->getServiceManager()->register(common_ext_ExtensionsManager::SERVICE_ID, new common_ext_ExtensionsManager());
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

            if(get_class($queue) === 'oat\Taskqueue\Persistence\RdsQueue') {
                $persistence = $queue->getOption('persistence');
                $queue->setOptions(
                    [
                        'payload'     => '\oat\oatbox\task\implementation\TaskQueuePayload',
                        'runner'      => '\oat\oatbox\task\TaskRunner',
                        'persistence' => '\oat\Taskqueue\Persistence\TaskSqlPersistence',
                        'config'      => ['persistence' => $persistence],
                    ]
                );
            } else {
                $queue->setOptions(
                    [
                        'payload'     => TaskQueuePayload::class,
                        'runner'      => TaskRunner::class,
                        'persistence' => InMemoryQueuePersistence::class,
                        'config'      => [],
                    ]
                );
            }
            $this->getServiceManager()->register(Queue::SERVICE_ID  , $queue);
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
            if(get_class($queue) === 'oat\Taskqueue\Persistence\RdsQueue') {
                $queue->setOptions(
                    [
                        'payload'     => '\oat\oatbox\task\implementation\TaskQueuePayload',
                        'runner'      => '\oat\oatbox\task\TaskRunner',
                        'persistence' => '\oat\Taskqueue\Persistence\TaskSqlPersistence',
                        'config'      => ['persistence' => 'default'],
                    ]
                );
            }
            $this->getServiceManager()->register(Queue::SERVICE_ID  , $queue);
            $this->setVersion('3.35.2');
        }

        $this->skip('3.35.2', '4.1.1');
    }
    
    private function getReadableModelIds() {
        $extensionManager = \common_ext_ExtensionsManager::singleton();
        common_ext_NamespaceManager::singleton()->reset();
        
        $uris = array(LOCAL_NAMESPACE.'#');
        foreach ($extensionManager->getModelsToLoad() as $subModelUri){
            if(!preg_match("/#$/", $subModelUri)){
                $subModelUri .= '#';
            }
            $uris[] = $subModelUri;
        }
        $ids = array();
        foreach(common_ext_NamespaceManager::singleton()->getAllNamespaces() as $namespace){
            if(in_array($namespace->getUri(), $uris)){
                $ids[] = $namespace->getModelId();
            }
        }
        return array_unique($ids);
    }
}

<?php

require_once __DIR__ . '/../../../../common/inc.extension.php';

use oat\oatbox\mutex\LockService;
use oat\oatbox\service\ServiceManager;

array_shift($argv);
$actionId = $argv[0];
$sleep = (integer) $argv[1];

$service = getInstance();
$factory = $service->getLockFactory();
$lock = $factory->createLock($actionId);
$lock->acquire(true);
sleep($sleep);
$lock->release();

/**
 * @return LockService
 */
function getInstance()
{
    $config = new \common_persistence_KeyValuePersistence([], new \common_persistence_InMemoryKvDriver());
    $config->set(\common_persistence_Manager::SERVICE_ID, new \common_persistence_Manager);
    $serviceManager = new ServiceManager($config);
    $service = new LockService([
        LockService::OPTION_PERSISTENCE => __DIR__.DIRECTORY_SEPARATOR.'flock'
    ]);
    $service->setServiceLocator($serviceManager);
    return $service;
}
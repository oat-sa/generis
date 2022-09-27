<?php

use oat\oatbox\mutex\LockService;
use oat\oatbox\service\ServiceManager;
use Symfony\Component\Lock\Store\FlockStore;
use oat\oatbox\mutex\NoLockStorage;

// phpcs:disable
require __DIR__ . '/../../../bootstrap.php';

function getInstance($class, $dir): LockService
{
    $config = new \common_persistence_KeyValuePersistence([], new \common_persistence_InMemoryKvDriver());
    $config->set(\common_persistence_Manager::SERVICE_ID, new \common_persistence_Manager());
    $serviceManager = new ServiceManager($config);
    $service = new LockService([
        LockService::OPTION_PERSISTENCE_CLASS => $class,
        LockService::OPTION_PERSISTENCE_OPTIONS => $dir
    ]);
    $service->setServiceLocator($serviceManager);

    return $service;
}
// phpcs:enable

array_shift($argv);
$actionId = $argv[0];
$sleep = (int)$argv[1];
$implementation = (string)$argv[2];
$dir = isset($argv[3]) ? $argv[3] : null;

if ($implementation === 'FlockStore') {
    $class = FlockStore::class;
} elseif ($implementation === 'NoLockStorage') {
    $class = NoLockStorage::class;
}

$service = getInstance($class, $dir);
$factory = $service->getLockFactory();
$lock = $factory->createLock($actionId);
$lock->acquire(true);
sleep($sleep);
$lock->release();

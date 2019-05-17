<?php

require_once __DIR__ . '/../../../common/inc.extension.php';

use oat\oatbox\mutex\LockService;
use oat\oatbox\service\ServiceManager;
use Symfony\Component\Lock\Store\PdoStore;

array_shift($argv);
$actionId = $argv[0];
$sleep = (integer) $argv[1];
$timeout = (integer) $argv[2];

$service = getInstance();
$factory = $service->getLockFactory();
$lock = $factory->createLock($actionId, $timeout);
$lock->acquire(true);
sleep($sleep);
$lock->release();

/**
 * @return LockService
 */
function getInstance()
{
    $service = new LockService([
        LockService::OPTION_PERSISTENCE_CLASS => PdoStore::class,
        LockService::OPTION_PERSISTENCE_OPTIONS => 'default',
    ]);
    $service->setServiceLocator(ServiceManager::getServiceManager());
    return $service;
}
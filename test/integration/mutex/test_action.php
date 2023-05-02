<?php

use oat\oatbox\mutex\LockService;
use oat\oatbox\service\ServiceManager;

// phpcs:disable
require_once __DIR__ . '/../../../common/inc.extension.php';

function getLockServiceInstance(): LockService
{
    return ServiceManager::getServiceManager()->get(LockService::class);
}
// phpcs:enable

array_shift($argv);
$actionId = $argv[0];
$sleep = (int) $argv[1];
$timeout = (int) $argv[2];

$service = getLockServiceInstance();
$factory = $service->getLockFactory();
$lock = $factory->createLock($actionId, $timeout);
$lock->acquire(true);
sleep($sleep);
$lock->release();

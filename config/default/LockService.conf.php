<?php

use oat\oatbox\mutex\LockService;
use oat\oatbox\mutex\NoLockStorage;

return new oat\oatbox\mutex\LockService([
    LockService::OPTION_PERSISTENCE_CLASS => NoLockStorage::class
]);

<?php
/**
 * Default config header
 *
 * To replace this add a file C:\domains\package-tao\generis\config/header/taskqueue.conf.php
 */

return new oat\oatbox\task\implementation\SyncQueue(
    [
        'payload'     => \oat\oatbox\task\implementation\TaskQueuePayload::class,
        'runner'      => \oat\oatbox\task\TaskRunner::class,
        'persistence' => \oat\oatbox\task\implementation\InMemoryQueuePersistence::class,
        'config'      => [],
    ]
);

<?php
/**
 * Default config header created during install
 */

return new oat\oatbox\TaskQueue\Queue([
    \oat\oatbox\TaskQueue\Queue::OPTION_QUEUE_NAME => 'queue',
    \oat\oatbox\TaskQueue\Queue::OPTION_MESSAGE_BROKER => \oat\oatbox\TaskQueue\MessageBroker\InMemoryBroker::class,
    \oat\oatbox\TaskQueue\Queue::OPTION_MESSAGE_BROKER_CONFIG => [],
    \oat\oatbox\TaskQueue\Queue::OPTION_MESSAGE_BROKER_CACHE => 'generis/cache'
]);

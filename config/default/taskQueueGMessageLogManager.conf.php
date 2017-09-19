<?php
/**
 * Default config header created during install
 */

return new oat\oatbox\TaskQueue\MessageLogManager([
    \oat\oatbox\TaskQueue\MessageLogManager::OPTION_MESSAGE_LOG_BROKER => \oat\oatbox\TaskQueue\MessageLogBroker\MySqlLogBroker::class,
    \oat\oatbox\TaskQueue\MessageLogManager::OPTION_MESSAGE_LOG_BROKER_CONFIG => [
        'persistence' => 'default'
    ]
]);

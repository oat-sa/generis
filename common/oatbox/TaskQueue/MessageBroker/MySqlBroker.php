<?php

namespace oat\oatbox\TaskQueue\MessageBroker;

use oat\oatbox\log\LoggerAwareTrait;
use oat\oatbox\TaskQueue\MessageInterface;

/**
 * Storing messages/tasks in MySql.
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
final class MySqlBroker extends AbstractMessageBroker
{
    use LoggerAwareTrait;

    public function createQueue()
    {
        // TODO: Implement createQueue() method.
    }

    public function pushMessage(MessageInterface $message)
    {
        // TODO: Implement pushMessage() method.
    }

    public function popMessage()
    {
        // TODO: Implement popMessage() method.
    }

    public function acknowledgeMessage(MessageInterface $message)
    {
        // TODO: Implement acknowledgeMessage() method.
    }

    public function count()
    {
        // TODO: Implement count() method.
    }
}
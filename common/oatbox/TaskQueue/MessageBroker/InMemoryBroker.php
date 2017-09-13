<?php

namespace oat\oatbox\TaskQueue\MessageBroker;

use oat\oatbox\TaskQueue\MessageInterface;
use oat\oatbox\log\LoggerAwareTrait;

final class InMemoryBroker extends AbstractMessageBroker
{
    use LoggerAwareTrait;

    /**
     * @var \SplQueue
     */
    private $queue;

    public function createQueue()
    {
        $this->queue = new \SplQueue();
        $this->logDebug('Memory Queue created');
    }

    public function pushMessage(MessageInterface $message)
    {
        $this->queue->enqueue($message);
        return true;
    }

    /**
     * @return null|MessageInterface
     */
    public function popMessage()
    {
        if (!$this->count()) {
            return null;
        }

        return $this->queue->dequeue();
    }

    public function acknowledgeMessage(MessageInterface $message)
    {
        // do nothing, because dequeue automatically deletes the message from the queue
    }

    public function count()
    {
        return $this->queue->count();
    }
}
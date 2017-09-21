<?php

namespace oat\oatbox\TaskQueue\MessageBroker;

use oat\oatbox\TaskQueue\MessageInterface;
use oat\oatbox\log\LoggerAwareTrait;

/**
 * Stores tasks in memory. It accomplishes Sync Queue mechanism.
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
final class InMemoryBroker extends AbstractMessageBroker
{
    use LoggerAwareTrait;

    /**
     * @var \SplQueue
     */
    private $queue;

    /**
     * Initiates the SplQueue
     */
    public function createQueue()
    {
        $this->queue = new \SplQueue();
        $this->logDebug('Memory Queue created');
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->queue->count();
    }

    /**
     * @param MessageInterface $message
     * @return bool
     */
    public function pushMessage(MessageInterface $message)
    {
        $this->queue->enqueue($message);
        return true;
    }

    /**
     * Overwriting the parent totally because in this case we need a much simpler logic for popping messages.
     *
     * @return mixed|null
     */
    public function popMessage()
    {
        if (!$this->count()) {
            return null;
        }

        return $this->queue->dequeue();
    }

    /**
     * Do nothing.
     */
    protected function doPop()
    {
    }

    /**
     * Do nothing, because dequeue automatically deletes the message from the queue
     *
     * @param MessageInterface $message
     */
    public function acknowledgeMessage(MessageInterface $message)
    {
    }

    /**
     * Do nothing.
     *
     * @param string $receipt
     * @param array  $logContext
     */
    protected function deleteMessage($receipt, array $logContext = [])
    {
    }
}
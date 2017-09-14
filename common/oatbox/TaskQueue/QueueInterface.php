<?php

namespace oat\oatbox\TaskQueue;

use oat\oatbox\action\Action;
use oat\oatbox\TaskQueue\MessageBroker\MessageBrokerInterface;
use Psr\Log\LoggerAwareInterface;

/**
 * Interface QueueInterface
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
interface QueueInterface extends \Countable, LoggerAwareInterface
{
    const SERVICE_ID = 'generis/taskQueueG';
    const QUEUE_PREFIX = 'TQG';

    /**
     * Returns queue name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the message broker being used.
     *
     * @return MessageBrokerInterface
     */
    public function getBroker();

    /**
     * Create a task to be managed by the queue from any Action
     *
     * @param Action $action
     * @param array $parameters
     * @return ActionTask
     */
    public function createTask(Action $action, array $parameters);

    /**
     * Publish a message to the queue.
     *
     * @param MessageInterface $message
     * @return bool Is the messaged successfully enqueued?
     */
    public function enqueue(MessageInterface $message);

    /**
     * Receive message from the queue.
     *
     * @return null|MessageInterface
     */
    public function dequeue();

    /**
     * Acknowledge that the message has been received and consumed.
     *
     * @param MessageInterface $message
     */
    public function acknowledge(MessageInterface $message);
}
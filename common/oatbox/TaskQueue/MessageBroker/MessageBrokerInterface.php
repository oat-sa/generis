<?php

namespace oat\oatbox\TaskQueue\MessageBroker;

use oat\oatbox\action\ActionService;
use oat\oatbox\TaskQueue\MessageInterface;
use Psr\Log\LoggerAwareInterface;

/**
 * Interface MessageBrokerInterface
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
interface MessageBrokerInterface extends \Countable, LoggerAwareInterface
{
    /**
     * MessageBrokerInterface constructor.
     *
     * @param string $queueName
     * @param array $config
     */
    public function __construct($queueName, array $config);

    /**
     * Get queue name
     *
     * @return string
     */
    public function getName();

    /**
     * @param \common_cache_Cache $cache
     */
    public function setCache(\common_cache_Cache $cache);

    /**
     * @param ActionService $resolver
     * @return mixed
     */
    public function setActionResolver(ActionService $resolver);

    /**
     * Creates the queue.
     *
     * @return mixed
     */
    public function createQueue();

    /**
     * Pushes a message into the queue.
     *
     * @param MessageInterface $message
     * @return bool
     */
    public function pushMessage(MessageInterface $message);

    /**
     * Pops a message from the queue.
     *
     * @return null|MessageInterface
     */
    public function popMessage();

    /**
     * If the driver supports it, this will be called when a message has been consumed.
     *
     * @param MessageInterface $message
     */
    public function acknowledgeMessage(MessageInterface $message);
}
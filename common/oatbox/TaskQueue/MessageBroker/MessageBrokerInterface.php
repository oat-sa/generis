<?php

namespace oat\oatbox\TaskQueue\MessageBroker;

use oat\oatbox\action\ActionService;
use oat\oatbox\TaskQueue\MessageInterface;
use Psr\Log\LoggerAwareInterface;

interface MessageBrokerInterface extends \Countable, LoggerAwareInterface
{
    public function __construct($queueName, array $config);

    public function getName();

    public function setCache(\common_cache_Cache $cache);
    public function setActionResolver(ActionService $resolver);

    public function createQueue();

    /**
     * @param MessageInterface $message
     * @return bool
     */
    public function pushMessage(MessageInterface $message);

    /**
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
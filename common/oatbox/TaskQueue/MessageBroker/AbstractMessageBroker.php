<?php

namespace oat\oatbox\TaskQueue\MessageBroker;


use oat\oatbox\action\ActionService;
use oat\oatbox\TaskQueue\QueueInterface;

/**
 * Class AbstractMessageBroker
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
abstract class AbstractMessageBroker implements MessageBrokerInterface
{
    // Maximum amount of messages that can be received when polling the queue; Default is 1.
    const CONFIG_MESSAGES_TO_RECEIVE = 'messages_to_receive';

    private $name;
    private $messagesToReceive = 1;
    private $cache;
    private $actionResolver;

    /**
     * AbstractMessageBroker constructor.
     *
     * @param string $queueName
     * @param array  $config
     */
    public function __construct($queueName, array $config)
    {
        $this->name = $queueName;

        if(isset($config[self::CONFIG_MESSAGES_TO_RECEIVE])) {
            $this->messagesToReceive = abs((int) $config[self::CONFIG_MESSAGES_TO_RECEIVE]);
        }
    }

    /**
     * @param \common_cache_Cache $cache
     * @return $this
     */
    public function setCache(\common_cache_Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @return \common_cache_Cache
     */
    protected function getCache()
    {
        return $this->cache;
    }

    /**
     * @param ActionService $resolver
     * @return $this
     */
    public function setActionResolver(ActionService $resolver)
    {
        $this->actionResolver = $resolver;

        return $this;
    }

    /**
     * @return ActionService
     */
    protected function getActionResolver()
    {
        return $this->actionResolver;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    protected function getNameWithPrefix()
    {
        return sprintf("%s_%s", QueueInterface::QUEUE_PREFIX, $this->getName());
    }

    /**
     * @return int
     */
    protected function getMessagesToReceive()
    {
        return $this->messagesToReceive;
    }
}
<?php

namespace oat\oatbox\TaskQueue;

use oat\oatbox\action\Action;
use oat\oatbox\action\ActionService;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\TaskQueue\MessageBroker\InMemoryBroker;
use oat\oatbox\TaskQueue\MessageBroker\MessageBrokerInterface;
use oat\oatbox\log\LoggerAwareTrait;

final class Queue extends ConfigurableService implements QueueInterface
{
    use LoggerAwareTrait;

    const OPTION_QUEUE_NAME = 'queue_name';
    const OPTION_MESSAGE_BROKER = 'message_broker';
    const OPTION_MESSAGE_BROKER_CONFIG = 'message_broker_config';
    const OPTION_MESSAGE_BROKER_CACHE = 'message_broker_cache';

    /**
     * @var MessageBrokerInterface
     */
    private $broker;

    public function __construct(array $options)
    {
        parent::__construct($options);

        if (!$this->hasOption(self::OPTION_QUEUE_NAME)) {
            throw new \InvalidArgumentException("Queue name needs to be set.");
        }

        if (!$this->hasOption(self::OPTION_MESSAGE_BROKER)) {
            throw new \InvalidArgumentException("Message Broker needs to be set.");
        }

        if(!is_a($this->getOption(self::OPTION_MESSAGE_BROKER), MessageBrokerInterface::class, true)) {
            throw new \InvalidArgumentException('Message Broker must implement ' . MessageBrokerInterface::class);
        }
    }

    public function getName()
    {
        return $this->getOption(self::OPTION_QUEUE_NAME);
    }

    public function getBroker()
    {
        if (is_null($this->broker)) {
            $brokerClass = $this->getOption(self::OPTION_MESSAGE_BROKER);
            $this->broker = new $brokerClass($this->getName(), (array) $this->getOption(self::OPTION_MESSAGE_BROKER_CONFIG));

            if ($this->hasOption(self::OPTION_MESSAGE_BROKER_CACHE)) {
                $this->broker->setCache($this->getServiceManager()->get($this->getOption(self::OPTION_MESSAGE_BROKER_CACHE)));
            }

            $this->broker->setActionResolver($this->getServiceManager()->get(ActionService::SERVICE_ID));

            // create the queue if InMemoryBroker is used
            if ($this->broker instanceof InMemoryBroker) {
                $this->broker->createQueue();
            }
        }

        return $this->broker;
    }

    /**
     * @param Action $action
     * @param array  $parameters
     * @return ActionTask
     */
    public function createTask(Action $action, array $parameters)
    {
        $actionTask = (new ActionTask())
            ->setAction($action)
            ->setParameter($parameters);

        if ($this->enqueue($actionTask)) {
            $actionTask->markAsEnqueued();
        }

        return $actionTask;
    }

    /**
     * @param MessageInterface $message
     * @return bool
     */
    public function enqueue(MessageInterface $message)
    {
        try {
            $isEnqueued = $this->getBroker()->pushMessage($message);

            if ($isEnqueued) {
                $this->getMessageLogManager()
                    ->add($message, MessageLogManagerInterface::MESSAGE_STATUS_ENQUEUED);
            }

            // if we need to run the task straightaway
            if ($isEnqueued && $this->getBroker() instanceof InMemoryBroker) {
                (new Worker($this, $this->getMessageLogManager(), false))
                    ->setMaxIterations(1)
                    ->processQueue();
            }

            return $isEnqueued;
        } catch (\Exception $e) {
            $this->logError('Enqueueing '. $message .' failed with MSG: '. $e->getMessage());
        }

        return false;
    }

    public function dequeue()
    {
        if ($message = $this->getBroker()->popMessage()) {
            $this->getMessageLogManager()
                ->setStatus($message->getId(), MessageLogManagerInterface::MESSAGE_STATUS_DEQUEUED);

            return $message;
        }

        return null;
    }

    public function acknowledge(MessageInterface $message)
    {
        $this->getBroker()->acknowledgeMessage($message);
    }

    public function count()
    {
        return $this->getBroker()->count();
    }

    /**
     * @return MessageLogManagerInterface
     */
    private function getMessageLogManager()
    {
        return $this->getServiceManager()->get(MessageLogManagerInterface::SERVICE_ID);
    }
}
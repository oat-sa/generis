<?php

namespace oat\oatbox\TaskQueue\MessageBroker;


use oat\oatbox\action\ActionService;
use oat\oatbox\action\ResolutionException;
use oat\oatbox\log\LoggerAwareTrait;
use oat\oatbox\TaskQueue\ActionTaskInterface;
use oat\oatbox\TaskQueue\Message;
use oat\oatbox\TaskQueue\MessageInterface;
use oat\oatbox\TaskQueue\QueueInterface;
use oat\oatbox\TaskQueue\TaskInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class AbstractMessageBroker
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
abstract class AbstractMessageBroker implements MessageBrokerInterface
{
    use LoggerAwareTrait;

    // Maximum amount of messages that can be received when polling the queue; Default is 1.
    const CONFIG_MESSAGES_TO_RECEIVE = 'messages_to_receive';

    private $name;
    private $messagesToReceive = 1;
    private $cache;
    private $actionResolver;
    private $preFetchedQueue;

    /**
     * AbstractMessageBroker constructor.
     *
     * @param string $queueName
     * @param array  $config
     */
    public function __construct($queueName, array $config)
    {
        $this->name = $queueName;
        $this->preFetchedQueue = new \SplQueue();

        if(isset($config[self::CONFIG_MESSAGES_TO_RECEIVE])) {
            $this->messagesToReceive = abs((int) $config[self::CONFIG_MESSAGES_TO_RECEIVE]);
        }
    }

    /**
     * Do the specific pop mechanism related to the given broker.
     * Messages need to be added to the internal pre-fetched queue.
     *
     * @return void
     */
    abstract protected function doPop();

    /**
     * @return null|MessageInterface
     */
    public function popMessage()
    {
        // if there is item in the pre-fetched queue, let's return that
        if ($message = $this->popPreFetchedMessage()) {
            return $message;
        }

        $this->doPop();

        return $this->popPreFetchedMessage();
    }

    /**
     * Pop a message from the internal queue.
     *
     * @return MessageInterface|null
     */
    private function popPreFetchedMessage()
    {
        if ($this->preFetchedQueue->count()) {
            return $this->preFetchedQueue->dequeue();
        }

        return null;
    }

    /**
     * Add a message to the internal queue.
     *
     * @param MessageInterface $message
     */
    protected function pushPreFetchedMessage(MessageInterface $message)
    {
        $this->preFetchedQueue->enqueue($message);
    }

    /**
     * Denormalize the given message JSON.
     *
     * If the json is not valid, it deletes the message straight away without processing it.
     *
     * @param string $messageJSON
     * @param string $receiptForDeletion An identification of the given message
     * @param array  $logContext
     * @return null|MessageInterface
     */
    protected function denormalizeMessage($messageJSON, $receiptForDeletion, array $logContext = [])
    {
        if (($basicData = json_decode($messageJSON, true)) !== null
            && json_last_error() === JSON_ERROR_NONE
            && isset($basicData[MessageInterface::JSON_BODY_KEY])
        ) {
            // it seems a valid message JSON, let's work with it

            $classOrBody = $basicData[MessageInterface::JSON_BODY_KEY];

            // if the body contains a valid class name, let's instantiate it otherwise just creating a simple message object
            $message = class_exists($classOrBody) ? new $classOrBody() : new Message($classOrBody);
            $message->setMetadata($basicData[MessageInterface::JSON_METADATA_KEY]);

            if ($message instanceof TaskInterface) {
                $message->setParameter($basicData[TaskInterface::JSON_PARAMETERS_KEY]);
            }

            if ($message instanceof ActionTaskInterface) {
                try {
                    $action = $this->getActionResolver()->resolve($message->getAction());

                    if ($action instanceof ServiceLocatorAwareInterface) {
                        $action->setServiceLocator($this->getActionResolver()->getServiceLocator());
                    }

                    $message->setAction($action);
                } catch (ResolutionException $e) {
                    $this->logError('Action class '. $message->getAction() .' does not exist', $logContext);
                    return null;
                }
            }

            return $message;
        }

        // if we have an invalid message:
        // - the given string is not json-decode-able, it's just an arbitrary string
        // - it's a valid json but not containing the 'body' key
        $this->deleteMessage($receiptForDeletion, $logContext);

        return null;
    }

    /**
     * Internal mechanism of deleting a message.
     *
     * @param string $receipt
     * @param array $logContext
     * @return void
     */
    abstract protected function deleteMessage($receipt, array $logContext = []);

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
    public function getMessagesToReceive()
    {
        return $this->messagesToReceive;
    }
}
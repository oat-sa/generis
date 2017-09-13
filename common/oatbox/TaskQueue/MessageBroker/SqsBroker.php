<?php

namespace oat\oatbox\TaskQueue\MessageBroker;

use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;
use oat\oatbox\action\ResolutionException;
use oat\oatbox\TaskQueue\ActionTaskInterface;
use oat\oatbox\TaskQueue\Message;
use oat\oatbox\TaskQueue\MessageInterface;
use oat\oatbox\log\LoggerAwareTrait;
use oat\oatbox\TaskQueue\TaskInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

final class SqsBroker extends AbstractMessageBroker
{
    use LoggerAwareTrait;

    const CONFIG_PROFILE = 'profile';
    const CONFIG_REGION = 'region';
    const CONFIG_VERSION = 'version';

    /**
     * @var SqsClient
     */
    private $client;

    private $queueUrl;

    private $preFetchedQueue;

    public function __construct($queueName, array $config)
    {
        parent::__construct($queueName, $config);

        if(!isset($config[self::CONFIG_PROFILE])) {
            throw new \InvalidArgumentException("AWS profile needs to be set.");
        }

        if(!isset($config[self::CONFIG_REGION])) {
            throw new \InvalidArgumentException("AWS region needs to be set.");
        }

        if (!isset($config[self::CONFIG_VERSION])) {
            $config[self::CONFIG_VERSION] = 'latest';
        }

        $this->client = new SqsClient([
            'profile' => $config[self::CONFIG_PROFILE],
            'region' => $config[self::CONFIG_REGION],
            'version' => $config[self::CONFIG_VERSION]
        ]);
        $this->preFetchedQueue = new \SplQueue();
    }

    public function createQueue()
    {
        try {
            // Note: we are creating a Standard Queue for the time being. More development needed to customize this for creating FIFO Queue.
            /** @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sqs-2012-11-05.html#createqueue */
            $result = $this->client->createQueue([
                'QueueName' => $this->getNameWithPrefix(),
                'Attributes' => [
                    'DelaySeconds' => 0,
                    'VisibilityTimeout' => 600
                ]
            ]);

            if ($result->hasKey('QueueUrl')) {
                $this->queueUrl = $result->get('QueueUrl');

                $this->getCache()->put($this->getUrlCacheKey(), $this->queueUrl);

                $this->logDebug('Queue '. $this->queueUrl .' created and cached');
            } else {
                $this->logError('Queue '. $this->getNameWithPrefix() .' not created');
            }
        } catch (AwsException $e) {
            $this->logError('Creating queue '. $this->getNameWithPrefix() .' failed with MSG: '. $e->getAwsErrorMessage());
        }
    }

    public function pushMessage(MessageInterface $message)
    {
        // ensures that the SQS Queue exist
        if (!$this->queueExists()) {
            $this->createQueue();
        }

        $logContext = [
            'QueueUrl' => $this->queueUrl,
            'InternalMessageId' => $message->getId()
        ];

        try {
            $result = $this->client->sendMessage([
                'MessageAttributes' => [],
                'MessageBody' => json_encode($message),
                'QueueUrl' => $this->queueUrl
            ]);

            if ($result->hasKey('MessageId')) {
                $this->logDebug('Message pushed to SQS', array_merge($logContext, [
                    'SqsMessageId' => $result->get('MessageId')
                ]));
                return true;
            } else {
                $this->logError('Message seems not received by SQS.', $logContext);
            }
        } catch (AwsException $e) {
            $this->logError('Pushing message failed with MSG: '. $e->getAwsErrorMessage(), $logContext);
        }

        return false;
    }

    /**
     * @return MessageInterface|null
     */
    public function popMessage()
    {
        // if there is item in the pre-fetched queue, let's return that
        if ($message = $this->popPreFetchedMessage()) {
            return $message;
        }

        // ensures that the SQS Queue exist
        if (!$this->queueExists()) {
            $this->createQueue();
        }

        $logContext = [
            'QueueUrl' => $this->queueUrl
        ];

        try {
            $result = $this->client->receiveMessage([
                'AttributeNames' => [], //nothing
                'MaxNumberOfMessages' => $this->getMessagesToReceive(),
                'MessageAttributeNames' => [], //nothing
                'QueueUrl' => $this->queueUrl,
                'WaitTimeSeconds' => 20 //retrieving messages with Long Polling
            ]);

            if (count($result->get('Messages')) > 0) {
                $this->logDebug('Received '. count($result->get('Messages')) .' messages.', $logContext);

                foreach ($result->get('Messages') as $message) {
                    if ($messageObject = $this->denormalizeMessage($message)) {
                        $this->preFetchedQueue->enqueue($messageObject);
                    }
                }
            } else {
                $this->logDebug('No messages in queue.', $logContext);
                return null;
            }
        } catch (AwsException $e) {
            $this->logError('Popping messages failed with MSG: '. $e->getAwsErrorMessage());
        }

        return $this->popPreFetchedMessage();
    }

    /**
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
     * Denormalize SQS message.
     *
     * @see http://docs.aws.amazon.com/aws-sdk-php/v3/api/api-sqs-2012-11-05.html#shape-message
     * @param array $messageSqsData
     * @return MessageInterface|null
     */
    private function denormalizeMessage(array $messageSqsData)
    {
        if (($basicData = json_decode($messageSqsData['Body'], true)) !== null
            && json_last_error() === JSON_ERROR_NONE
            && isset($basicData[MessageInterface::JSON_BODY_KEY])
        ) {
            $classOrBody = $basicData[MessageInterface::JSON_BODY_KEY];

            // if the body contains a valid class name, let's instantiate it otherwise just creating a simple message object
            $message = class_exists($classOrBody) ? new $classOrBody() : new Message($classOrBody);
            $message->setMetadata($basicData[MessageInterface::JSON_METADATA_KEY]);
            $message->setMetadata('SqsMessageId', $messageSqsData['MessageId']);
            $message->setMetadata('ReceiptHandle', $messageSqsData['ReceiptHandle']);

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
                    $this->logError('Action class '. $message->getAction() .' does not exist');
                    return null;
                }
            }

            return $message;
        }

        // if we have an invalid message:
        // - just a simple string in the body which is not json-decode-able
        // - it's a valid json but not containing the 'body' key
        $this->deleteMessage($messageSqsData['ReceiptHandle'], [
            'SqsMessageId' => $messageSqsData['MessageId']
        ]);

        return null;
    }

    public function acknowledgeMessage(MessageInterface $message)
    {
        $this->deleteMessage($message->getMetadata('ReceiptHandle'), [
            'InternalMessageId' => $message->getId(),
            'SqsMessageId' => $message->getMetadata('SqsMessageId')
        ]);
    }

    /**
     * Delete a message by its receipt.
     *
     * @param string $receipt
     * @param array $logContext
     */
    private function deleteMessage($receipt, array $logContext)
    {
        // ensures that the SQS Queue exist
        if (!$this->queueExists()) {
            $this->createQueue();
        }

        $logContext = array_merge([
            'QueueUrl' => $this->queueUrl
        ], $logContext);

        try {
            $this->client->deleteMessage([
                'QueueUrl' => $this->queueUrl,
                'ReceiptHandle' => $receipt
            ]);

            $this->logDebug('Message deleted from queue.', $logContext);
        } catch (AwsException $e) {
            $this->logError('Deleting message failed with MSG: '. $e->getAwsErrorMessage(), $logContext);
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        // ensures that the SQS Queue exist
        if (!$this->queueExists()) {
            $this->createQueue();
        }

        try {
            $result = $this->client->getQueueAttributes([
                'QueueUrl' => $this->queueUrl,
                'AttributeNames' => ['ApproximateNumberOfMessages'],
            ]);

            if (isset($result['Attributes']['ApproximateNumberOfMessages'])) {
                return (int) $result['Attributes']['ApproximateNumberOfMessages'];
            }
        } catch (AwsException $e) {
            $this->logError('Counting messages failed with MSG: '. $e->getAwsErrorMessage());
        }

        return 0;
    }

    /**
     * Checks if queue exists
     *
     * @return bool
     */
    private function queueExists()
    {
        if (isset($this->queueUrl)) {
            return true;
        }

        if ($this->getCache()->has($this->getUrlCacheKey())) {
            $this->queueUrl = $this->getCache()->get($this->getUrlCacheKey());
            return true;
        }

        try {
            $result = $this->client->getQueueUrl([
                'QueueName' => $this->getNameWithPrefix()
            ]);

            $this->queueUrl = $result->get('QueueUrl');

            if ($result->hasKey('QueueUrl')) {
                $this->queueUrl = $result->get('QueueUrl');
            } else {
                $this->logError('Queue url for'. $this->getNameWithPrefix() .' not fetched');
            }

            if ($this->queueUrl !== null) {
                $this->getCache()->put($this->getUrlCacheKey(), $this->queueUrl);
                $this->logDebug('Queue url '. $this->queueUrl .' fetched and cached');
                return true;
            }
        } catch (AwsException $e) {
            $this->logError('Fetching queue url for '. $this->getNameWithPrefix() .' failed. MSG: '. $e->getAwsErrorMessage());
        }

        return false;
    }

    /**
     * @return string
     */
    private function getUrlCacheKey()
    {
        return $this->getNameWithPrefix() .'_url';
    }

    /**
     * SQS can return max 10 messages at once.
     *
     * @return int
     */
    protected function getMessagesToReceive()
    {
        return parent::getMessagesToReceive() > 10 ? 10 : parent::getMessagesToReceive();
    }
}
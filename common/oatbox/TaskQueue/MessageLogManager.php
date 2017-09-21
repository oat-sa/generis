<?php

namespace oat\oatbox\TaskQueue;

use common_report_Report as Report;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\TaskQueue\MessageLogBroker\MessageLogBrokerInterface;
use oat\oatbox\log\LoggerAwareTrait;

/**
 * Managing message/task logs:
 * - storing every information for a message/task like dates, status changes, reports etc.
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
final class MessageLogManager extends ConfigurableService implements MessageLogManagerInterface
{
    use LoggerAwareTrait;

    const OPTION_MESSAGE_LOG_BROKER = 'log_broker';
    const OPTION_MESSAGE_LOG_BROKER_CONFIG = 'log_broker_config';

    /**
     * @var MessageLogBrokerInterface
     */
    private $broker;

    /**
     * MessageLogManager constructor.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        parent::__construct($options);

        if (!$this->hasOption(self::OPTION_MESSAGE_LOG_BROKER)) {
            throw new \InvalidArgumentException("Message Log Broker needs to be set.");
        }

        if(!is_a($this->getOption(self::OPTION_MESSAGE_LOG_BROKER), MessageLogBrokerInterface::class, true)) {
            throw new \InvalidArgumentException('Message Log Broker must implement ' . MessageLogBrokerInterface::class);
        }
    }

    /**
     * Gets the message log broker. It will be created if it has not been initialized.
     *
     * @return MessageLogBrokerInterface
     */
    public function getBroker()
    {
        if (is_null($this->broker)) {
            $brokerClass = $this->getOption(self::OPTION_MESSAGE_LOG_BROKER);
            $this->broker = new $brokerClass($this->getOption(self::OPTION_MESSAGE_LOG_BROKER_CONFIG));
        }

        return $this->broker;
    }

    /**
     * Inserts a new message log for a message/task with a specified status.
     *
     * @param MessageInterface $message
     * @param string           $status
     * @return $this
     */
    public function add(MessageInterface $message, $status)
    {
        try {
            $this->validateStatus($status);

            $this->getBroker()->add($message, $status);
        } catch (\Exception $e) {
            $this->logError('Adding result for item '. $message->getId() .' failed with MSG: '. $e->getMessage());
        }

        return $this;
    }

    /**
     * @param string $messageId
     * @param string $status
     * @return $this
     */
    public function setStatus($messageId, $status)
    {
        try {
            $this->validateStatus($status);

            return $this->getBroker()->updateStatus($messageId, $status);
        } catch (\Exception $e) {
            $this->logError('Setting the status for item '. $messageId .' failed with MSG: '. $e->getMessage());
        }

        return $this;
    }

    /**
     * @param string $messageId
     * @return string
     */
    public function getStatus($messageId)
    {
        try {
            return $this->getBroker()->getStatus($messageId);
        } catch (\Exception $e) {
            $this->logError('Getting status for item '. $messageId .' failed with MSG: '. $e->getMessage());
        }

        return self::MESSAGE_STATUS_UNKNOWN;
    }

    /**
     * Running status can be set for those message logs only which have the MESSAGE_STATUS_DEQUEUED status.
     *
     * @param string $messageId
     * @return $this
     */
    public function saveRunningStatus($messageId)
    {
        try {
            return $this->getBroker()->updateStatus($messageId, self::MESSAGE_STATUS_RUNNING, self::MESSAGE_STATUS_DEQUEUED);
        } catch (\Exception $e) {
            $this->logError('Setting the running status for item '. $messageId .' failed with MSG: '. $e->getMessage());
        }

        return $this;
    }

    /**
     * @param string $messageId
     * @param Report $report
     * @param null   $status
     * @return $this
     */
    public function setReport($messageId, Report $report, $status = null)
    {
        try {
            $this->validateStatus($status);

            $this->getBroker()->addReport($messageId, $report, $status);

        } catch (\Exception $e) {
            $this->logError('Setting report for item '. $messageId .' failed with MSG: '. $e->getMessage());
        }

        return $this;
    }

    /**
     * @param string $messageId
     * @return Report
     */
    public function getReport($messageId)
    {
        try {
            return $this->getBroker()->getReport($messageId);
        } catch (\Exception $e) {
            $this->logError('Getting report for item '. $messageId .' failed with MSG: '. $e->getMessage());
        }

        return Report::createFailure(__('Fetching report failed.'));
    }

    /**
     * @param $status
     */
    private function validateStatus($status)
    {
        $statuses = [
            self::MESSAGE_STATUS_ENQUEUED,
            self::MESSAGE_STATUS_DEQUEUED,
            self::MESSAGE_STATUS_RUNNING,
            self::MESSAGE_STATUS_COMPLETED,
            self::MESSAGE_STATUS_FAILED,
            self::MESSAGE_STATUS_ARCHIVED
        ];

        if (!in_array($status, $statuses)) {
            throw new \InvalidArgumentException('Status "'. $status .'"" is not a valid task queue status.');
        }
    }
}
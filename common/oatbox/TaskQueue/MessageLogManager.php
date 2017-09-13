<?php

namespace oat\oatbox\TaskQueue;

use common_report_Report as Report;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\TaskQueue\MessageLogBroker\MessageLogBrokerInterface;
use oat\oatbox\log\LoggerAwareTrait;

final class MessageLogManager extends ConfigurableService implements MessageLogManagerInterface
{
    use LoggerAwareTrait;

    const OPTION_MESSAGE_LOG_BROKER = 'log_broker';
    const OPTION_MESSAGE_LOG_BROKER_CONFIG = 'log_broker_config';

    /**
     * @var MessageLogBrokerInterface
     */
    private $broker;

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

    public function getBroker()
    {
        if (is_null($this->broker)) {
            $brokerClass = $this->getOption(self::OPTION_MESSAGE_LOG_BROKER);
            $this->broker = new $brokerClass($this->getOption(self::OPTION_MESSAGE_LOG_BROKER_CONFIG));
        }

        return $this->broker;
    }

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

    public function getStatus($messageId)
    {
        try {
            return $this->getBroker()->getStatus($messageId);
        } catch (\Exception $e) {
            $this->logError('Getting status for item '. $messageId .' failed with MSG: '. $e->getMessage());
        }

        return self::MESSAGE_STATUS_UNKNOWN;
    }

    public function saveRunningStatus($messageId)
    {
        try {
            return $this->getBroker()->updateStatus($messageId, self::MESSAGE_STATUS_RUNNING, self::MESSAGE_STATUS_DEQUEUED);
        } catch (\Exception $e) {
            $this->logError('Setting the running status for item '. $messageId .' failed with MSG: '. $e->getMessage());
        }

        return $this;
    }

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
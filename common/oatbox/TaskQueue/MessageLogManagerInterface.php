<?php

namespace oat\oatbox\TaskQueue;

use common_report_Report as Report;
use oat\oatbox\TaskQueue\MessageLogBroker\MessageLogBrokerInterface;
use Psr\Log\LoggerAwareInterface;

/**
 * Interface MessageLogManagerInterface
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
interface MessageLogManagerInterface extends LoggerAwareInterface
{
    const SERVICE_ID = 'generis/taskQueueMessageLogManager';

    const MESSAGE_STATUS_ENQUEUED = 'enqueued';
    const MESSAGE_STATUS_DEQUEUED = 'dequeued';
    const MESSAGE_STATUS_RUNNING = 'running';
    const MESSAGE_STATUS_COMPLETED = 'completed';
    const MESSAGE_STATUS_FAILED = 'failed';
    const MESSAGE_STATUS_ARCHIVED = 'archived';
    const MESSAGE_STATUS_UNKNOWN = 'unknown';

    /**
     * @return MessageLogBrokerInterface
     */
    public function getBroker();

    /**
     * Insert a new message/task with status into the result container.
     *
     * @param MessageInterface $message
     * @param string $status
     * @return mixed
     */
    public function add(MessageInterface $message, $status);

    /**
     * Set a status for a message.
     *
     * @param string $messageId
     * @param string $status
     * @return int
     */
    public function setStatus($messageId, $status);

    /**
     * Gets the status of a message.
     *
     * @param string $messageId
     * @return string
     */
    public function getStatus($messageId);

    /**
     * Saves specifically only the running status as an atomic way.
     *
     * If it returns 1, it means the given task was successfully updated and it can be processed.
     * If it returns 0, it means the given task has been already updated by another worker so it does not have be processed.
     *
     * @param string $messageId
     * @return int
     */
    public function saveRunningStatus($messageId);

    /**
     * Saves the report and a status for a message.
     *
     * @param string $messageId
     * @param Report $report
     * @param null   $status
     * @return mixed
     */
    public function setReport($messageId, Report $report, $status = null);

    /**
     * Gets the report for a message if that exists.
     *
     * @param string $messageId
     * @return Report|null
     */
    public function getReport($messageId);
}
<?php

namespace oat\oatbox\TaskQueue\MessageLogBroker;

use common_report_Report as Report;
use oat\oatbox\TaskQueue\MessageInterface;

/**
 * Interface MessageLogBrokerInterface
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
interface MessageLogBrokerInterface
{
    const CONFIG_CONTAINER_NAME = 'container_name';

    /**
     * MessageLogBrokerInterface constructor.
     *
     * @param array $config
     */
    public function __construct(array $config);

    /**
     * Creates the container where the message logs will be stored.
     */
    public function createContainer();

    /**
     * Inserts a new message log with status for a message/task.
     *
     * @param MessageInterface $message
     * @param string $status
     */
    public function add(MessageInterface $message, $status);

    /**
     * Find a message log by id which is the message id itself.
     *
     * @param string $messageId
     * @return array
     */
    public function findById($messageId);

    /**
     * Update the status of a message/task.
     *
     * The previous status can be used for querying the record.
     *
     * @param string $messageId
     * @param string $newStatus
     * @param string|null $prevStatus
     */
    public function updateStatus($messageId, $newStatus, $prevStatus = null);

    /**
     * Gets the status of a message.
     *
     * @param string $messageId
     * @return string
     */
    public function getStatus($messageId);

    /**
     * Add a report for a message/task.
     * New status can be supplied as well.
     *
     * @param string $messageId
     * @param Report $report
     * @param null   $status
     * @return int
     */
    public function addReport($messageId, Report $report, $status = null);

    /**
     * Gets a report for a message/task.
     *
     * @param string $messageId
     * @return Report|null
     */
    public function getReport($messageId);
}
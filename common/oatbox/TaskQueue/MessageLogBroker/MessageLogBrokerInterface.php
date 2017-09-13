<?php

namespace oat\oatbox\TaskQueue\MessageLogBroker;

use common_report_Report as Report;
use oat\oatbox\TaskQueue\MessageInterface;

interface MessageLogBrokerInterface
{
    const CONFIG_CONTAINER_NAME = 'container_name';

    public function __construct(array $config);

    public function createContainer();

    /**
     * Add a new result with status.
     *
     * @param MessageInterface $message
     * @param string $status
     */
    public function add(MessageInterface $message, $status);

    /**
     * @param $messageId
     * @return array
     */
    public function findById($messageId);

    /**
     * Update a result with the given task status.
     *
     * The previous status can be used for querying the record.
     *
     * @param string $messageId
     * @param string $newStatus
     * @param string|null $prevStatus
     */
    public function updateStatus($messageId, $newStatus, $prevStatus = null);

    public function getStatus($messageId);

    public function addReport($messageId, Report $report, $status = null);

    /**
     * @param string $messageId
     * @return Report|null
     */
    public function getReport($messageId);
}
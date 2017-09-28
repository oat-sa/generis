<?php

namespace oat\oatbox\TaskQueue;

use common_report_Report as Report;
use oat\oatbox\task\Task;
use Psr\Log\LoggerAwareInterface;

interface TaskLogInterface extends LoggerAwareInterface
{
    const SERVICE_ID = 'generis/TaskLog';

    const CONFIG_PERSISTENCE = 'persistence';
    const CONFIG_CONTAINER_NAME = 'container_name';

    /**
     * Creates the container where the task reports and metadata will be stored.
     */
    public function createContainer();

    /**
     * Inserts a new record with status for a task.
     *
     * @param Task $task
     * @return bool
     */
    public function add(Task $task);

    /**
     * Set a status for a task.
     *
     * @param string $id
     * @param string $newStatus
     * @return int
     */
    public function setStatus($id, $newStatus);

    /**
     * Gets the status of a task.
     *
     * @param string $id
     * @return string
     */
    public function getStatus($id);

    /**
     * Saves the report for a message.
     *
     * @param string $id
     * @param Report $report
     * @return bool
     */
    public function setReport($id, Report $report);

    /**
     * Gets the report for a message if that exists.
     *
     * @param string $id
     * @return Report|null
     */
    public function getReport($id);
}
<?php

namespace oat\oatbox\task;

use common_report_Report as Report;
use oat\oatbox\task\TaskInterface\TaskPersistenceInterface;
use oat\oatbox\task\TaskInterface\TaskQueue;
use oat\oatbox\TaskQueue\TaskLog;

/**
 * Class LegacyTaskLog
 *
 * Retrieves status and report from both container: the new one and the old queue
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
final class LegacyTaskLog extends TaskLog
{
    /** @var  TaskPersistenceInterface */
    private $getLegacyQueuePersistence;

    /**
     * @return TaskPersistenceInterface
     */
    private function getLegacyQueuePersistence()
    {
        if (is_null($this->getLegacyQueuePersistence)) {
            /** @var TaskQueue $queue */
            $queue = $this->getServiceManager()->get(Queue::SERVICE_ID);
            $this->getLegacyQueuePersistence =  $queue->getPersistence();
        }

        return $this->getLegacyQueuePersistence;
    }

    /**
     * @param string $id
     * @return string
     */
    public function getStatus($id)
    {
        try {
            // check the new storage first
            if ($status = parent::getStatus($id)) {
                return $status;
            }

            // if there is no task in the new storage, check the old one
            if ($task = $this->getLegacyQueuePersistence()->get($id)) {
                return $task->getStatus();
            }
        } catch (\Exception $e) {
            $this->logError('Getting status for task '. $id .' failed with MSG: '. $e->getMessage());
        }

        return '';
    }

    /**
     * @param string $id
     * @return null|Report
     */
    public function getReport($id)
    {
        try {
            // check the new storage first
            if ($report = parent::getReport($id)) {
                return $report;
            }

            // if there is no report in the new storage, check the old one
            if ($task = $this->getLegacyQueuePersistence()->get($id)) {
                return $task->getReport();
            }
        } catch (\Exception $e) {
            $this->logError('Getting report for task '. $id .' failed with MSG: '. $e->getMessage());
        }

        return null;
    }
}
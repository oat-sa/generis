<?php

namespace oat\generis\scripts\TaskQueue;

use oat\oatbox\action\Action;
use oat\oatbox\task\Queue;
use oat\oatbox\task\Task;
use oat\oatbox\TaskQueue\TaskLogInterface;
use oat\Taskqueue\Persistence\RdsQueue;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Migrates the data (reports and metadata) of the already existing tasks from the queue storage
 * into the new task log container
 *
 * ```
 * $ sudo -u www-data php index.php 'oat\generis\scripts\TaskQueue\MigrateTaskData'
 * ```
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
class MigrateTaskData implements Action, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function __invoke($params)
    {
        try {
            /** @var RdsQueue $queue */
            $queue = $this->getServiceLocator()->get(Queue::SERVICE_ID);

            /** @var TaskLogInterface $taskLogService */
            $taskLogService = $this->getServiceLocator()->get(TaskLogInterface::SERVICE_ID);

            $i = 0;

            foreach ($queue->getPersistence()->getAll() as $task) {
                if($taskLogService->add($task)) {
                    $i++;
                }
            }

            return \common_report_Report::createSuccess('Migrated '. $i .' tasks');
        } catch (\Exception $e) {
            return \common_report_Report::createFailure($e->getMessage());
        }
    }
}


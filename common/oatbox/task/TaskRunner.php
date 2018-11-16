<?php
/**  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2014-2017 (original work) Open Assessment Technologies SA;
 *
 */
namespace oat\oatbox\task;
 
use oat\oatbox\action\ActionService;
use oat\oatbox\task\TaskInterface\TaskQueue;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use \oat\oatbox\task\TaskInterface\TaskRunner as TaskRunnerInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use common_report_Report as Report;

/**
 * @deprecated since version 7.10.0, to be removed in 8.0. Use any implementation of \oat\tao\model\taskQueue\Worker\WorkerInterface instead.
 */
class TaskRunner implements TaskRunnerInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     */
    public function run(Task $task) {

        \common_Logger::d('Running task '.$task->getId());
        $report = new Report(\common_report_Report::TYPE_INFO, __('Running task %s at %s', $task->getId(), microtime(true)));
        /** @var TaskQueue $queue */
        $queue = $this->getServiceLocator()->get(Queue::SERVICE_ID);
        $queue->updateTaskStatus($task->getId(), Task::STATUS_RUNNING);
        try {
            $actionService = $this->getServiceLocator()->get(ActionService::SERVICE_ID);
            $invocable = $task->getInvocable();
            if (is_string($invocable)) {
                $invocable = $actionService->resolve($task->getInvocable());
            } else if ($invocable instanceof ServiceLocatorAwareInterface) {
                $invocable->setServiceLocator($this->getServiceLocator());
            }
            $subReport = call_user_func($invocable, $task->getParameters());
            $report->add($subReport);
            $report->setMessage($report->getMessage() . '; ' . __('Finished at %s', microtime(true)));
        } catch (\Exception $e) {
            $message = __('Failed at %s; Error message: %s', microtime(true), $e->getMessage());
            \common_Logger::e($message);
            $report->setType(Report::TYPE_ERROR);
            $report->setMessage($report->getMessage() . '; ' . $message);
        }
        $queue->updateTaskStatus($task->getId(), Task::STATUS_FINISHED);
        $queue->updateTaskReport($task->getId(), $report);
        return $report; 
    }

}

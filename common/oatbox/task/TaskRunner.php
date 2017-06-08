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
 
use oat\oatbox\service\ServiceManager;
use oat\oatbox\action\ActionService;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use \oat\oatbox\task\TaskInterface\TaskRunner as TaskRunnerInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class TaskRunner implements TaskRunnerInterface
{
    use ServiceLocatorAwareTrait;

    public function run(Task $task) {

        \common_Logger::d('Running task '.$task->getId());
        $report = new \common_report_Report(\common_report_Report::TYPE_INFO, __('Running task %s', $task->getId()));
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
        } catch (\Exception $e) {
            $message = 'Task ' . $task->getId() . ' failed. Error message: ' . $e->getMessage();
            \common_Logger::e($message);
            $report = new \common_report_Report(\common_report_Report::TYPE_ERROR, $message);
        }
        $queue->updateTaskStatus($task->getId(), Task::STATUS_FINISHED);
        $queue->updateTaskReport($task->getId(), $report);
        return $report; 
    }

}

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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */
namespace oat\oatbox\task;
 
use oat\oatbox\service\ServiceManager;
use oat\oatbox\action\ActionService;

class TaskRunner
{
    public function run(Task $task) {
        
        \common_Logger::d('Running task '.$task->getId());
        $report = new \common_report_Report(\common_report_Report::TYPE_INFO, __('Running task %s', $task->getId()));
        $queue = $this->getServiceLocator()->get(Queue::CONFIG_ID);
        $queue->updateTaskStatus($task->getId(), Task::STATUS_RUNNING);
        try {
            $actionService = $this->getServiceLocator()->get(ActionService::SERVICE_ID);
            $invocable = $actionService->resolve($task->getInvocable());
            $subReport = call_user_func($invocable, $task->getParameters());
            $report->add($subReport);
        } catch (\Exception $e) {
            \common_Logger::e('Task ' . $task->getId() . ' failed. Error message: ' . $e->getMessage());
            $report = new \common_report_Report(\common_report_Report::TYPE_ERROR, __('Unable to run task %s', $task->getId()));
        }
        $queue->updateTaskStatus($task->getId(), Task::STATUS_FINISHED, $report);
        return $report; 
    }
    
    public function getServiceLocator()
    {
        return ServiceManager::getServiceManager();
    }
}

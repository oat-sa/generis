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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\oatbox\task\implementation;

use oat\oatbox\task\AbstractQueue;
use oat\oatbox\task\Task;
use oat\oatbox\task\TaskRunner;
use \common_report_Report as Report;

/**
 * Class SyncQueue
 *
 * Basic implementation of task queue. Created task will be executed immediately after creation
 * in the same process.
 *
 * Usage example:
 * ```
 * $queue = \oat\oatbox\service\ServiceManager::getServiceManager()->get(SyncQueue::CONFIG_ID);
 * $queue->createTask(new SendReport(), [$resource->getUri()]); //task will be created and executed immediately
 * ```
 *
 * @package oat\oatbox\task\implementation
 * @author Aleh Hutnikau, <huntikau@1pt.com>
 *
 * @deprecated since version 7.10.0, to be removed in 8.0. Use \oat\tao\model\taskQueue\QueueDispatcher instead.
 */
class SyncQueue extends AbstractQueue
{

    /**
     * @var TaskRunner
     */
    protected $taskRunner;

    /**
     * Create and run task
     *
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *
     * @param \oat\oatbox\action\Action|string $action action instance, classname or callback function
     * @param array $parameters parameters to be passed to the action
     * @param boolean $recall Parameter which indicates that task has been created repeatedly after fail of previous.
     * For current implementation in means that the second call will not be executed to avoid loop.
     * @param null|string $label
     * @param null|string $type
     * @return SyncTask
     */
    public function createTask($action, $parameters, $recall = false , $label = null , $type = null)
    {
        if ($recall) {
            \common_Logger::w("Repeated call of action'; Execution canceled.");
            return false;
        }
        $task = new SyncTask($action, $parameters);
        $task->setLabel($label);
        $task->setType($type);
        $this->getPersistence()->add($task);
        $this->runTask($task);
        return $task;
    }

    /**
     * not implemented
     *
     * @deprecated since version 7.10.0, to be removed in 8.0.
     */
    public function getIterator()
    {
        return new TaskList($this->getPersistence()->getAll());
    }

    /**
     * Create task resource in the rdf storage and link placeholder resource to it.
     *
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *             
     * @param Task $task
     * @param \core_kernel_classes_Resource|null $resource - placeholder resource to be linked with task.
     * @throws
     * @return \core_kernel_classes_Resource
     */
    public function linkTask(Task $task, \core_kernel_classes_Resource $resource = null)
    {
        $taskResource = parent::linkTask($task, $resource);
        $report = $task->getReport();
        if (!empty($report)) {
            //serialize only two first report levels because sometimes serialized report is huge and it does not fit into `k_po` index of statemetns table.
            $serializableReport = new Report($report->getType(), $report->getMessage(), $report->getData());
            foreach ($report as $subReport) {
                $serializableSubReport = new Report($subReport->getType(), $subReport->getMessage(), $subReport->getData());
                $serializableReport->add($serializableSubReport);
            }
            $taskResource->setPropertyValue(
                new \core_kernel_classes_Property(Task::PROPERTY_REPORT),
                json_encode($serializableReport)
            );
        }
        return $taskResource;
    }

}

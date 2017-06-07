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
 */
class SyncQueue extends AbstractQueue
{

    /**
     * @var TaskRunner
     */
    protected $taskRunner;

    /**
     * Create and run task
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
     */
    public function getIterator()
    {
        return new TaskList($this->getPersistence()->getAll());
    }



}

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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\oatbox\task\implementation;


use oat\oatbox\task\Queue;
use oat\oatbox\task\Task;
use oat\oatbox\task\TaskInterface\TaskPersistenceInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class SyncQueuePersistence implements TaskPersistenceInterface
{

    use ServiceLocatorAwareTrait;

    /**
     * @var array
     */
    protected $taskList = [];

    public function get($taskId)
    {
        if(array_key_exists($taskId , $this->taskList)) {
            return $this->taskList[$taskId];
        }
        return null;
    }

    public function add(Task $task)
    {
        $taskId = (microtime(true) * 1000) . rand(10000 , 99999);
        $task->setId($taskId);
        $this->taskList[$taskId] = $task;
        return $task;
    }

    public function search(task $filterTask, $limit, $offset)
    {

        $taskList = array_filter($this->taskList , function($elem) use($filterTask){
            /**
             * @var $elem Task
             */

            if(!is_null($filterTask->getStatus())) {
                $result = ($elem->getStatus() === $filterTask->getStatus());
            } else {
                $result = ($elem->getStatus() !== Task::STATUS_ARCHIVED);
            }

            if(!is_null($filterTask->getType())) {
                $result = ($elem->getType() === $filterTask->getType());
            }

            if(!is_null($filterTask->getOwner())) {
                $result = ($elem->getOwner() === $filterTask->getOwner());
            }

            if(!is_null($filterTask->getLabel())) {
                $result = (strpos(strtolower($filterTask->getLabel()) , strtotime($elem->getLabel())) !== false);
            }

            return $result;
        });

        return new TaskList($taskList);
    }

    public function has($taskId)
    {
        return !is_null($this->get($taskId));
    }

    public function update($taskId, $status)
    {
        $task = $this->get($taskId);
        if(!is_null($task)) {
            $task->setStatus($status);
            return true;
        }
        return false;
    }

    public function setReport($taskId, \common_report_Report $report)
    {
        $task = $this->get($taskId);
        if(!is_null($task)) {
            $task->setReport($report);
            return true;
        }
        return false;
    }



}
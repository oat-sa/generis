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

use oat\oatbox\task\Task;
use oat\oatbox\task\TaskInterface\TaskPersistenceInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * @deprecated since version 7.10.0, to be removed in 8.0. Use \oat\tao\model\taskQueue\Queue\Broker\InMemoryQueueBroker instead.
 */
class InMemoryQueuePersistence implements TaskPersistenceInterface
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
        $taskId = (microtime(true) * 10000) . rand(100000 , 999999);
        $task->setId($taskId);
        $this->taskList[$taskId] = $task;
        return $task;
    }

    public function search(array $filterTask, $rows = null, $page = null , $sortBy = null , $sortOrder = null)
    {

        $taskList = array_filter($this->taskList , function($elem) use($filterTask){
            /**
             * @var $elem Task
             */

            if(isset($filterTask['status'])) {
                $result = ($elem->getStatus() === $filterTask['status']);
            } else {
                $result = ($elem->getStatus() !== Task::STATUS_ARCHIVED);
            }

            if(isset($filterTask['type'])) {
                $result = ($elem->getType() === $filterTask['type']);
            }

            if(isset($filterTask['owner'])) {
                $result = ($elem->getOwner() === $filterTask['owner']);
            }

            if(isset($filterTask['label'])) {
                $result = (strpos(strtolower($filterTask['label']) , strtotime($elem->getLabel())) !== false);
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

    public function count(array $params)
    {
        return count($this->search($params));
    }


    public function getAll()
    {
        return $this->taskList;
    }

}
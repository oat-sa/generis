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

interface Queue extends \IteratorAggregate
{
    /**
     * @deprecated since 3.15.2
     */
    const CONFIG_ID = 'generis/taskqueue';

    const SERVICE_ID = 'generis/taskqueue';

    const FILE_SYSTEM_ID = 'taskQueueStorage';

    /**
     * @param $actionId
     * @param $parameters
     * @param $label
     * @param $task
     * @param boolean $repeatedly Whether task created repeatedly (for example when execution of task was failed and task puts to the queue again).
     * @return mixed
     */
    public function createTask($actionId, $parameters, $repeatedly = false , $label = null , $task = null);
    
    public function getIterator();

    public function updateTaskStatus($taskId, $status);

    public function updateTaskReport($taskId, $report);

    /**
     * Get task instance by id
     * @param $taskId
     * @return Task
     */
    public function getTask($taskId);

}

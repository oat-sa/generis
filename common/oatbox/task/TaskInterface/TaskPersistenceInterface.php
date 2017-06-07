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

namespace oat\oatbox\task\TaskInterface;


use oat\oatbox\task\Task;
use Zend\ServiceManager\ServiceLocatorAwareInterface;


/**
 * Interface TaskPersistenceInterface
 * @package oat\oatbox\task\TaskInterface
 */

interface TaskPersistenceInterface extends ServiceLocatorAwareInterface

{

    /**
     * @param $taskId
     * @return Task
     */
    public function get($taskId);

    /**
     * @param Task $task
     * @return boolean
     */
    public function add(Task $task);

    /**
     * @param array $filterTask
     * @param null $rows
     * @param null $page
     * @param null $sortBy
     * @param null $sortOrder
     * @return array
     */

    public function search(array $filterTask, $rows = null, $page = null , $sortBy = null , $sortOrder = null);

    /**
     * @param $taskId
     * @return boolean
     */
    public function has($taskId);

    /**
     * @param $taskId
     * @param $status
     * @return boolean
     */
    public function update($taskId , $status);

    /**
     * @param $taskId
     * @param \common_report_Report $report
     * @return boolean
     */
    public function setReport($taskId , \common_report_Report $report);

    /**
     * @param array $params
     * @return int
     */
    public function count(array $params);

    /**
     * @return array
     */
    public function getAll();

}
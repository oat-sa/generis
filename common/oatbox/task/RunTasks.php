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

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\action\Action;

/**
 * Class RunTasks is used to run tasks in queue.
 *
 * Run example:
 * ```
 * sudo -u www-data php index.php 'oat\oatbox\task\RunTasks' 10
 * ```
 * @deprecated since version 7.10.0, to be removed in 8.0. Use \oat\taoTaskQueue\scripts\tools\RunWorker instead.
 *
 * @package oat\oatbox\task
 */
class RunTasks extends ConfigurableService implements Action
{
    /**
     * @var array
     */
    protected $params = [];

    /**
     * @param array $params
     *              $params[0] (int) tasks limit. If parameter is not given or equals 0 then all tasks in queue will be executed.
     * @return \common_report_Report
     */
    public function __invoke($params)
    {
        $this->params = $params;
        $limit = $this->getLimit();
        $taskService = new TaskService([TaskService::OPTION_LIMIT => $limit]);
        $taskService->setServiceLocator($this->getServiceLocator());
        $report = $taskService->runQueue();
        return $report; 
    }

    /**
     * Get max amount of tasks to run.
     * @return int
     */
    protected function getLimit()
    {
        $limit = isset($this->params[0]) ? $this->params[0] : 0;
        return (integer) $limit;
    }
}

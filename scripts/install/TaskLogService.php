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

namespace oat\generis\scripts\install;

use oat\oatbox\extension\InstallAction;
use oat\oatbox\TaskQueue\TaskLog;

/**
 * Class TaskLogService
 *
 * Action to initialize task log service
 *
 * @package oat\generis\scripts\install
 * @author Gyula Szucs <gyula@taotesting.com>
 */
class TaskLogService extends InstallAction
{
    /**
     * Install action
     */
    public function __invoke($params)
    {
        // use the new storage for task reports and status for new install
        $taskLogService = new TaskLog([
            TaskLog::CONFIG_PERSISTENCE => 'default',
            TaskLog::CONFIG_CONTAINER_NAME => 'task_log'
        ]);
        $this->registerService(TaskLog::SERVICE_ID, $taskLogService);

        try{
            $taskLogService->createContainer();
        } catch (\Exception $e) {
            return \common_report_Report::createFailure('Creating task log container failed');
        }

        return \common_report_Report::createSuccess('Task log service registered.');
    }
}
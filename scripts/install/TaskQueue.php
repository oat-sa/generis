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

use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\task\Queue;

/**
 * Class TaskQueue
 *
 * Action to initialize task queue
 *
 * @package oat\generis\scripts\install
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class TaskQueue extends \common_ext_action_InstallAction
{

    /**
     * Install action
     */
    public function __invoke($params = [])
    {

        $fsm = $this->getServiceManager()->get(FileSystemService::SERVICE_ID);
        $fsm->createFileSystem(Queue::FILE_SYSTEM_ID, Queue::FILE_SYSTEM_ID);
        $this->getServiceManager()->register(FileSystemService::SERVICE_ID, $fsm);

        return new \common_report_Report(\common_report_Report::TYPE_SUCCESS, 'Task queue storage registered.');
    }
}
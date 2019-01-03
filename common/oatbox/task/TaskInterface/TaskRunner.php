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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 *
 */
namespace oat\oatbox\task\TaskInterface;


use oat\oatbox\task\Task;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * @deprecated since version 7.10.0, to be removed in 8.0. Use \oat\tao\model\taskQueue\Worker\WorkerInterface instead.
 */
interface TaskRunner extends ServiceLocatorAwareInterface
{
    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *
     * @param Task $task
     * @return \common_report_Report
     */
    public function run(Task $task);


}
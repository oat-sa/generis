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


use oat\oatbox\task\Queue;
use oat\oatbox\task\Task;

/**
 * @deprecated since version 7.10.0, to be removed in 8.0. Use \oat\tao\model\taskQueue\QueueDispatcher instead.
 */
interface TaskQueue extends Queue
{
    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *
     * @return TaskPersistenceInterface
     */
    public function getPersistence();

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *
     * @param TaskPersistenceInterface $persistence
     * @return $this
     */
    public function setPersistence(TaskPersistenceInterface $persistence);

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *
     * @param TaskRunner $runner
     * @return $this
     */
    public function setRunner(TaskRunner $runner);

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *
     * @return TaskRunner
     */
    public function getRunner();

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *
     * @param Task $task
     * @return mixed
     */
    public function runTask(Task $task);

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *
     * @param $currentUserId
     * @return TaskPayLoad
     */
    public function getPayload($currentUserId);

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *
     * Get resource from rdf storage which represents task in the task queue by linked resource
     * Returns null if there is no task linked to given resource
     * @param \core_kernel_classes_Resource $resource
     * @return mixed
     */
    public function getTaskResource(\core_kernel_classes_Resource $resource);

    /**
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *
     * @param \core_kernel_classes_Resource $resource
     * @return \common_report_Report
     */
    public function getReportByLinkedResource(\core_kernel_classes_Resource $resource);

    /**
     * Create task resource in the rdf storage and link placeholder resource to it.
     *
     * @deprecated since version 7.10.0, to be removed in 8.0.
     *
     * @param Task $task
     * @param \core_kernel_classes_Resource|null $resource - placeholder resource to be linked with task.
     */
    public function linkTask(Task $task, \core_kernel_classes_Resource $resource = null);

}
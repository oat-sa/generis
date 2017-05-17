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

namespace oat\oatbox\task;

use oat\oatbox\service\ConfigurableService;
use oat\generis\model\OntologyAwareTrait;

/**
 * Class AbstractQueue

 * @package oat\oatbox\task
 * @author Aleh Hutnikau, <huntikau@1pt.com>
 */
abstract class AbstractQueue extends ConfigurableService implements Queue
{
    use OntologyAwareTrait;

    /**
     * Check whether resource is a placeholder of task in the task queue
     * @param \core_kernel_classes_Resource $resource
     * @return mixed
     */
    public function isTaskPlaceholder(\core_kernel_classes_Resource $resource)
    {
        $tasksRootClass = new \core_kernel_classes_Class(Task::TASK_CLASS);
        $task = $tasksRootClass->searchInstances([Task::PROPERTY_LINKED_RESOURCE => $resource->getUri()]);
        return !empty($task);
    }

    /**
     * @param \core_kernel_classes_Resource $resource
     * @return \common_report_Report
     */
    public function getReportByLinkedResource(\core_kernel_classes_Resource $resource)
    {
        $tasksRootClass = new \core_kernel_classes_Class(Task::TASK_CLASS);
        $taskResource = $tasksRootClass->searchInstances([Task::PROPERTY_LINKED_RESOURCE => $resource->getUri()]);
        if (!empty($taskResource)) {
            $taskResource = current($taskResource);
            $report = $taskResource->getOnePropertyValue($this->getProperty(Task::PROPERTY_REPORT));
            if ($report) {
                $report = \common_report_Report::jsonUnserialize($report->literal);
            } else {
                $task = $this->getTask($taskResource->getUri());
                if ($task) {
                    $report = \common_report_Report::createInfo(__('Import task is in \'%s\' state', $task->getStatus()));
                } else {
                    //this is an assumption.
                    //in case if sync implementation is used task may not be found.
                    $report = \common_report_Report::createInfo(__('Import task is in progress'));
                }
            }
        } else {
            $report = \common_report_Report::createFailure(__('Resource is not the task placeholder'));
        }
        return $report;
    }

    /**
     * Create task resource in the rdf storage and link placeholder resource to it.
     * @param Task $task
     * @param \core_kernel_classes_Resource|null $resource - placeholder resource to be linked with task.
     * @return \core_kernel_classes_Resource
     */
    public function linkTask(Task $task, \core_kernel_classes_Resource $resource = null)
    {
        $taskResource = new \core_kernel_classes_Resource($task->getId());
        if (!$taskResource->exists()) {
            $tasksRootClass = new \core_kernel_classes_Class(Task::TASK_CLASS);
            $taskResource = $tasksRootClass->createInstance('', '', $task->getId());
        }

        if ($resource !== null) {
            $taskResource->setPropertyValue(
                new \core_kernel_classes_Property(Task::PROPERTY_LINKED_RESOURCE),
                $resource->getUri()
            );
        }
        return $taskResource;
    }
}

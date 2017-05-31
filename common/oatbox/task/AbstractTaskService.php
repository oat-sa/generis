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

namespace oat\oatbox\task;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\task\TaskInterface\TaskListIterator;
use oat\oatbox\task\TaskInterface\TaskPersistenceInterface;
use oat\oatbox\task\TaskInterface\TaskQueue;
use oat\oatbox\task\TaskInterface\TaskRunner;

abstract class AbstractTaskService
    extends ConfigurableService
    implements TaskQueue
{
    /**
     * @var TaskPersistenceInterface
     */
    protected $persistence;

    /**
     * @var TaskRunner
     */
    protected $runner;

    /**
     * @var TaskListIterator
     */
    protected $iterator;

    /**
     * @var string
     */
    protected $taskClassName;

    /**
     * @return Task
     */
    protected function taskFactory() {

        $className = $this->taskClassName;

        return new $className();

    }

    public function createTask($actionId, $parameters, $repeatedly = false, $label = null, $task = null)
    {

        $task = $this->taskFactory();
        $task->setInvocable($actionId);
        $task->setParameters($parameters);
        $task->setLabel($label);

        return $this->getPersistence()->add($task);
    }

    public function getIterator()
    {
        return $this->iterator;
    }

    public function updateTaskStatus($taskId, $status)
    {
        $this->getPersistence()->update($taskId, $status);
    }

    public function updateTaskReport($taskId, $report)
    {
        $this->getPersistence()->setReport($taskId, $report);
    }

    public function getTask($taskId)
    {
        return $this->getPersistence()->get($taskId);
    }

    public function getPersistence()
    {
        return $this->persistence;
    }

    public function setPersistence(TaskPersistenceInterface $persistence)
    {
        $this->persistence = $persistence;
        return $this;
    }

    public function setRunner(TaskRunner $runner)
    {
        $this->runner = $runner;
        return $this;
    }

    public function getRunner()
    {
        return $this->runner;
    }


}
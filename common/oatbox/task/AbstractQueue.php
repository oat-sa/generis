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

namespace oat\oatbox\task;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\task\Exception\BadTaskQueueOption;
use oat\oatbox\task\TaskInterface\TaskPayLoad;
use oat\oatbox\task\TaskInterface\TaskPersistenceInterface;
use oat\oatbox\task\TaskInterface\TaskQueue;
use oat\oatbox\task\TaskInterface\TaskRunner as TaskRunnerInterface;

/**
 * Class AbstractQueue
 * generic abstract queue object
 * @package oat\oatbox\task
 */
abstract class AbstractQueue
    extends ConfigurableService
    implements TaskQueue
{

    /**
     * @var TaskRunner
     */
    protected $runner;

    /**
     * @var TaskPersistenceInterface
     */
    protected $persistence;

    /**
     * AbstractQueue constructor.
     *
     * config exemple :
     *  'payload'     => payload class name,
     * 'runner'      => task runner class name,
     * 'persistence' => persistence class name,
     * 'config'      => [
     * persistence options array
     * custom in function of needs
     * ],
     *
     * @param array $options
     * @throws BadTaskQueueOption
     */
    public function __construct(array $options = array())
    {
        parent::__construct($options);
        if($this->hasOption('runner')) {
            $classRunner       = $this->getOption('runner');
            if(!is_a($classRunner , TaskRunnerInterface::class,true)) {
                throw new BadTaskQueueOption('task runner must implement ' . TaskRunnerInterface::class);
            }
            $this->runner      = new $classRunner();
        }

        if($this->hasOption('persistence') && $this->hasOption('config')) {
            $classPersistence = $this->getOption('persistence');
            if(!is_a($classPersistence , TaskPersistenceInterface::class, true)) {
                throw new BadTaskQueueOption('task persistence must implement ' . TaskPersistenceInterface::class);
            }
            $configPersistence = $this->getOption('config');
            $this->persistence = new $classPersistence($configPersistence);
        }

    }

    /**
     * set task runner
     * @param TaskRunnerInterface $runner
     * @return $this
     */
    public function setRunner(TaskRunnerInterface $runner)
    {
        $this->runner = $runner;
        return $this;
    }

    /**
     * set task persistence
     * @param TaskPersistenceInterface $persistence
     * @return $this
     */
    public function setPersistence(TaskPersistenceInterface $persistence)
    {
        $this->persistence = $persistence;
        return $this;
    }

    /**
     * return task runner
     * @return TaskRunner
     */
    public function getRunner()
    {
        $this->runner->setServiceLocator($this->getServiceLocator());
        return $this->runner;
    }

    /**
     * return task persistence
     * @return TaskPersistenceInterface
     */
    public function getPersistence()
    {
        $this->persistence->setServiceLocator($this->getServiceLocator());
        return $this->persistence;
    }

    /**
     * change task status using  task persistence
     * @param $taskId
     * @param $status
     * @return self
     */
    public function updateTaskStatus($taskId, $status)
    {
        if ($this->getPersistence()->has($taskId)) {
            $this->getPersistence()->update($taskId , $status);
        }
        return $this;
    }

    /**
     * set task report using  task persistence
     * @param $taskId
     * @param $report
     * @return self
     */
    public function updateTaskReport($taskId, $report)
    {
        if ($this->getPersistence()->has($taskId)) {
            $this->getPersistence()->setReport($taskId, $report);
        }
        return $this;
    }

    /**
     * get task from persistence
     * @param $taskId
     * @return Task
     */
    public function getTask($taskId)
    {
        return $this->getPersistence()->get($taskId);

    }

    /**
     * execute task with task runner
     * @param Task $task
     * @return mixed
     */
    public function runTask(Task $task)
    {
        return $this->getRunner()->run($task);
    }

    /**
     * return a new instance of payload
     * @param $currentUserId
     * @return TaskPayLoad
     * @throws BadTaskQueueOption
     */
    public function getPayload($currentUserId = null)
    {
        $class = $this->getOption('payload');
        if(!is_a($class , TaskPayLoad::class , true)) {
            throw new BadTaskQueueOption('task payload must implement ' . TaskPayLoad::class);
        }
        /**
         * @var $payload TaskPayLoad
         */
        $payload = new $class($this->getPersistence() , $currentUserId);
        $payload->setServiceLocator($this->getServiceLocator());
        /**
         * @var TaskPayLoad $payload
         */
        return $payload;
    }



}
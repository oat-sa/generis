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
use oat\oatbox\task\TaskInterface\TaskPayLoad;
use oat\oatbox\task\TaskInterface\TaskPersistenceInterface;
use oat\oatbox\task\TaskInterface\TaskQueue;
use oat\oatbox\task\TaskInterface\TaskRunner;

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


    public function __construct(array $options = array())
    {
        parent::__construct($options);

        $classRunner       = $this->getOption('runner');
        $this->runner      = new $classRunner();

        $classPersistence  = $this->getOption('persistence');
        $configPersistence  = $this->getOption('config');

        $this->persistence = new $classPersistence($configPersistence);

    }

    /**
     * @param TaskRunner $runner
     * @return $this
     */
    public function setRunner(TaskRunner $runner)
    {
        $this->runner = $runner;
        return $this;
    }

    /**
     * @param TaskPersistenceInterface $persistence
     * @return $this
     */
    public function setPersistence(TaskPersistenceInterface $persistence)
    {
        $this->persistence = $persistence;
        return $this;
    }

    /**
     * @return TaskRunner
     */
    public function getRunner()
    {
        $this->runner->setServiceLocator($this->getServiceManager());
        return $this->runner;
    }

    /**
     * @return TaskPersistenceInterface
     */
    public function getPersistence()
    {
        $this->persistence->setServiceLocator($this->getServiceManager());
        return $this->persistence;
    }

    /**
     * @param $currentUserId
     * @return TaskPayLoad
     */
    public function getPayload($currentUserId = null)
    {
        $class = $this->getOption('payload');
        $payload = new $class($this->persistence , $currentUserId);
        $payload->setServiceLocator($this->getServiceManager());
        /**
         * @var TaskPayLoad $payload
         */
        return $payload;
    }



}
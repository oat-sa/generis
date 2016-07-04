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
 * 
 */
namespace oat\generis\test\oatbox\task;

use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\oatbox\task\implementation\SyncQueue;
use oat\oatbox\task\implementation\SyncTask;
use Prophecy\Prophet;
use Prophecy\Argument;

/**
 * Class SyncQueueTest
 *
 * @author Aleh Hutnikau, <huntikau@1pt.com>
 */
class SyncQueueTest extends GenerisPhpUnitTestRunner
{

    protected $prophet;

    public function testCreateTask()
    {
        $queue = $this->getQueue();
        $this->assertTrue($queue instanceof SyncQueue);
        $task = $queue->createTask('Action', []);
        $this->assertTrue($task instanceof SyncTask);
        $this->assertEquals($task->getStatus(), SyncTask::STATUS_FINISHED);
        $this->assertInstanceOf(SyncTask::class, $queue->createTask('Action', [], true));

        $this->prophet->checkPredictions();

    }

    public function testGetIterator()
    {
        //not implemented
        $queue = new SyncQueue();
        $this->assertTrue($queue->getIterator() instanceof \EmptyIterator);
    }

    public function testUpdateTaskStatus()
    {
        $queue = new SyncQueue();
        $task = $queue->createTask('Action', []);
        $this->assertEquals(SyncTask::STATUS_CREATED, $task->getStatus());
        $queue->updateTaskStatus($task->getId(), SyncTask::STATUS_RUNNING);
        $this->assertEquals(SyncTask::STATUS_RUNNING, $task->getStatus());
    }

    protected function getQueue()
    {
        $queue = new SyncQueue();
        $reflectionObject = new \ReflectionObject($queue);
        $this->prophet = new Prophet();
        $taskRunnerProphet = $this->prophet->prophesize('oat\oatbox\task\TaskRunner');
        $taskRunnerProphet->run(Argument::type('oat\oatbox\task\implementation\SyncTask'))
            ->shouldBeCalledTimes(2)
            ->will(function ($args) use($queue) {
            $task = $args[0];
            $queue->updateTaskStatus($task->getId(), SyncTask::STATUS_FINISHED);
        });
        $taskRunner = $taskRunnerProphet->reveal();

        $taskRunnerProp = $reflectionObject->getProperty('taskRunner');
        $taskRunnerProp->setAccessible(true);
        $taskRunnerProp->setValue($queue, $taskRunner);
        return $queue;
    }
}

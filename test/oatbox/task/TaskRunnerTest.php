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
namespace oat\generis\test\oatbox\task;

use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\oatbox\task\TaskRunner;
use Prophecy\Argument;
use Prophecy\Prophet;

class TaskRunnerTest extends \PHPUnit_Framework_TestCase
{
    private $prophet;

    protected function setup()
    {
        $this->prophet = new Prophet();
    }

    protected function tearDown()
    {
        $this->prophet->checkPredictions();
    }

    /**
     * Test of oat\oatbox\task\TaskRunner::run()
     */
    public function testRun()
    {
        $taskRunner = new TaskRunner();
        $task = $this->getTask();
        $report = $taskRunner->run($task);
        $this->assertEquals([], $report->getErrors());
    }

    /**
     * @return \oat\oatbox\task\Task
     */
    private function getTask()
    {
        $invocableReport = new \common_report_Report(\common_report_Report::TYPE_INFO, 'Invocable Called');

        $taskInvocableProphecy = $this->prophet->prophesize('oat\oatbox\action\Action');
        $taskInvocableProphecy->__invoke(Argument::is(['foo', 'bar']))
            ->shouldBeCalledTimes(1)
            ->willReturn($invocableReport);

        $taskProphecy = $this->prophet->prophesize('oat\oatbox\task\Task');
        $taskProphecy->getId()->shouldBeCalled()->willReturn('testTask');
        $taskProphecy->getInvocable()->shouldBeCalled()->willReturn($taskInvocableProphecy->reveal());
        $taskProphecy->getParameters()->shouldBeCalled()->willReturn(['foo', 'bar']);

        return $taskProphecy->reveal();
    }
}

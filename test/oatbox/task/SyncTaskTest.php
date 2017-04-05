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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\generis\test\oatbox\task;

use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\oatbox\task\implementation\SyncTask;

/**
 * @author Aleh Hutnikau, <huntikau@1pt.com>
 */
class SyncTaskTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $invocable = 'invocable/Action';
        $params = ['key' => 'val', 2, 'three'];
        $task = new SyncTask($invocable, $params);
        $this->assertEquals($invocable, $task->getInvocable());
        $this->assertEquals($params, $task->getParameters());
        $this->assertEquals(SyncTask::STATUS_CREATED, $task->getStatus());
        $this->assertTrue(\common_Utils::isUri($task->getId()));
    }

    public function testGetId()
    {
        $task = new SyncTask('invocable/Action', []);
        $this->assertTrue(\common_Utils::isUri($task->getId()));
    }

    public function testGetInvocable()
    {
        $task = new SyncTask('invocable/Action', []);
        $this->assertEquals('invocable/Action', $task->getInvocable());
    }

    public function testGetStatus()
    {
        $task = new SyncTask('invocable/Action', []);
        $this->assertEquals(SyncTask::STATUS_CREATED, $task->getStatus());
        $task->setStatus(SyncTask::STATUS_FINISHED);
        $this->assertEquals(SyncTask::STATUS_FINISHED, $task->getStatus());
    }

    public function testSetStatus()
    {
        $task = new SyncTask('invocable/Action', []);
        $task->setStatus(SyncTask::STATUS_RUNNING);
        $this->assertEquals(SyncTask::STATUS_RUNNING, $task->getStatus());
    }

    public function testGetParameters()
    {
        $params = ['key' => 'val', 2, 'three'];
        $task = new SyncTask('invocable/Action', $params);
        $this->assertEquals($params, $task->getParameters());
        $task->setParameters(['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $task->getParameters());
    }

    public function testSetParameters()
    {
        $task = new SyncTask('invocable/Action', []);
        $this->assertEquals([], $task->getParameters());
        $task->setParameters(['foo', 'bar']);
        $this->assertEquals(['foo', 'bar'], $task->getParameters());
    }
}

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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\generis\test\integration\mutex;

use oat\generis\test\TestCase;
use oat\oatbox\mutex\LockService;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\mutex\NoLockStorage;

/**
 * Class LockServiceTest
 * @package oat\generis\test\integration\mutex
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class LockServiceTest extends TestCase
{

    public function testLock()
    {
        $service = $this->getInstance();
        if ($this->isNoLockConfigured($service)) {
            $this->markTestSkipped('No lock storage configured for lock service. Skip integration test.');
        }
        $actionId1 = 'action_1';
        $actionId2 = 'action_2';
        $sleep = 3;
        $time = time();
        $pipe1 = popen('php ' . __DIR__ . DIRECTORY_SEPARATOR . 'test_action.php ' . $actionId1 . ' ' . $sleep . ' 0', 'w');
        $pipe2 = popen('php ' . __DIR__ . DIRECTORY_SEPARATOR . 'test_action.php ' . $actionId1 . ' ' . $sleep . ' 0', 'w');
        $pipe3 = popen('php ' . __DIR__ . DIRECTORY_SEPARATOR . 'test_action.php ' . $actionId1 . ' ' . $sleep . ' 0', 'w');
        $pipe4 = popen('php ' . __DIR__ . DIRECTORY_SEPARATOR . 'test_action.php ' . $actionId2 . ' ' . $sleep . ' 0', 'w');
        pclose($pipe1);
        pclose($pipe2);
        pclose($pipe3);
        pclose($pipe4);
        $this->assertTrue((time() - $time) >= ($sleep * 3));
        $this->assertTrue((time() - $time) < ($sleep * 4));
    }

    public function testLockTimeout()
    {
        $service = $this->getInstance();
        if ($this->isNoLockConfigured($service)) {
            $this->markTestSkipped('No lock storage configured for lock service. Skip integration test.');
        }
        $actionId1 = 'action_1';
        $sleep = 5;
        $timeout = 2;
        $time = time();
        $pipe1 = popen('php ' . __DIR__ . DIRECTORY_SEPARATOR . 'test_action.php ' . $actionId1 . ' ' . $sleep . ' ' . $timeout, 'w');
        $pipe2 = popen('php ' . __DIR__ . DIRECTORY_SEPARATOR . 'test_action.php ' . $actionId1 . ' ' . $sleep . ' ' . $timeout, 'w');
        pclose($pipe1);
        pclose($pipe2);

        /**
         * Process A:
         * acquireLock      timeout              close
         *      |      2 sec   |    3 sec          |
         *      ------------------------------------
         *
         * Process B:
         * acquireLock       start                              close
         *      |      wait    |    5 sec                         |
         *                     ------------------------------------
         *
         * Total time:
         * |   2 + 5 = 7 seconds                                  |
         * --------------------------------------------------------
         */

        $consumedTime = time() - $time;

        $this->assertTrue($consumedTime > $sleep);
        $this->assertTrue($consumedTime < ($sleep * 2));
    }

    /**
     * @return LockService|\oat\oatbox\service\ConfigurableService
     */
    public function getInstance()
    {
        return ServiceManager::getServiceManager()->get(LockService::class);
    }

    /**
     * @param $service
     * @return bool
     * @throws \ReflectionException
     */
    private function isNoLockConfigured($service)
    {
        $reflectionClass = new \ReflectionClass($service->getOption($service::OPTION_PERSISTENCE_CLASS));
        return $reflectionClass->getName() === NoLockStorage::class;
    }
}

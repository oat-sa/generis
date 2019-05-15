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

namespace oat\generis\test\unit\oatbox\mutex;

use oat\generis\test\TestCase;
use oat\oatbox\mutex\LockService;
use oat\oatbox\service\ServiceManager;

/**
 * Class LockServiceTest
 * @package oat\generis\test\integration\mutex
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class LockServiceTest extends TestCase
{

    private $lockDir = __DIR__.DIRECTORY_SEPARATOR.'flock';

    public function setUp()
    {
        if (!is_dir($this->lockDir)) {
            mkdir($this->lockDir);
        }
    }

    public function testLock()
    {
        $actionId1 = 'action_1';
        $actionId2 = 'action_2';
        $sleep = 3;
        $this->getInstance();
        $time = time();
        $pipe1 = popen('php ' . __DIR__ . DIRECTORY_SEPARATOR . 'test_action.php ' . $actionId1 . ' ' . $sleep, 'w');
        $pipe2 = popen('php ' . __DIR__ . DIRECTORY_SEPARATOR . 'test_action.php ' . $actionId1 . ' ' . $sleep, 'w');
        $pipe3 = popen('php ' . __DIR__ . DIRECTORY_SEPARATOR . 'test_action.php ' . $actionId1 . ' ' . $sleep, 'w');
        $pipe4 = popen('php ' . __DIR__ . DIRECTORY_SEPARATOR . 'test_action.php ' . $actionId2 . ' ' . $sleep, 'w');
        pclose($pipe1);
        pclose($pipe2);
        pclose($pipe3);
        pclose($pipe4);
        $this->assertTrue((time() - $time) >= ($sleep*3));
        $this->assertTrue((time() - $time) < ($sleep*4));
    }

    /**
     * @return LockService
     * @throws \common_Exception
     * @throws \common_exception_NotImplemented
     */
    public function getInstance()
    {
        $config = new \common_persistence_KeyValuePersistence([], new \common_persistence_InMemoryKvDriver());
        $config->set(\common_persistence_Manager::SERVICE_ID, new \common_persistence_Manager);
        $serviceManager = new ServiceManager($config);

        $service = new LockService([
            LockService::OPTION_PERSISTENCE => $this->lockDir
        ]);
        $service->setServiceLocator($serviceManager);
        $service->install();
        return $service;
    }

}
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
 */

namespace oat\generis\test\unit\common\oatbox\event;

use oat\generis\test\TestCase;
use oat\oatbox\event\EventManager;
use oat\oatbox\event\GenericEvent;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceManager;

class EventManagerTest extends TestCase
{
    public function testTriggerEventWithRegisteredService()
    {
        $listeners = [
            'fixture-event' => [
                [
                    ConfigurableEventListenerMock::SERVICE_ID, 'listen'
                ]
            ]
        ];

        $eventManager = $this->getEventManager($listeners);
        $eventManager->getServiceLocator()->register(ConfigurableEventListenerMock::SERVICE_ID, new ConfigurableEventListenerMock());

        $eventManager->trigger(new FixtureEvent());
        $this->assertTrue(ConfigurableEventListenerMock::$listened);
    }

    public function testTriggerEventWithClassName()
    {
        $listeners = [
            'fixture-event' => [
                [
                    ConfigurableEventListenerMock::class, 'listen'
                ]
            ]
        ];
        $this->getEventManager($listeners)->trigger(new FixtureEvent());
        $this->assertTrue(ConfigurableEventListenerMock::$listened);
    }

    public function testTriggerEventWithNotConfigurableClassName()
    {
        $listeners = [
            'fixture-event' => [
                [
                    WildEventListenerMock::class, 'staticListen'
                ]
            ]
        ];
        $this->getEventManager($listeners)->trigger(new FixtureEvent());
        $this->assertTrue(WildEventListenerMock::$listened);
    }

    /**
     * @param array $listeners
     * @return EventManager
     */
    protected function getEventManager(array $listeners)
    {
        $eventManager = new EventManager([
            EventManager::OPTION_LISTENERS => $listeners
        ]);
        $eventManager->setServiceLocator(new ServiceManager(new \common_persistence_InMemoryKvDriver()));
        return $eventManager;
    }
}

class WildEventListenerMock
{
    public static $listened = false;

    static public function staticListen()
    {
        self::$listened = true;
    }
}

class ConfigurableEventListenerMock extends ConfigurableService
{
    const SERVICE_ID = 'fixture/toto';

    public static $listened = false;

    static public function listen()
    {
        self::$listened = true;
    }
}

class FixtureEvent extends GenericEvent
{
    public function __construct()
    {
        parent::__construct('fixture-event');
    }
}
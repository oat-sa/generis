<?php

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
                    EventListenerMock::SERVICE_ID, 'listen'
                ]
            ]
        ];

        $eventManager = $this->getEventManager($listeners);
        $eventManager->getServiceLocator()->register(EventListenerMock::SERVICE_ID, new EventListenerMock());

        $eventManager->trigger(new FixtureEvent());
        $this->assertTrue(EventListenerMock::$listened);
    }

    public function testTriggerEventWithClassName()
    {
        $listeners = [
            'fixture-event' => [
                [
                    EventListenerMock::class, 'listen'
                ]
            ]
        ];
        $this->getEventManager($listeners)->trigger(new FixtureEvent());
        $this->assertTrue(EventListenerMock::$listened);
    }

    protected function getEventManager(array $listeners)
    {
        $eventManager = new EventManager([
            EventManager::OPTION_LISTENERS => $listeners
        ]);
        $eventManager->setServiceLocator(new ServiceManager(new \common_persistence_InMemoryKvDriver()));
        return $eventManager;
    }
}

class EventListenerMock extends ConfigurableService
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
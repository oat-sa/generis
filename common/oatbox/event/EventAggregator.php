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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */
declare(strict_types=1);

namespace oat\oatbox\event;

use oat\oatbox\log\LoggerAwareTrait;
use oat\oatbox\service\ConfigurableService;
use Psr\Log\LoggerAwareInterface;

class EventAggregator extends ConfigurableService implements LoggerAwareInterface
{
    const SERVICE_ID = 'generis/eventAggregator';

    use LoggerAwareTrait;

    /** @var int */
    private $numberOfAggregatedEvents;

    /** @var array */
    private $events = [];

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->numberOfAggregatedEvents = $options['numberOfAggregatedEvents'];
    }

    public function put(string $eventId, Event $event): void
    {
        $this->events[$eventId] = $event;

        if (count($this->events) >= $this->numberOfAggregatedEvents) {
            $this->triggerAggregatedEvents();
        }
    }

    public function triggerAggregatedEvents(): void
    {
        $countEvents = count($this->events);

        if ($countEvents < 1) {
            return;
        }

        $this->logInfo(sprintf('Triggering %d aggregated events', $countEvents));

        $eventManager = $this->getEventManager();
        foreach ($this->events as $event) {
            $eventManager->trigger($event);
        }

        $this->events = [];
    }

    public function getEventManager(): EventManager
    {
        return $this->getServiceLocator()->get(EventManager::SERVICE_ID);
    }
}

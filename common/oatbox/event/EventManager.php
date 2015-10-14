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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\oatbox\event;

use oat\oatbox\service\ConfigurableService;
/**
 * The simple placeholder ServiceManager
 * @author Joel Bout <joel@taotesting.com>
 */
class EventManager extends ConfigurableService
{
    const CONFIG_ID = 'generis/event';
    
    const OPTION_LISTENERS = 'listeners';
    
    public function trigger($event, $params = array()) {
        $eventObject = is_object($event) ? $event : new GenericEvent($event, $params);
        foreach ($this->getListeners($eventObject) as $callback) {
            call_user_func($callback, $eventObject);
        }
    }
    
    public function attach($event, $callback) {
        $eventObject = is_object($event) ? $event : new GenericEvent($event);
        $listeners = $this->getOption(self::OPTION_LISTENERS);
        if (!isset($listeners[$eventObject->getName()])) {
            $listeners[$eventObject->getName()] = array();
        }
        $listeners[$eventObject->getName()][] = $callback;
        $this->setOption(self::OPTION_LISTENERS, $listeners);
    }
    
    protected function getListeners($eventObject) {
        $listeners = $this->getOption(self::OPTION_LISTENERS);
        return isset($listeners[$eventObject->getName()])
            ? $listeners[$eventObject->getName()]
            : array();
    }
}

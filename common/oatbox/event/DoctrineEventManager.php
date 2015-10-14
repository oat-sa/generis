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
use Doctrine\Common\EventManager;
use Doctrine\Common\EventArgs;
/**
 * The simple placeholder ServiceManager
 * @author Joel Bout <joel@taotesting.com>
 */
class DoctrineEventManager extends ConfigurableService
{
    const CONFIG_ID = 'generis/event';
    
    const OPTION_LISTENERS = 'listeners';
    
    /**
     * 
     * @var EventManager
     */
    private $eventManager;
    
    public function trigger($event, $params = array()) {
        $eventObject = is_object($event) ? $event : new GenericEvent($event);
        $eventArgs = new EventArgs();
        $this->getRealEventManager()->dispatchEvent($eventObject->getName(), $eventArgs);
    }
    
    public function attach($event, $callback) {
        $eventObject = is_object($event) ? $event : new GenericEvent($event);
        $this->getRealEventManager()->addEventListener(array($eventObject->getName()), $callback);
        
        $listeners = $this->getOptions(self::OPTION_LISTENERS);
        if (!isset($listeners[$eventObject->getName()])) {
            $listeners[$eventObject->getName()] = array();
        }
        $listeners[$eventObject->getName()][] = $callback;
        $this->setOption(self::OPTION_LISTENERS, $listeners);
    }
    
    /**
     * 
     * @return EventManager:
     */
    protected function getRealEventManager() {
        if (is_null($this->eventManager)) {
            $this->eventManager = new EventManager();
            foreach ($this->getOption(self::OPTION_LISTENERS) as $eventName => $listener) {
                $this->eventManager->addEventListener(array(eventName), $listener);
            }
        }
        return $this->eventManager;
    }
}

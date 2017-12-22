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
namespace oat\oatbox\extension;

use oat\oatbox\event\EventManager;
use oat\oatbox\service\ConfigurableService;

/**
 * New Abstract action containing some helper functions
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
abstract class InstallAction extends AbstractAction
{
    
    /**
     * Add a new event Listener
     *
     * @param mixed $event either an Event object or a string
     * @param Callable $callback
     */
    public function registerEvent($event, $callback)
    {
        $eventManager = $this->getServiceLocator()->get(EventManager::CONFIG_ID);
        $eventManager->attach($event, $callback);
        $this->getServiceManager()->register(EventManager::CONFIG_ID, $eventManager);
    }

}

<?php

namespace oat\generis\model\data\statistics;

use oat\generis\model\data\event\SessionRead;
use oat\oatbox\event\Event;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceManager;

/**
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; under version 2
 *  of the License (non-upgradable).
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 *  Copyright (c) 2017 (original work) Open Assessment Technologies SA
 *
 *
 */
class StatisticsManager extends ConfigurableService
{
    const SERVICE_ID = 'generis/statistics';
    const OPTION_PERSISTENCE = 'persistence';

    const KEY_LAST_ACCESS_TIME = 'generis:session:lastaccesstime';


    /**
     * @param Event $event
     * @throws \common_Exception
     * @throws \oat\oatbox\service\ServiceNotFoundException
     */
    public static function catchEvent(Event $event)
    {
        if ($event instanceof SessionRead) {
            $persistence = self::getPersistence();
            if ($persistence) {
                $persistence->set(self::KEY_LAST_ACCESS_TIME, time());
            }
        }
    }

    /**
     * @param $key
     * @return string
     * @throws \oat\oatbox\service\ServiceNotFoundException
     * @throws \common_Exception
     */
    public function get($key)
    {
        return self::getPersistence()->get($key);
    }

    /**
     * @return \common_persistence_KvDriver
     * @throws \oat\oatbox\service\ServiceNotFoundException
     * @throws \common_Exception
     */
    private static function getPersistence()
    {
        $sm = ServiceManager::getServiceManager();
        $persistenceOption = $sm->get(self::SERVICE_ID)->getOption(self::OPTION_PERSISTENCE);
        return $sm->get(\common_persistence_Manager::SERVICE_ID)->getPersistence($persistenceOption);
    }

}
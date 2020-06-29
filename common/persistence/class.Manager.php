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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

use oat\oatbox\service\ServiceManager;
use oat\oatbox\service\ServiceNotFoundException;
use oat\generis\persistence\PersistenceManager;

 /**
 * A backward compatibility wrapper for our persistence factory
 *
 * @author Lionel Lecaque  <lionel@taotesting.com>
 * @license GPLv2
 * @package generis
 * @deprecated use PersistenceManager
 */
class common_persistence_Manager extends PersistenceManager
{
    /** @deprecated */
    const SERVICE_KEY = 'generis/persistences';
    
    /**
     * @return common_persistence_Manager
     * @deprecated
     */
    protected static function getDefaultManager()
    {
        try {
            $manager = ServiceManager::getServiceManager()->get(self::SERVICE_ID);
        } catch (ServiceNotFoundException $ex) {
            $manager = new self([
                self::OPTION_PERSISTENCES => []
            ]);
            $manager->setServiceManager(ServiceManager::getServiceManager());
        }
        return $manager;
    }

    /**
     *
     * @param string $persistenceId
     * @return common_persistence_Persistence
     * @deprecated
     */
    public static function getPersistence($persistenceId)
    {
        return self::getDefaultManager()->getPersistenceById($persistenceId);
    }

    /**
     * Add a new persistence to the system
     *
     * @param string $persistenceId
     * @param array $persistenceConf
     * @deprecated
     */
    public static function addPersistence($persistenceId, array $persistenceConf)
    {
        $manager = self::getDefaultManager();
        $manager->registerPersistence($persistenceId, $persistenceConf);
        $manager->getServiceManager()->register(self::SERVICE_ID, $manager);
    }
}

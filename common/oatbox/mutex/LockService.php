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

namespace oat\oatbox\mutex;

use oat\oatbox\service\ConfigurableService;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\Lock\StoreInterface;
use Symfony\Component\Lock\Store\PdoStore;
use Symfony\Component\Lock\Store\RetryTillSaveStore;

/**
 * Class LockService
 *
 * Service is used to configure and create lock factory.
 * See https://symfony.com/doc/current/components/lock.html for more details
 *
 * @package oat\oatbox\mutex
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class LockService extends ConfigurableService
{
    const SERVICE_ID = 'generis/LockService';
    const OPTION_PERSISTENCE = 'persistence';

    /** @var Factory */
    private $factory;

    /** @var StoreInterface */
    private $store;

    /**
     * @return Factory
     * @throws \common_exception_NotImplemented
     */
    public function getLockFactory()
    {
        if ($this->factory === null) {
            $this->factory = new Factory(new RetryTillSaveStore($this->getStore()));
        }
        return $this->factory;
    }

    /**
     * @return StoreInterface
     * @throws \common_exception_NotImplemented
     */
    private function getStore()
    {
        if ($this->store === null) {
            $persistenceId = $this->getOption(self::OPTION_PERSISTENCE);
            $persistenceManager = $this->getServiceLocator()->get(\common_persistence_Manager::SERVICE_ID);
            if ($persistenceManager->hasPersistence($persistenceId)) {
                $this->store = $this->getPdoStore($persistenceId);
            } elseif (is_dir($persistenceId) && is_writable($persistenceId)) {
                $this->store = $this->getFlockStore($persistenceId);
            }
        }
        return $this->store;
    }

    /**
     * @throws \common_exception_NotImplemented
     */
    public function install()
    {
        if ($this->getStore() instanceof PdoStore) {
            try {
                $this->getStore()->createTable();
            } catch (\Doctrine\DBAL\DBALException $exception) {
                // the table could not be created for some reason
            }
        }
    }

    /**
     * @param $persistenceId
     * @return PdoStore
     * @throws \common_exception_NotImplemented
     */
    private function getPdoStore($persistenceId)
    {
        $persistenceManager = $this->getServiceLocator()->get(\common_persistence_Manager::SERVICE_ID);
        $persistence = $persistenceManager->getPersistenceById($persistenceId);
        if (!$persistence instanceof \common_persistence_SqlPersistence) {
            throw new \common_exception_NotImplemented('Only Sql persistence store supported by LockService');
        }
        return  new PdoStore($persistence->getDriver()->getDbalConnection());
    }

    /**
     * @param $persistenceId
     * @return FlockStore
     */
    private function getFlockStore($persistenceId)
    {
        return new FlockStore($persistenceId);
    }
}
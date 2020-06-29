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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA ;
 */

namespace oat\generis\scripts\update;

use common_persistence_Manager;
use common_persistence_SqlKvDriver;
use common_report_Report as Report;
use Exception;
use oat\oatbox\extension\InstallAction;

class RegisterDefaultKvPersistence extends InstallAction
{
    /**
     * @var Report
     */
    protected $report;

    /**
     * @inheritdoc
     */
    public function __invoke($params)
    {
        $this->report = new Report(Report::TYPE_INFO, 'Setup "default_kv" persistence...');

        try {
            /** @var common_persistence_Manager $persistenceManager */
            $persistenceManager = $this->getServiceLocator()->get(common_persistence_Manager::SERVICE_ID);
            if ($persistenceManager->hasPersistence('default_kv')) {
                $this->report->add(Report::createInfo('"default_kv" persistence is already configured.'));
            } else {
                $this->setupDefaultKvPersistence();
            }
        } catch (Exception $e) {
            $this->logError($e->getMessage());

            $this->report->add(Report::createFailure($e->getMessage()));
        }

        return $this->report;
    }

    /**
     * @throws \common_Exception
     */
    private function setupDefaultKvPersistence()
    {
        /** @var common_persistence_Manager $persistenceManager */
        $persistenceManager = $this->getServiceManager()->get(common_persistence_Manager::SERVICE_ID);

        $persistencesConfig = $persistenceManager->getOption('persistences');
        $persistenceCandidates = array_keys($persistencesConfig);
        array_unshift($persistenceCandidates, 'serviceState', 'redis');

        // By default if there is no KV persistence on the server fall back to RDS KV implementation
        $newPersistenceConfig = [
            'driver' => common_persistence_SqlKvDriver::class,
            common_persistence_SqlKvDriver::OPTION_PERSISTENCE_SQL => 'default'
        ];

        foreach ($persistenceCandidates as $persistenceId) {
            if ($this->canUsePersistence($persistenceId)) {
                $newPersistenceConfig = $persistencesConfig[$persistenceId];

                break;
            }
        }

        $persistenceManager->registerPersistence('default_kv', $newPersistenceConfig);
        $this->getServiceManager()->register(common_persistence_Manager::SERVICE_ID, $persistenceManager);

        $this->report->add(Report::createSuccess('"default_kv" persistence successfully configured.'));
    }

    /**
     * Check if persistence config can be used as "default_kv" persistence
     *
     * @param string $persistenceId
     * @return bool
     * @throws \oat\oatbox\service\exception\InvalidServiceManagerException
     */
    private function canUsePersistence($persistenceId)
    {
        /** @var common_persistence_Manager $persistenceManager */
        $persistenceManager = $this->getServiceManager()->get(common_persistence_Manager::SERVICE_ID);
        $persistencesConfig = $persistenceManager->getOption('persistences');
        if (
            !$persistenceManager->hasPersistence($persistenceId)
            || $persistencesConfig[$persistenceId]['driver'] == 'phpfile'
        ) {
            return false;
        }

        $persistence = $persistenceManager->getPersistenceById($persistenceId);
        if (!$persistence instanceof \common_persistence_KeyValuePersistence) {
            return false;
        }

        return true;
    }
}

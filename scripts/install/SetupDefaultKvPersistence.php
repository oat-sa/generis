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

namespace oat\generis\scripts\install;

use common_persistence_Manager;
use common_persistence_SqlKvDriver;
use common_report_Report as Report;
use Exception;
use oat\oatbox\extension\InstallAction;
use oat\generis\persistence\PersistenceManager;
use oat\generis\persistence\sql\SchemaProviderInterface;

class SetupDefaultKvPersistence extends InstallAction
{
    /**
     * @inheritdoc
     */
    public function __invoke($params)
    {
        $report = new Report(Report::TYPE_INFO, 'Setup "default_kv" persistence...');

        /** @var common_persistence_Manager $persistenceManager */
        $persistenceManager = $this->getServiceLocator()->get(common_persistence_Manager::SERVICE_ID);
        if ($persistenceManager->hasPersistence('default_kv')) {
            $report->add(Report::createInfo('"default_kv" persistence is already configured.'));
        } else {
            $newPersistenceConfig = [
                'driver' => common_persistence_SqlKvDriver::class,
                common_persistence_SqlKvDriver::OPTION_PERSISTENCE_SQL => 'default'
            ];
            $persistenceManager->registerPersistence('default_kv', $newPersistenceConfig);
            $report->add(Report::createInfo('Setup new "default_kv" persistence.'));
        }
        $schemaCollection = $persistenceManager->getSqlSchemas();
        $kvdriver = $persistenceManager->getPersistenceById('default_kv')->getDriver();
        if ($kvdriver instanceof SchemaProviderInterface) {
            $kvdriver->provideSchema($schemaCollection);
        }
        $persistenceManager->applySchemas($schemaCollection);

        return $report;
    }
}

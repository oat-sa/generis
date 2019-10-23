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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace   oat\generis\model\kernel\persistence\smoothsql\install;

use core_kernel_api_ModelFactory as ModelFactory;
use Doctrine\DBAL\Schema\Schema;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceManager;

/**
 * Helper to setup the required tables for generis smoothsql
 */
class SmoothRdsModel extends ConfigurableService
{
    /**
     * 
     * @param Schema $schema
     * @return \Doctrine\DBAL\Schema\Schema
     */
    public function addSmoothTables(Schema $schema)
    {
        /** @var ModelFactory $modelFactory */
        $modelFactory = ServiceManager::getServiceManager()->get(ModelFactory::SERVICE_ID);
        $modelFactory->createModelsTable($schema);
        $modelFactory->createStatementsTable($schema);
        return $schema;
    }
}

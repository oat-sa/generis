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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

declare(strict_types=1);

namespace oat\generis\model\DependencyInjection;

use oat\generis\model\data\Ontology;
use oat\generis\model\DependencyInjection\Poc\MyService;
use oat\generis\persistence\PersistenceManager;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\log\LoggerService;
use oat\tao\model\security\ActionProtector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class ContainerServiceProvider implements ContainerServiceProviderInterface
{
    public function __invoke(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();

        $services->set(LegacyServiceGateway::class, LegacyServiceGateway::class);

        //@TODO @FIXME Remove this service after tests
        $services->set(MyService::class, MyService::class)
            ->public()
            ->args(
                [
                    service(PersistenceManager::SERVICE_ID),
                    service(FileSystemService::SERVICE_ID),
                    service(LoggerService::SERVICE_ID),
                    service(Ontology::SERVICE_ID),
                    service(ServiceOptions::SERVICE_ID),
                    service(ActionProtector::SERVICE_ID)
                ]
            );
    }
}

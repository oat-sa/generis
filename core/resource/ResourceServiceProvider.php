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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\generis\model\resource;

use oat\oatbox\event\EventManager;
use oat\generis\model\data\Ontology;
use oat\generis\model\resource\Service\ResourceDeleter;
use oat\generis\model\resource\Repository\ClassRepository;
use oat\generis\model\resource\Repository\ResourceRepository;
use oat\generis\model\DependencyInjection\ContainerServiceProviderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class ResourceServiceProvider implements ContainerServiceProviderInterface
{
    public function __invoke(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();

        $services
            ->set(ClassRepository::class, ClassRepository::class)
            ->public()
            ->args(
                [
                    service(Ontology::SERVICE_ID),
                    service(EventManager::SERVICE_ID),
                ]
            );

        $services
            ->set(ResourceRepository::class, ResourceRepository::class)
            ->public()
            ->args(
                [
                    service(Ontology::SERVICE_ID),
                    service(EventManager::SERVICE_ID),
                ]
            );

        $services
            ->set(ResourceDeleter::class, ResourceDeleter::class)
            ->public()
            ->args(
                [
                    service(ResourceRepository::class),
                ]
            );
    }
}

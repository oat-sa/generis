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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Gabriel Felipe Soares <gabriel.felipe.soares@taotesting.com>
 */

declare(strict_types=1);

namespace oat\generis\persistence;

use common_persistence_sql_Platform;
use common_persistence_SqlPersistence;
use Doctrine\DBAL\Query\QueryBuilder;
use oat\generis\model\DependencyInjection\ContainerServiceProviderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

/**
 * @codeCoverageIgnore
 */
class PersistenceServiceProvider implements ContainerServiceProviderInterface
{
    public const DEFAULT_SQL_PERSISTENCE = common_persistence_SqlPersistence::class . '::default';
    public const DEFAULT_SQL_PLATFORM = common_persistence_sql_Platform::class . '::default';
    public const DEFAULT_QUERY_BUILDER = QueryBuilder::class . '::default';

    public function __invoke(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();

        $services->set(self::DEFAULT_SQL_PERSISTENCE, common_persistence_SqlPersistence::class)
            ->factory(
                [
                    service(PersistenceManager::SERVICE_ID),
                    'getPersistenceById'
                ]
            )
            ->args(
                [
                    'default'
                ]
            )
            ->public();

        $services->set(self::DEFAULT_SQL_PLATFORM, common_persistence_sql_Platform::class)
            ->factory(
                [
                    service(self::DEFAULT_SQL_PERSISTENCE),
                    'getPlatForm'
                ]
            );

        $services->set(self::DEFAULT_QUERY_BUILDER, QueryBuilder::class)
            ->factory(
                [
                    service(self::DEFAULT_SQL_PLATFORM),
                    'getQueryBuilder'
                ]
            )->share(false);
    }
}

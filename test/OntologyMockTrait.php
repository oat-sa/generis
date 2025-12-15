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
 * Copyright (c) 2018-2025 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\generis\test;

use common_session_Session;
use core_kernel_persistence_smoothsql_SmoothModel;
use oat\generis\model\data\Ontology;
use oat\generis\model\kernel\persistence\newsql\NewSqlOntology;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\generis\model\kernel\uri\Bin2HexUriProvider;
use oat\generis\model\kernel\uri\UriProvider;
use oat\generis\persistence\DriverConfigurationFeeder;
use oat\generis\persistence\PersistenceManager;
use oat\generis\persistence\sql\SchemaProviderInterface;
use oat\oatbox\cache\NoCache;
use oat\oatbox\cache\SimpleCache;
use oat\oatbox\event\EventAggregator;
use oat\oatbox\event\EventManager;
use oat\oatbox\log\LoggerService;
use oat\oatbox\session\SessionService;
use oat\oatbox\user\UserLanguageServiceInterface;
use Psr\Log\LoggerInterface;
use oat\oatbox\cache\PropertyCache;
use PHPUnit\Framework\MockObject\MockObject as PHPUnitMockObject;

trait OntologyMockTrait
{
    /**
     * @return NewSqlOntology
     */
    protected function getNewSqlMock()
    {
        $model = new NewSqlOntology([
            NewSqlOntology::OPTION_PERSISTENCE => 'mockSql',
            NewSqlOntology::OPTION_READABLE_MODELS => [2,3],
            NewSqlOntology::OPTION_WRITEABLE_MODELS => [2],
            NewSqlOntology::OPTION_NEW_TRIPLE_MODEL => 2,
        ]);
        return $this->setupOntology($model);
    }

    protected function getStarSqlMock()
    {
        $model = new \core_kernel_persistence_starsql_StarModel([
            \core_kernel_persistence_starsql_StarModel::OPTION_PERSISTENCE => 'neo4j',
            \core_kernel_persistence_starsql_StarModel::OPTION_SEARCH_SERVICE => ComplexSearchService::SERVICE_ID,
        ]);
        return $this->setupOntology($model);
    }

    /**
     * @return core_kernel_persistence_smoothsql_SmoothModel
     */
    protected function getOntologyMock()
    {
        $model = new core_kernel_persistence_smoothsql_SmoothModel([
            core_kernel_persistence_smoothsql_SmoothModel::OPTION_PERSISTENCE => 'mockSql',
            core_kernel_persistence_smoothsql_SmoothModel::OPTION_READABLE_MODELS => [2,3],
            core_kernel_persistence_smoothsql_SmoothModel::OPTION_WRITEABLE_MODELS => [2],
            core_kernel_persistence_smoothsql_SmoothModel::OPTION_NEW_TRIPLE_MODEL => 2,
        ]);
        return $this->setupOntology($model);
    }

    /**
     * @return Ontology
     */
    private function setupOntology(Ontology $onto)
    {
        $eventAggregator = new EventAggregator(['numberOfAggregatedEvents' => 10]);

        $persistenceManagerWithSqlMock = $this->getPersistenceManagerWithSqlMock('mockSql');
        $session = new \common_session_AnonymousSession();
        $eventManager = new EventManager();

        $serviceManagerMethod = method_exists($this, 'getServiceLocatorMock')
            ? 'getServiceLocatorMock'
            : 'getServiceManagerMock';

        $serviceLocatorMock = $this->$serviceManagerMethod([
            Ontology::SERVICE_ID => $onto,
            PersistenceManager::SERVICE_ID => $persistenceManagerWithSqlMock,
            UserLanguageServiceInterface::SERVICE_ID => $this->getUserLanguageServiceMock('xx_XX'),
            SessionService::SERVICE_ID => $this->getSessionServiceMock($session),
            EventManager::SERVICE_ID => $eventManager,
            LoggerService::SERVICE_ID => $this->createMock(LoggerInterface::class),
            UriProvider::SERVICE_ID => new Bin2HexUriProvider([
                Bin2HexUriProvider::OPTION_NAMESPACE => 'http://ontology.mock/bin2hex#'
            ]),
            SimpleCache::SERVICE_ID => new NoCache(),
            PropertyCache::SERVICE_ID => new NoCache(),
            DriverConfigurationFeeder::SERVICE_ID => new DriverConfigurationFeeder(),
            EventAggregator::SERVICE_ID => $eventAggregator
        ]);
        $eventAggregator->setServiceLocator($serviceLocatorMock);
        $session->setServiceLocator($serviceLocatorMock);
        $eventManager->setServiceLocator($serviceLocatorMock);
        $onto->setServiceLocator($serviceLocatorMock);
        $persistenceManagerWithSqlMock->setServiceLocator($serviceLocatorMock);

        // setup schema
        $schemas = $persistenceManagerWithSqlMock->getSqlSchemas();
        if ($onto instanceof SchemaProviderInterface) {
            $onto->provideSchema($schemas);
        }
        $persistenceManagerWithSqlMock->applySchemas($schemas);

        return $onto;
    }

    /**
     * @param string $sqlId
     * @return PersistenceManager
     */
    protected function getPersistenceManagerWithSqlMock($sqlId)
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('sqlite not found, tests skipped.');
        }

        return new PersistenceManager([
            PersistenceManager::OPTION_PERSISTENCES => [
                $sqlId => [
                    'driver' => 'dbal',
                    'connection' => [
                        'url' => 'sqlite:///:memory:'
                    ]
                ]
            ]
        ]);
    }

    private function getSessionServiceMock(common_session_Session $session): SessionService|PHPUnitMockObject
    {
        $sessionServiceMock = $this->createMock(SessionService::class);
        $sessionServiceMock
            ->method('getCurrentUser')
            ->willReturn($session->getUser());
        $sessionServiceMock
            ->method('getCurrentSession')
            ->willReturn($session);

        return $sessionServiceMock;
    }

    private function getUserLanguageServiceMock(string $lang): UserLanguageServiceInterface|PHPUnitMockObject
    {
        $userLanguageServiceMock = $this->createMock(UserLanguageServiceInterface::class);
        $userLanguageServiceMock
            ->method('getDefaultLanguage')
            ->willReturn($lang);
        $userLanguageServiceMock
            ->method('getInterfaceLanguage')
            ->willReturn($lang);

        return $userLanguageServiceMock;
    }
}

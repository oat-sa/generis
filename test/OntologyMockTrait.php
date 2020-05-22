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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\generis\test;

use oat\oatbox\user\UserLanguageServiceInterface;
use oat\oatbox\session\SessionService;
use Prophecy\Argument;
use common_session_Session;
use oat\oatbox\event\EventManager;
use Psr\Log\LoggerInterface;
use oat\oatbox\log\LoggerService;
use oat\generis\persistence\PersistenceManager;
use oat\generis\model\kernel\uri\UriProvider;
use oat\generis\model\kernel\uri\Bin2HexUriProvider;
use oat\generis\model\data\Ontology;
use core_kernel_persistence_smoothsql_SmoothModel;
use oat\generis\model\kernel\persistence\newsql\NewSqlOntology;
use oat\generis\persistence\sql\SchemaProviderInterface;
use oat\oatbox\cache\SimpleCache;
use oat\oatbox\cache\NoCache;

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
        $pm = $this->getPersistenceManagerWithSqlMock('mockSql');
        $session = new \common_session_AnonymousSession();
        $sl = $this->getServiceLocatorMock([
            Ontology::SERVICE_ID => $onto,
            PersistenceManager::SERVICE_ID => $pm,
            UserLanguageServiceInterface::SERVICE_ID => $this->getUserLanguageServiceMock('xx_XX'),
            SessionService::SERVICE_ID => $this->getSessionServiceMock($session),
            EventManager::SERVICE_ID => new EventManager(),
            LoggerService::SERVICE_ID => $this->prophesize(LoggerInterface::class)->reveal(),
            UriProvider::SERVICE_ID => new Bin2HexUriProvider([Bin2HexUriProvider::OPTION_NAMESPACE => 'http://ontology.mock/bin2hex#']),
            SimpleCache::SERVICE_ID => new NoCache()
        ]);
        $session->setServiceLocator($sl);
        $onto->setServiceLocator($sl);
        $pm->setServiceLocator($sl);

        // setup schema
        $schemas = $pm->getSqlSchemas();
        if ($onto instanceof SchemaProviderInterface) {
            $onto->provideSchema($schemas);
        }
        $pm->applySchemas($schemas);

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
        $pm = new PersistenceManager([
            PersistenceManager::OPTION_PERSISTENCES => [
                $sqlId => [
                    'driver' => 'dbal',
                    'connection' => [
                        'url' => 'sqlite:///:memory:'
                    ]
                ]
            ]
        ]);
        return $pm;
    }

    /**
     * @param common_session_Session $session
     * @return SessionService
     */
    protected function getSessionServiceMock(common_session_Session $session)
    {
        $prophet = $this->prophesize(SessionService::class);
        $prophet->getCurrentUser()->willReturn($session->getUser());
        $prophet->getCurrentSession()->willReturn($session);
        return $prophet->reveal();
    }

    /**
     * @param string $lang
     * @return UserLanguageServiceInterface
     */
    protected function getUserLanguageServiceMock($lang = 'en_US')
    {
        $prophet = $this->prophesize(UserLanguageServiceInterface::class);
        $prophet->getDefaultLanguage()->willReturn($lang);
        $prophet->getInterfaceLanguage(Argument::any())->willReturn($lang);
        return $prophet->reveal();
    }
}

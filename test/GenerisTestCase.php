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

use oat\generis\model\kernel\persistence\smoothsql\install\SmoothRdsModel;
use oat\oatbox\user\UserLanguageServiceInterface;
use oat\oatbox\session\SessionService;
use Prophecy\Argument;
use oat\oatbox\event\EventManager;
use Psr\Log\LoggerInterface;
use oat\oatbox\log\LoggerService;
use oat\generis\persistence\PersistenceManager;
use oat\generis\model\kernel\uri\UriProvider;
use oat\generis\model\kernel\uri\Bin2HexUriProvider;
use oat\generis\model\data\Ontology;
use oat\oatbox\filesystem\FileSystemService;

class GenerisTestCase extends TestCase
{

    /**
     * @return Ontology
     */
    protected function getOntologyMock()
    {
        $pm = $this->getSqlMock('mockSql');
        $rds = $pm->getPersistenceById('mockSql');
        $schema = $rds->getSchemaManager()->createSchema();
        $schema = SmoothRdsModel::addSmoothTables($schema);
        $queries = $rds->getPlatform()->schemaToSql($schema);
        foreach ($queries as $query) {
            $rds->query($query);
        }
        
        $session = new \common_session_AnonymousSession();
        $model = new \core_kernel_persistence_smoothsql_SmoothModel([
            \core_kernel_persistence_smoothsql_SmoothModel::OPTION_PERSISTENCE => 'mockSql',
            \core_kernel_persistence_smoothsql_SmoothModel::OPTION_READABLE_MODELS => [123],
            \core_kernel_persistence_smoothsql_SmoothModel::OPTION_WRITEABLE_MODELS => [123],
            \core_kernel_persistence_smoothsql_SmoothModel::OPTION_NEW_TRIPLE_MODEL => 123,
            'cache' => 'smoothcache'
        ]);
        $sl = $this->getServiceLocatorMock([
            Ontology::SERVICE_ID => $model,
            PersistenceManager::SERVICE_ID => $pm,
            UserLanguageServiceInterface::SERVICE_ID => $this->getUserLanguageServiceMock('xx_XX'),
            SessionService::SERVICE_ID => $this->getSessionServiceMock($session),
            EventManager::SERVICE_ID => new EventManager(),
            LoggerService::SERVICE_ID => $this->prophesize(LoggerInterface::class)->reveal(),
            UriProvider::SERVICE_ID => new Bin2HexUriProvider([Bin2HexUriProvider::OPTION_NAMESPACE => 'http://ontology.mock/bin2hex#']),
            'smoothcache' => new \common_cache_NoCache()
        ]);
        $session->setServiceLocator($sl);
        $model->setServiceLocator($sl);

        return $model;
    }

    /**
     * @return FileSystemService
     */
    protected function getFileSystemMock($dirs = [])
    {
        $adapterparam = [
            'testfs' => class_exists('League\Flysystem\Memory\MemoryAdapter')
                ? [
                    'class' => \League\Flysystem\Memory\MemoryAdapter::class
                ]
                : [
                    'class' => FileSystemService::FLYSYSTEM_LOCAL_ADAPTER,
                    'options' => ['root' => \tao_helpers_File::createTempDir()]
                ]
        ];
        $dirparam = [];
        foreach ($dirs as $dir) {
            $dirparam[$dir] = 'testfs';
        }
        return new FileSystemService([
            FileSystemService::OPTION_ADAPTERS => $adapterparam,
            FileSystemService::OPTION_DIRECTORIES => $dirparam
        ]);
    }

    protected function getSessionServiceMock($session)
    {
        $prophet = $this->prophesize(SessionService::class);
        $prophet->getCurrentUser()->willReturn($session->getUser());
        $prophet->getCurrentSession()->willReturn($session);
        return $prophet->reveal();
    }
    
    protected function getUserLanguageServiceMock($lang = 'en_US')
    {
        $prophet = $this->prophesize(UserLanguageServiceInterface::class);
        $prophet->getDefaultLanguage()->willReturn($lang);
        $prophet->getInterfaceLanguage(Argument::any())->willReturn($lang);
        return $prophet->reveal();
    }
}

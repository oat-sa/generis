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

use common_persistence_Manager;
use oat\generis\model\kernel\persistence\smoothsql\install\SmoothRdsModel;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\user\UserLanguageServiceInterface;
use oat\oatbox\session\SessionService;
use Prophecy\Argument;
use oat\oatbox\event\EventManager;
use Psr\Log\LoggerInterface;
use oat\oatbox\log\LoggerService;

class GenerisTestCase extends TestCase
{

    protected function getOntologyMock($key = 'mockSql')
    {
        $pm = $this->getSqlMock($key);
        $rds = $pm->getPersistenceById($key);
        $schema = $rds->getSchemaManager()->createSchema();
        $schema = SmoothRdsModel::addSmoothTables($schema);
        $queries = $rds->getPlatform()->schemaToSql($schema);
        foreach ($queries as $query){
            $rds->query($query);
        }
        
        $session = new \common_session_AnonymousSession();
        $sl = $this->getServiceLocatorMock([
            common_persistence_Manager::SERVICE_ID => $pm,
            UserLanguageServiceInterface::SERVICE_ID => $this->getUserLanguageServiceMock('xx_XX'),
            SessionService::SERVICE_ID => $this->getSessionServiceMock($session),
            EventManager::SERVICE_ID => new EventManager(),
            LoggerService::SERVICE_ID => $this->getLoggerServiceMock(),
            'generis/smoothcache' => new \common_cache_NoCache()
        ]);
        $session->setServiceLocator($sl);
        $model = new \core_kernel_persistence_smoothsql_SmoothModel([
            \core_kernel_persistence_smoothsql_SmoothModel::OPTION_PERSISTENCE => $key,
            \core_kernel_persistence_smoothsql_SmoothModel::OPTION_READABLE_MODELS=> [123],
            \core_kernel_persistence_smoothsql_SmoothModel::OPTION_WRITEABLE_MODELS=> [123],
            \core_kernel_persistence_smoothsql_SmoothModel::OPTION_NEW_TRIPLE_MODEL=> 123,
            'cache' => 'generis/smoothcache'
        ]);
        $model->setServiceLocator($sl);

        return $model;
    }

    protected function getSessionServiceMock($session)
    {
        $prophet = $this->prophesize(SessionService::class);
        $prophet->getCurrentUser()->willReturn($session->getUser());
        $prophet->getCurrentSession()->willReturn($session);
        $prophet->setServiceLocator(Argument::any())->willReturn(true);
        return $prophet->reveal();
    }

    protected function getLoggerServiceMock()
    {
        $prophet = $this->prophesize(LoggerInterface::class);
        $prophet->willExtend(ConfigurableService::class);
        $prophet->setServiceLocator(Argument::any())->willReturn(true);
        return $prophet->reveal();
    }

    protected function getUserLanguageServiceMock($lang = 'en_US')
    {
        $prophet = $this->prophesize(UserLanguageServiceInterface::class);
        $prophet->willExtend(ConfigurableService::class);
        $prophet->getDefaultLanguage()->willReturn($lang);
        $prophet->getInterfaceLanguage(Argument::any())->willReturn($lang);
        $prophet->setServiceLocator(Argument::any())->willReturn(true);
        return $prophet->reveal();
    }
}

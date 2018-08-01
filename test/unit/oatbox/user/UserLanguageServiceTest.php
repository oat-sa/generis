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

namespace oat\generis\test\unit\oatbox\user;

use oat\oatbox\user\UserLanguageService;
use oat\oatbox\user\User;
use oat\generis\model\GenerisRdf;
use oat\generis\test\TestCase;

/**
 * class UserLanguageServiceTest
 * @package oat\oatbox\user
 */
class UserLanguageServiceTest extends TestCase
{

    public function setUp()
    {
        if (!defined('DEFAULT_LANG')) {
            define('DEFAULT_LANG', 'en-US');
        }
    }

    public function testGetInterfaceLanguage()
    {
        $service = $this->getService();
        $user = $this->getUser();
        $this->assertEquals('en-US', $service->getInterfaceLanguage($user));
        $user = $this->getUser('fr-FR');
        $this->assertEquals('fr-FR', $service->getInterfaceLanguage($user));
    }

    public function testGetDataLanguage()
    {
        $service = $this->getService([
            UserLanguageService::OPTION_LOCK_DATA_LANGUAGE => false
        ]);
        $user = $this->getUser();
        $this->assertEquals('en-US', $service->getDataLanguage($user));
        $user = $this->getUser('fr-FR', 'fr-FR');
        $this->assertEquals('fr-FR', $service->getDataLanguage($user));

        $service = $this->getService([
            UserLanguageService::OPTION_LOCK_DATA_LANGUAGE => true
        ]);
        $user = $this->getUser();
        $this->assertEquals('en-US', $service->getDataLanguage($user));
        $user = $this->getUser('fr-FR', 'fr-FR');
        $this->assertEquals('en-US', $service->getDataLanguage($user));
    }

    public function testIsDataLanguageEnabled()
    {
        $service = $this->getService();
        $this->assertEquals(true, $service->isDataLanguageEnabled());

        $service = $this->getService([
            UserLanguageService::OPTION_LOCK_DATA_LANGUAGE => false
        ]);
        $this->assertEquals(true, $service->isDataLanguageEnabled());

        $service = $this->getService([
            UserLanguageService::OPTION_LOCK_DATA_LANGUAGE => true
        ]);
        $this->assertEquals(false, $service->isDataLanguageEnabled());
    }

    /**
     * @param string $uiLg
     * @param string $dataLg
     * @return User
     */
    private function getUser($uiLg = null, $dataLg = null)
    {
        $user= $this->prophesize(User::class);
        $user->getPropertyValues(GenerisRdf::PROPERTY_USER_DEFLG)
            ->willReturn($dataLg === null ? [] : [$dataLg]);
        $user->getPropertyValues(GenerisRdf::PROPERTY_USER_UILG)
            ->willReturn($uiLg === null ? [] : [$uiLg]);
        return $user->reveal();
    }

    /**
     * @param array $options
     * @return UserLanguageService
     */
    private function getService($options = [])
    {
        $service = new UserLanguageService($options);
        return $service;
    }
}

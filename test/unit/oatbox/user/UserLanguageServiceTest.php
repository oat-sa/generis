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
 * Copyright (c) 2018-2021 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
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
    public function setUp(): void
    {
        if (!defined('DEFAULT_LANG')) {
            define('DEFAULT_LANG', 'en-US');
        }
    }

    /**
     * @dataProvider getDefaultLanguageDataProvider
     */
    public function testGetDefaultLanguage(string $expected, array $serviceParams): void
    {
        $service = new UserLanguageService($serviceParams);

        $this->assertEquals($expected, $service->getDefaultLanguage());
    }

    public function getDefaultLanguageDataProvider(): array
    {
        return [
            'With OPTION_DEFAULT_LANGUAGE set' => [
                'expected' => 'nb-NO',
                'serviceParams' => [
                    UserLanguageService::OPTION_DEFAULT_LANGUAGE => 'nb-NO'
                ],
            ],
            'With OPTION_DEFAULT_LANGUAGE not set' => [
                'expected' => DEFAULT_LANG,
                'serviceParams' => [],
            ],
        ];
    }

    public function testGetInterfaceLanguage()
    {
        $service = new UserLanguageService();
        $user = $this->getUser();
        $this->assertEquals('en-US', $service->getInterfaceLanguage($user));
        $user = $this->getUser('fr-FR');
        $this->assertEquals('fr-FR', $service->getInterfaceLanguage($user));
    }

    public function testGetInterfaceLanguageWithCustomInterfaceLanguage()
    {
        $service = new UserLanguageService();

        $user = $this->getUser();
        $this->assertEquals('en-US', $service->getInterfaceLanguage($user));
        $user = $this->getUser('fr-FR');
        $this->assertEquals('fr-FR', $service->getInterfaceLanguage($user));
    }

    public function testGetDataLanguageWithOptionDisabled()
    {
        $service = new UserLanguageService([
            UserLanguageService::OPTION_LOCK_DATA_LANGUAGE => false
        ]);

        $user = $this->getUser();
        $this->assertEquals('en-US', $service->getDataLanguage($user));
        $user = $this->getUser('fr-FR', 'fr-FR');
        $this->assertEquals('fr-FR', $service->getDataLanguage($user));
    }

    public function testGetDataLanguageWithOptionEnabled()
    {
        $service = new UserLanguageService([
            UserLanguageService::OPTION_LOCK_DATA_LANGUAGE => true
        ]);

        $user = $this->getUser();
        $this->assertEquals('en-US', $service->getDataLanguage($user));
        $user = $this->getUser('fr-FR', 'fr-FR');
        $this->assertEquals('en-US', $service->getDataLanguage($user));
    }

    public function testIsDataLanguageEnabled()
    {
        $service = new UserLanguageService();
        $this->assertEquals(true, $service->isDataLanguageEnabled());

        $service = new UserLanguageService([
            UserLanguageService::OPTION_LOCK_DATA_LANGUAGE => false
        ]);
        $this->assertEquals(true, $service->isDataLanguageEnabled());

        $service = new UserLanguageService([
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
        $user = $this->prophesize(User::class);
        $user->getPropertyValues(GenerisRdf::PROPERTY_USER_DEFLG)
            ->willReturn($dataLg === null ? [] : [$dataLg]);
        $user->getPropertyValues(GenerisRdf::PROPERTY_USER_UILG)
            ->willReturn($uiLg === null ? [] : [$uiLg]);

        return $user->reveal();
    }
}

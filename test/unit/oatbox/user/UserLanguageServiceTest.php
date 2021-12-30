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

    public function testGetDefaultLanguage(): void
    {
        $service = $this->getService();

        $this->assertEquals(DEFAULT_LANG, $service->getDefaultLanguage());
    }

    /**
     * @dataProvider getInterfaceLanguageDataProvider
     */
    public function testGetInterfaceLanguage(string $expected, User $user, array $serviceParams): void
    {
        $service = $this->getService($serviceParams);

        $this->assertEquals($expected, $service->getInterfaceLanguage($user));
    }

    public function getInterfaceLanguageDataProvider(): array
    {
        return [
            'OPTION_INTERFACE_LANGUAGE=nb-NO, User UI Language not set' => [
                'expected' => 'nb-NO',
                'user' => $this->getUser(),
                'serviceParams' => [
                    UserLanguageService::OPTION_INTERFACE_LANGUAGE => 'nb-NO'
                ],
            ],
            'OPTION_INTERFACE_LANGUAGE=en-US, User UI Language set to fr-FR' => [
                'expected' => 'fr-FR',
                'user' => $this->getUser('fr-FR'),
                'serviceParams' => [
                    UserLanguageService::OPTION_INTERFACE_LANGUAGE => 'en-US'
                ],
            ],
            'OPTION_DEFAULT_LANGUAGE not set, User UI Language not set' => [
                'expected' => 'en-US', // should match DEFAULT_LANG from setUp
                'user' => $this->getUser(),
                'serviceParams' => [],
            ],
            'OPTION_DEFAULT_LANGUAGE not set, User UI Language set to fr-FR' => [
                'expected' => 'fr-FR',
                'user' => $this->getUser('fr-FR'),
                'serviceParams' => [],
            ],
        ];
    }

    public function testGetDataLanguageWithOptionDisabled()
    {
        $service = $this->getService([
            UserLanguageService::OPTION_LOCK_DATA_LANGUAGE => false
        ]);

        $user = $this->getUser();
        $this->assertEquals('en-US', $service->getDataLanguage($user));
        $user = $this->getUser('fr-FR', 'fr-FR');
        $this->assertEquals('fr-FR', $service->getDataLanguage($user));
    }

    public function testGetDataLanguageWithOptionEnabled()
    {
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
        $user = $this->prophesize(User::class);
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
        return new UserLanguageService($options);
    }
}

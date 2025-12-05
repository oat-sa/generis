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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\generis\test\unit\common\oatbox\user;

use oat\generis\model\user\UserRdf;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use oat\oatbox\user\User;
use oat\oatbox\user\UserLanguageService;

class UserLanguageServiceTest extends TestCase
{
    private UserLanguageService $subject;

    public function setUp(): void
    {
        $this->subject = new UserLanguageService(
            [
                UserLanguageService::OPTION_AUTHORING_LANGUAGE => 'fr-FR',
            ]
        );
    }

    public function testGetAuthoringLanguage(): void
    {
        $this->assertSame('fr-FR', $this->subject->getAuthoringLanguage());
    }

    public function testGetDefaultLanguageForAuthoringLanguage(): void
    {
        $this->subject->setOptions([]);

        $this->assertSame($this->subject->getDefaultLanguage(), $this->subject->getAuthoringLanguage());
    }

    public function testGetInterfaceLanguageReturnsDefaultLanguageWhenUserDoesNotHaveItSet(): void
    {
        $user = $this->createUser();

        $this->assertSame($this->subject->getDefaultLanguage(), $this->subject->getInterfaceLanguage($user));
    }

    public function testGetInterfaceLanguageReturnsLanguageFromUser(): void
    {
        $user = $this->createUser('en-US');

        $this->assertSame('en-US', $this->subject->getInterfaceLanguage($user));
    }

    public function testGetInterfaceLanguageReturnsCustomInterfaceLanguage(): void
    {
        $this->subject->setCustomInterfaceLanguage('es-ES');

        $user = $this->createUser();

        $this->assertSame('es-ES', $this->subject->getInterfaceLanguage($user));
    }

    private function createUser(?string $withLanguage = null): User|MockObject
    {
        $user = $this->createMock(User::class);

        if ($withLanguage !== null) {
            $user
                ->method('getPropertyValues')
                ->with(UserRdf::PROPERTY_UILG)
                ->willReturn([$withLanguage]);
        }

        return $user;
    }
}

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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\generis\test;

use oat\session\SessionSubstitutionService;
use oat\session\PretenderSession;
use common_session_SessionManager;

/**
 * Interface SessionSubstitutionService
 *
 * @author Aleh Hutnikau <hutnikau@1pt.com>
 * @package generis\test
 */
class SessionSubstitutionServiceTest extends GenerisPhpUnitTestRunner
{

    private $testUserUri = 'http://sample/first.rdf#tessionSubstitutionServiceTestUser';

    public static function getSubstituteSessionService()
    {
        return [[new SessionSubstitutionService()]];
    }

    /**
     * @param SessionSubstitutionService $service
     * @dataProvider getSubstituteSessionService
     * @runInSeparateProcess
     */
    public function testSubstituteSession($service)
    {
        $initialSession = common_session_SessionManager::getSession();
        $newUser = new \core_kernel_users_GenerisUser(new \core_kernel_classes_Resource($this->testUserUri));

        $service->substituteSession($newUser);
        $newSession = common_session_SessionManager::getSession();

        $this->assertNotEquals($initialSession->getUserUri(), $newSession->getUserUri());
        $this->assertEquals($newUser->getIdentifier(), $newSession->getUserUri());
        $this->assertTrue($newSession instanceof PretenderSession);
    }

    /**
     * @param SessionSubstitutionService $service
     * @dataProvider getSubstituteSessionService
     * @runInSeparateProcess
     */
    public function testIsSubstituted($service)
    {
        $this->assertFalse($service->isSubstituted());
        $newUser = new \core_kernel_users_GenerisUser(new \core_kernel_classes_Resource($this->testUserUri));

        $service->substituteSession($newUser);
        $this->assertTrue($service->isSubstituted());
    }

    /**
     * @param SessionSubstitutionService $service
     * @dataProvider getSubstituteSessionService
     * @runInSeparateProcess
     */
    public function testRevert($service)
    {
        $initialSession = common_session_SessionManager::getSession();
        $this->assertFalse($service->isSubstituted());
        $newUser = new \core_kernel_users_GenerisUser(new \core_kernel_classes_Resource($this->testUserUri));

        $service->substituteSession($newUser);
        $this->assertTrue($service->isSubstituted());
        $this->assertEquals(common_session_SessionManager::getSession()->getUserUri(), $this->testUserUri);

        $service->revert();

        $this->assertFalse($service->isSubstituted());
        $this->assertNotEquals(common_session_SessionManager::getSession()->getUserUri(), $this->testUserUri);
        $this->assertEquals($initialSession->getUserUri(), common_session_SessionManager::getSession()->getUserUri());
    }
}
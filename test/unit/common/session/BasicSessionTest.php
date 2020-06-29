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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\generis\test\unit\common\persistence;

use oat\generis\test\TestCase;
use oat\oatbox\user\User;
use oat\oatbox\session\SessionContext;

class BasicSessionTest extends TestCase
{
    public function testGetContext() : void
    {
        $user = $this->prophesize(User::class)->reveal();
        $context1 = $this->prophesize(SessionContext::class)->reveal();
        $context2 = $this->prophesize(SessionContext::class)->reveal();

        $session = new \common_session_BasicSession($user, [$context1]);
        $this->assertEquals([$context1], $session->getContexts());

        $session = new \common_session_BasicSession($user, [$context1, $context2]);
        $this->assertEquals([$context1, $context2], $session->getContexts());
    }

}

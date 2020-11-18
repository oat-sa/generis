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
 *
 */

namespace oat\generis\test\unit\core\data\permission;

use oat\generis\model\data\permission\PermissionHelper;
use oat\generis\model\data\permission\PermissionInterface;
use oat\oatbox\session\SessionService;
use oat\oatbox\user\User;
use oat\generis\test\TestCase;
use Prophecy\Argument;

class PermissionTest extends TestCase
{
    const RIGHT = 'testRight';
    /**
     * @dataProvider getSamples
     */
    public function testFilter($supported, $ids, $permissions, $expected)
    {
        $userMock = $this->prophesize(User::class);

        $sessionMock = $this->prophesize(SessionService::class);
        $sessionMock->getCurrentUser()->willReturn($userMock->reveal());

        $permissionMock = $this->prophesize(PermissionInterface::class);
        $permissionMock->getPermissions(Argument::any(), $ids)->willReturn($permissions);
        $permissionMock->getSupportedRights()->willReturn($supported);

        $helper = new PermissionHelper();
        $helper->setServiceLocator($this->getServiceLocatorMock([
            SessionService::SERVICE_ID => $sessionMock->reveal(),
            PermissionInterface::SERVICE_ID => $permissionMock->reveal()
        ]));
        $actual = $helper->filterByPermission($ids, self::RIGHT);
        $this->assertEquals($expected, $actual);
    }

    public function getSamples()
    {
        return [
            [[self::RIGHT], ['a', 'b'], ['a' => [self::RIGHT]], ['a']],
            [[self::RIGHT], ['a', 'b'], [], []],
            [[self::RIGHT], [1,2,3,4,5], [1 => [self::RIGHT]], [1]],
            [[self::RIGHT], [1,2,3,4,5], [2 => [self::RIGHT, 'somethingelse']], [1 => 2]],
            [[self::RIGHT], [1,2,3,4,5], [2 => [self::RIGHT], 3 =>['somethingelse']], [1 => 2]],
            [[self::RIGHT], [1,2,3,4,5], [2 => [self::RIGHT], 4 =>[self::RIGHT]], [1 => 2, 3 => 4]],
            [[self::RIGHT, 'weird'], [1,2,3,4,5], [1 => [self::RIGHT]], [1]],
            [[], [1,2,3,4,5], [], [1,2,3,4,5]],
            [['weird'], [1,2,3,4,5], [], [1,2,3,4,5]]
        ];
    }
}

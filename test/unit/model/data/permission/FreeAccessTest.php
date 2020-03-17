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
 * Copyright (c) (original work) 2015 Open Assessment Technologies SA
 *
 */

namespace oat\generis\test\unit\model\data\permission;

use oat\generis\model\data\permission\implementation\FreeAccess;
use oat\oatbox\user\User;
use oat\generis\test\TestCase;

class FreeAccessTest extends TestCase
{
    /**
     * @var User
     */
    private $user;

    public function setUp(): void
    {
        $user = $this->prophesize('oat\oatbox\user\User');
        $user->getIdentifier()->willReturn('tastIdentifier\\_of_//User');

        $this->user = $user->reveal();
    }

    public function testConstruct()
    {
        $model = new FreeAccess();
        $this->assertInstanceOf('oat\generis\model\data\permission\PermissionInterface', $model);
    }

    public function testGetPermissions()
    {
        $model = new FreeAccess();
        $this->assertEquals(['res1' => [FreeAccess::RIGHT_UNSUPPORTED]], $model->getPermissions($this->user, ['res1']));
        $this->assertEquals(['res1' => [FreeAccess::RIGHT_UNSUPPORTED], 'res2' => [FreeAccess::RIGHT_UNSUPPORTED]], $model->getPermissions($this->user, ['res1', 'res2']));
    }

    public function testGetSupportedRights()
    {
        $model = new FreeAccess();
        $this->assertEquals([], $model->getSupportedRights());
    }

    public function testPhpSerialize()
    {
        $phpCode = \common_Utils::toPHPVariableString(new FreeAccess());
        $restoredModel = eval('return ' . $phpCode . ';');

        $this->assertInstanceOf('oat\generis\model\data\permission\PermissionInterface', $restoredModel);
        $this->assertEquals([], $restoredModel->getSupportedRights());
        $this->assertEquals(['res1' => [FreeAccess::RIGHT_UNSUPPORTED]], $restoredModel->getPermissions($this->user, ['res1']));
    }
}

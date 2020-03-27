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

use oat\generis\model\data\permission\implementation\Intersection;
use oat\oatbox\user\User;
use oat\generis\test\TestCase;

class IntersectionTest extends TestCase
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


    private function createIntersection()
    {
        $permissionModel1 = $this->prophesize('oat\generis\model\data\permission\PermissionInterface');
        $permissionModel1->getSupportedRights()->willReturn(['rightA', 'rightB', 'rightC', 'right']);

        $permissionModel2 = $this->prophesize('oat\generis\model\data\permission\PermissionInterface');
        $permissionModel2->getSupportedRights()->willReturn(['rightB', 'rightC', 'rightD', 'rightAB']);

        $permissionModel3 = $this->prophesize('oat\generis\model\data\permission\PermissionInterface');
        $permissionModel3->getSupportedRights()->willReturn(['rightC', 'rightD', 'rightE', 'rightABC']);

        // res1
        $permissionModel1->getPermissions($this->user, ['res1'])->willReturn(['res1' => ['rightA']]);
        $permissionModel2->getPermissions($this->user, ['res1'])->willReturn(['res1' => ['rightB']]);
        $permissionModel3->getPermissions($this->user, ['res1'])->willReturn(['res1' => ['rightC']]);

        // res2
        $permissionModel1->getPermissions($this->user, ['res1', 'res2'])->willReturn(['res1' => ['rightA', 'rightC'], 'res2' => []]);
        $permissionModel2->getPermissions($this->user, ['res1', 'res2'])->willReturn(['res1' => ['rightC'], 'res2' => []]);
        $permissionModel3->getPermissions($this->user, ['res1', 'res2'])->willReturn(['res1' => ['rightC', 'rightD'], 'res2' => []]);

        return Intersection::spawn([$permissionModel1->reveal(), $permissionModel2->reveal(), $permissionModel3->reveal()]);
    }

    public function testConstruct()
    {
        $this->assertInstanceOf('oat\generis\model\data\permission\PermissionInterface', $this->createIntersection());
    }

    public function testGetPermissions()
    {
        $model = $this->createIntersection();
        $this->assertEquals(['res1' => []], $model->getPermissions($this->user, ['res1']));
        $this->assertEquals(['res1' => ['rightC'], 'res2' => []], $model->getPermissions($this->user, ['res1', 'res2']));
    }

    public function testGetSupportedRights()
    {
        $model = $this->createIntersection();
        $this->assertEquals(['rightC'], $model->getSupportedRights());
    }

    public function testOnResourceCreated()
    {
        $permissionModel = $this->prophesize('oat\generis\model\data\permission\PermissionInterface');
        $permissionModel2 = $this->prophesize('oat\generis\model\data\permission\PermissionInterface');

        $model = Intersection::spawn([$permissionModel->reveal(),$permissionModel2->reveal()]);
        $resourceprophecy = $this->prophesize('core_kernel_classes_Resource');
        $resource = $resourceprophecy->reveal();
        $model->onResourceCreated($resource);
        $permissionModel->onResourceCreated($resource)->shouldHaveBeenCalled();
        $permissionModel2->onResourceCreated($resource)->shouldHaveBeenCalled();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testPhpSerialize()
    {
        // no idea how to test
    }
}

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

use core_kernel_classes_Resource;
use oat\generis\model\data\permission\implementation\Intersection;
use oat\oatbox\user\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use oat\generis\model\data\permission\PermissionInterface;

class IntersectionTest extends TestCase
{
    private User|MockObject $user;

    public function setUp(): void
    {
        $this->user = $this->createMock(User::class);
        $this->user
            ->method('getIdentifier')
            ->willReturn('tastIdentifier\\_of_//User');
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(PermissionInterface::class, $this->createIntersection());
    }

    public function testGetPermissions(): void
    {
        $model = $this->createIntersection();
        $this->assertEquals(['res1' => []], $model->getPermissions($this->user, ['res1']));
        $this->assertEquals(
            ['res1' => ['rightC'], 'res2' => []],
            $model->getPermissions($this->user, ['res1', 'res2'])
        );
    }

    public function testGetSupportedRights(): void
    {
        $model = $this->createIntersection();
        $this->assertEquals(['rightC'], $model->getSupportedRights());
    }

    public function testOnResourceCreated(): void
    {
        $resource = $this->createMock(core_kernel_classes_Resource::class);

        $permissionModel = $this->createMock(PermissionInterface::class);
        $permissionModel
            ->expects($this->once())
            ->method('onResourceCreated')
            ->with($resource);

        $permissionModel2 = $this->createMock(PermissionInterface::class);
        $permissionModel2
            ->expects($this->once())
            ->method('onResourceCreated')
            ->with($resource);

        $model = Intersection::spawn([$permissionModel, $permissionModel2]);
        $model->onResourceCreated($resource);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testPhpSerialize(): void
    {
        // no idea how to test
    }


    private function createIntersection(): Intersection
    {
        $permissionModel1 = $this->createMock(PermissionInterface::class);
        $permissionModel1
            ->method('getSupportedRights')
            ->willReturn(['rightA', 'rightB', 'rightC', 'right']);

        $permissionModel2 = $this->createMock(PermissionInterface::class);
        $permissionModel2
            ->method('getSupportedRights')
            ->willReturn(['rightB', 'rightC', 'rightD', 'rightAB']);

        $permissionModel3 = $this->createMock(PermissionInterface::class);
        $permissionModel3
            ->method('getSupportedRights')
            ->willReturn(['rightC', 'rightD', 'rightE', 'rightABC']);

        $permissionModel1
            ->method('getPermissions')
            ->willReturnMap([
                [$this->user, ['res1'], ['res1' => ['rightA']]],
                [$this->user, ['res1', 'res2'], ['res1' => ['rightA', 'rightC'], 'res2' => []]],
            ]);

        $permissionModel2
            ->method('getPermissions')
            ->willReturnMap([
                [$this->user, ['res1'], ['res1' => ['rightB']]],
                [$this->user, ['res1', 'res2'], ['res1' => ['rightC'], 'res2' => []]],
            ]);

        $permissionModel3
            ->method('getPermissions')
            ->willReturnMap([
                [$this->user, ['res1'], ['res1' => ['rightC']]],
                [$this->user, ['res1', 'res2'], ['res1' => ['rightC', 'rightD'], 'res2' => []]],
            ]);

        return Intersection::spawn([
            $permissionModel1,
            $permissionModel2,
            $permissionModel3
        ]);
    }
}

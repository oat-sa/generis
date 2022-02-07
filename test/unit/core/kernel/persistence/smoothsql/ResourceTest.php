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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\generis\test\unit\core\kernel\persistence\smoothsql;

use core_kernel_classes_Resource;
use core_kernel_persistence_smoothsql_Resource;
use core_kernel_persistence_smoothsql_SmoothModel;
use oat\generis\test\GenerisTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ResourceTest extends GenerisTestCase
{
    /** @var core_kernel_persistence_smoothsql_SmoothModel|MockObject */
    private $model;

    /** @var core_kernel_persistence_smoothsql_Resource */
    private $sut;

    public function setUp(): void
    {
        $this->model = $this->createMock(core_kernel_persistence_smoothsql_SmoothModel::class);
        $this->sut = new core_kernel_persistence_smoothsql_Resource($this->model);
    }

    public function testIsWritable(): void
    {
        $this->model->method('isWritable')
            ->willReturn(true);

        $this->assertTrue($this->sut->isWritable($this->createMock(core_kernel_classes_Resource::class)));
    }
}

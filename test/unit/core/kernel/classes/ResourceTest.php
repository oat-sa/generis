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

namespace oat\generis\test\unit\core\kernel\classes;

use core_kernel_classes_Resource;
use core_kernel_persistence_ResourceInterface;
use core_kernel_persistence_smoothsql_Resource;
use oat\generis\model\data\Ontology;
use oat\generis\model\data\RdfsInterface;
use oat\generis\test\GenerisTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ResourceTest extends GenerisTestCase
{
    /** @var core_kernel_classes_Resource */
    private $subject;

    /** @var Ontology|MockObject */
    private $model;

    /** @var RdfsInterface|MockObject */
    private $rdfs;

    /** @var core_kernel_persistence_smoothsql_Resource|MockObject */
    private $smoothSqlResource;

    public function setUp(): void
    {
        $this->model = $this->createMock(Ontology::class);
        $this->rdfs = $this->createMock(RdfsInterface::class);
        $this->smoothSqlResource = $this->createMock(core_kernel_persistence_smoothsql_Resource::class);

        $this->model->method('getRdfsInterface')
            ->willReturn($this->rdfs);

        $this->subject = new core_kernel_classes_Resource('uri');
        $this->subject->setModel($this->model);
    }

    public function testIsWritableByDefault(): void
    {
        $this->rdfs->method('getResourceImplementation')
            ->willReturn($this->createMock(core_kernel_persistence_ResourceInterface::class));

        $this->smoothSqlResource
            ->method('isWritable')
            ->willReturn(true);

        $this->assertTrue($this->subject->isWritable());
    }

    public function testIsWritable(): void
    {
        $this->rdfs->method('getResourceImplementation')
            ->willReturn($this->smoothSqlResource);

        $this->smoothSqlResource
            ->method('isWritable')
            ->willReturn(true);

        $this->assertTrue($this->subject->isWritable());
    }

    public function testIsNotWritable(): void
    {
        $this->rdfs->method('getResourceImplementation')
            ->willReturn($this->smoothSqlResource);

        $this->smoothSqlResource
            ->method('isWritable')
            ->willReturn(false);

        $this->assertFalse($this->subject->isWritable());
    }
}

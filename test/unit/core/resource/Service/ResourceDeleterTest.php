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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA.
 */

declare(strict_types=1);

namespace oat\generis\test\unit\model\resource\Service;

use core_kernel_classes_Class;
use oat\generis\test\TestCase;
use core_kernel_classes_Resource;
use core_kernel_classes_Property;
use PHPUnit\Framework\MockObject\MockObject;
use oat\generis\model\Context\ContextInterface;
use oat\generis\model\resource\Service\ResourceDeleter;
use oat\generis\model\resource\Context\ResourceRepositoryContext;
use oat\generis\model\resource\exception\ResourceDeletionException;
use oat\generis\model\resource\Contract\ResourceRepositoryInterface;

class ResourceDeleterTest extends TestCase
{
    private const PARAM_RESOURCE = ResourceRepositoryContext::PARAM_RESOURCE;

    /** @var ResourceDeleter */
    private $sut;

    /** @var ResourceRepositoryInterface|MockObject */
    private $resourceRepository;

    /** @var core_kernel_classes_Resource|MockObject */
    private $resource;

    protected function setUp(): void
    {
        $this->resourceRepository = $this->createMock(ResourceRepositoryInterface::class);
        $this->sut = new ResourceDeleter($this->resourceRepository);

        $this->resource = $this->createMock(core_kernel_classes_Resource::class);
        $this->resource
            ->expects($this->once())
            ->method('getProperty')
            ->willReturn($this->createMock(core_kernel_classes_Property::class));
    }

    public function testDeleteSuccessWithParentClass(): void
    {
        $this->resourceRepository
            ->expects($this->once())
            ->method('delete');

        $this->resource
            ->expects($this->once())
            ->method('getOnePropertyValue')
            ->willReturn('parentClassUri');
        $this->resource
            ->expects($this->once())
            ->method('getClass')
            ->willReturn($this->createMock(core_kernel_classes_Class::class));

        $this->resource
            ->expects($this->never())
            ->method('getLabel');
        $this->resource
            ->expects($this->never())
            ->method('getUri');

        $this->sut->delete($this->resource);
    }

    public function testDeleteSuccessWithoutParentClass(): void
    {
        $this->resourceRepository
            ->expects($this->once())
            ->method('delete');

        $this->resource
            ->expects($this->once())
            ->method('getOnePropertyValue')
            ->willReturn(null);
        $this->resource
            ->expects($this->never())
            ->method('getClass');

        $this->resource
            ->expects($this->never())
            ->method('getLabel');
        $this->resource
            ->expects($this->never())
            ->method('getUri');

        $this->sut->delete($this->resource);
    }

    public function testDeleteWithRepositoryError(): void
    {
        $resourceRepositoryContext = $this->createMock(ContextInterface::class);
        $resourceRepositoryContext
            ->expects($this->once())
            ->method('getParameter')
            ->with(self::PARAM_RESOURCE)
            ->willReturn(null);

        $this->resourceRepository
            ->expects($this->once())
            ->with($resourceRepositoryContext)
            ->method('delete');

        $this->resource
            ->expects($this->once())
            ->method('getOnePropertyValue')
            ->willReturn('parentClassUri');
        $this->resource
            ->expects($this->once())
            ->method('getClass')
            ->willReturn($this->createMock(core_kernel_classes_Class::class));

        $this->resource
            ->expects($this->once())
            ->method('getLabel')
            ->willReturn('resourceLabel');
        $this->resource
            ->expects($this->once())
            ->method('getUri')
            ->willReturn('resourceUri');

        $this->expectException(ResourceDeletionException::class);
        $this->expectExceptionMessage(
            'Unable to delete resource "resourceLabel::resourceUri" (Resource was not provided for deletion.).'
        );

        $this->sut->delete($this->resource);
    }
}

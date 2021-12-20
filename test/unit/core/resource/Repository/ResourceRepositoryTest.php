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

namespace oat\generis\test\unit\model\resource\Repository;

use RuntimeException;
use InvalidArgumentException;
use core_kernel_classes_Class;
use oat\generis\test\TestCase;
use core_kernel_classes_Resource;
use oat\oatbox\event\EventManager;
use oat\generis\model\data\Ontology;
use oat\generis\model\data\RdfsInterface;
use PHPUnit\Framework\MockObject\MockObject;
use core_kernel_persistence_ResourceInterface;
use oat\generis\model\Context\ContextInterface;
use oat\generis\model\resource\Repository\ResourceRepository;
use oat\generis\model\resource\Context\ResourceRepositoryContext;

class ResourceRepositoryTest extends TestCase
{
    private const PARAM_RESOURCE = ResourceRepositoryContext::PARAM_RESOURCE;
    private const PARAM_DELETE_REFERENCE = ResourceRepositoryContext::PARAM_DELETE_REFERENCE;
    private const PARAM_SELECTED_CLASS = ResourceRepositoryContext::PARAM_SELECTED_CLASS;
    private const PARAM_PARENT_CLASS = ResourceRepositoryContext::PARAM_PARENT_CLASS;

    /** @var ResourceRepository */
    private $sut;

    /** @var core_kernel_persistence_ResourceInterface|MockObject */
    private $resourceImplementation;

    /** @var RdfsInterface|MockObject */
    private $rdfsInterface;

    /** @var Ontology|MockObject */
    private $ontology;

    /** @var EventManager|MockObject */
    private $eventManager;

    protected function setUp(): void
    {
        $this->resourceImplementation = $this->createMock(core_kernel_persistence_ResourceInterface::class);

        $this->rdfsInterface = $this->createMock(RdfsInterface::class);
        $this->rdfsInterface
            ->method('getResourceImplementation')
            ->willReturn($this->resourceImplementation);

        $this->ontology = $this->createMock(Ontology::class);
        $this->ontology
            ->method('getRdfsInterface')
            ->willReturn($this->rdfsInterface);

        $this->eventManager = $this->createMock(EventManager::class);

        $this->sut = new ResourceRepository($this->ontology, $this->eventManager);
    }

    public function testDeleteSuccess(): void
    {
        $this->ontology
            ->expects($this->once())
            ->method('getRdfsInterface');
        $this->rdfsInterface
            ->expects($this->once())
            ->method('getResourceImplementation');
        $this->resourceImplementation
            ->expects($this->once())
            ->method('delete')
            ->willReturn(true);
        $this->eventManager
            ->expects($this->once())
            ->method('trigger');

        $context = $this->createContext(4, $this->createResource('resourceUri'));
        $this->sut->delete($context);
    }

    public function testDeleteWithoutResource(): void
    {
        $this->ontology
            ->expects($this->never())
            ->method('getRdfsInterface');
        $this->rdfsInterface
            ->expects($this->never())
            ->method('getResourceImplementation');
        $this->resourceImplementation
            ->expects($this->never())
            ->method('delete');
        $this->eventManager
            ->expects($this->never())
            ->method('trigger');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Resource was not provided for deletion.');

        $context = $this->createContext(1, null);
        $this->sut->delete($context);
    }

    public function testDeleteFailure(): void
    {
        $this->ontology
            ->expects($this->once())
            ->method('getRdfsInterface');
        $this->rdfsInterface
            ->expects($this->once())
            ->method('getResourceImplementation');
        $this->resourceImplementation
            ->expects($this->once())
            ->method('delete')
            ->willReturn(false);
        $this->eventManager
            ->expects($this->never())
            ->method('trigger');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Resource "resourceLabel" ("resourceUri") was not deleted.');

        $context = $this->createContext(2, $this->createResource('resourceUri', 'resourceLabel'));
        $this->sut->delete($context);
    }

    private function createResource(string $uri, string $label = null): core_kernel_classes_Resource
    {
        $class = $this->createMock(core_kernel_classes_Resource::class);
        $class
            ->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);
        $class
            ->expects($label !== null ? $this->once() : $this->never())
            ->method('getLabel')
            ->willReturn($label);

        return $class;
    }

    private function createContext(int $expects, ?core_kernel_classes_Resource $resource): ContextInterface
    {
        $context = $this->createMock(ContextInterface::class);
        $context
            ->expects($this->exactly($expects))
            ->method('getParameter')
            ->willReturnCallback(
                function (string $param) use ($resource) {
                    if ($param === self::PARAM_RESOURCE) {
                        return $resource;
                    }

                    if ($param === self::PARAM_DELETE_REFERENCE) {
                        return false;
                    }

                    if (in_array($param, [self::PARAM_SELECTED_CLASS, self::PARAM_PARENT_CLASS], true)) {
                        return $this->createMock(core_kernel_classes_Class::class);
                    }

                    return null;
                }
            );

        return $context;
    }
}

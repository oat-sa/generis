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

use core_kernel_classes_Class;
use core_kernel_persistence_ClassInterface;
use InvalidArgumentException;
use oat\generis\model\Context\ContextInterface;
use oat\generis\model\data\Ontology;
use oat\generis\model\data\RdfsInterface;
use oat\generis\model\resource\Context\ResourceRepositoryContext;
use oat\generis\model\resource\Repository\ClassRepository;
use oat\generis\test\TestCase;
use oat\oatbox\event\EventManager;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;

class ClassRepositoryTest extends TestCase
{
    private const PARAM_CLASS = ResourceRepositoryContext::PARAM_CLASS;
    private const PARAM_DELETE_REFERENCE = ResourceRepositoryContext::PARAM_DELETE_REFERENCE;
    private const PARAM_SELECTED_CLASS = ResourceRepositoryContext::PARAM_SELECTED_CLASS;
    private const PARAM_PARENT_CLASS = ResourceRepositoryContext::PARAM_PARENT_CLASS;

    /** @var ClassRepository */
    private $sut;

    /** @var core_kernel_persistence_ClassInterface|MockObject */
    private $classImplementation;

    /** @var RdfsInterface|MockObject */
    private $rdfsInterface;

    /** @var Ontology|MockObject */
    private $ontology;

    /** @var EventManager|MockObject */
    private $eventManager;

    protected function setUp(): void
    {
        $this->classImplementation = $this->createMock(core_kernel_persistence_ClassInterface::class);

        $this->rdfsInterface = $this->createMock(RdfsInterface::class);
        $this->rdfsInterface
            ->method('getClassImplementation')
            ->willReturn($this->classImplementation);

        $this->ontology = $this->createMock(Ontology::class);
        $this->ontology
            ->method('getRdfsInterface')
            ->willReturn($this->rdfsInterface);

        $this->eventManager = $this->createMock(EventManager::class);

        $this->sut = new ClassRepository($this->ontology, $this->eventManager);
    }

    public function testDeleteSuccess(): void
    {
        $this->ontology
            ->expects($this->once())
            ->method('getRdfsInterface');
        $this->rdfsInterface
            ->expects($this->once())
            ->method('getClassImplementation');
        $this->classImplementation
            ->expects($this->once())
            ->method('delete')
            ->willReturn(true);
        $this->eventManager
            ->expects($this->once())
            ->method('trigger');

        $context = $this->createContext(4, $this->createClass());
        $this->sut->delete($context);
    }

    public function testDeleteWithoutResource(): void
    {
        $this->ontology
            ->expects($this->never())
            ->method('getRdfsInterface');
        $this->rdfsInterface
            ->expects($this->never())
            ->method('getClassImplementation');
        $this->classImplementation
            ->expects($this->never())
            ->method('delete');
        $this->eventManager
            ->expects($this->never())
            ->method('trigger');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class was not provided for deletion.');

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
            ->method('getClassImplementation');
        $this->classImplementation
            ->expects($this->once())
            ->method('delete')
            ->willReturn(false);
        $this->eventManager
            ->expects($this->never())
            ->method('trigger');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Class "classLabel" ("classUri") was not deleted.');

        $context = $this->createContext(3, $this->createClass('classUri', 'classLabel'));
        $this->sut->delete($context);
    }

    private function createClass(string $uri = null, string $label = null): core_kernel_classes_Class
    {
        $class = $this->createMock(core_kernel_classes_Class::class);
        $class
            ->expects($uri !== null ? $this->once() : $this->never())
            ->method('getUri')
            ->willReturn((string)$uri);
        $class
            ->expects($label !== null ? $this->once() : $this->never())
            ->method('getLabel')
            ->willReturn($label);
        $class
            ->expects($this->once())
            ->method('getParentClasses')
            ->willReturn([]);

        return $class;
    }

    private function createContext(int $expects, ?core_kernel_classes_Class $class): ContextInterface
    {
        $context = $this->createMock(ContextInterface::class);
        $context
            ->expects($this->exactly($expects))
            ->method('getParameter')
            ->willReturnCallback(
                function (string $param) use ($class) {
                    if ($param === self::PARAM_CLASS) {
                        return $class;
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

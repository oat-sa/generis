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

namespace oat\generis\test\unit\model\data\event;

use core_kernel_classes_Class;
use oat\generis\test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use oat\generis\model\data\event\ClassDeletedEvent;

class ClassDeletedEventTest extends TestCase
{
    private const CLASS_URI = 'classUri';

    /** @var ClassDeletedEvent */
    private $sut;

    /** @var core_kernel_classes_Class|MockObject */
    private $class;

    protected function setUp(): void
    {
        $this->class = $this->createMock(core_kernel_classes_Class::class);

        $this->sut = new ClassDeletedEvent($this->class);
    }

    public function testGetName(): void
    {
        $this->assertEquals(ClassDeletedEvent::class, $this->sut->getName());
    }

    public function testGetClass(): void
    {
        $this->assertEquals($this->class, $this->sut->getClass());
    }

    public function testJsonSerializeWithoutAnyData(): void
    {
        $this->class
            ->expects($this->once())
            ->method('getUri')
            ->willReturn(self::CLASS_URI);

        $this->assertEquals(['uri' => self::CLASS_URI], $this->sut->jsonSerialize());
    }

    public function testJsonSerializeWithSelectedClass(): void
    {
        $this->class
            ->expects($this->once())
            ->method('getUri')
            ->willReturn(self::CLASS_URI);

        $this->sut->setSelectedClass($this->createClass('selectedClassUri', 'selectedClassLabel'));

        $this->assertEquals(
            [
                'uri' => self::CLASS_URI,
                'selectedClass' => [
                    'uri' => 'selectedClassUri',
                    'label' => 'selectedClassLabel',
                ],
            ],
            $this->sut->jsonSerialize()
        );
    }

    public function testJsonSerializeWithParentClass(): void
    {
        $this->class
            ->expects($this->once())
            ->method('getUri')
            ->willReturn(self::CLASS_URI);

        $this->sut->setParentClass($this->createClass('parentClassUri', 'parentClassLabel'));

        $this->assertEquals(
            [
                'uri' => self::CLASS_URI,
                'parentClass' => [
                    'uri' => 'parentClassUri',
                    'label' => 'parentClassLabel',
                ],
            ],
            $this->sut->jsonSerialize()
        );
    }

    public function testJsonSerializeWithSelectedAndParentClasses(): void
    {
        $this->class
            ->expects($this->once())
            ->method('getUri')
            ->willReturn(self::CLASS_URI);

        $this->sut->setSelectedClass($this->createClass('selectedClassUri', 'selectedClassLabel'));
        $this->sut->setParentClass($this->createClass('parentClassUri', 'parentClassLabel'));

        $this->assertEquals(
            [
                'uri' => self::CLASS_URI,
                'selectedClass' => [
                    'uri' => 'selectedClassUri',
                    'label' => 'selectedClassLabel',
                ],
                'parentClass' => [
                    'uri' => 'parentClassUri',
                    'label' => 'parentClassLabel',
                ],
            ],
            $this->sut->jsonSerialize()
        );
    }

    public function testJsonSerializeWithNullableSelectedAndParentClasses(): void
    {
        $this->class
            ->expects($this->once())
            ->method('getUri')
            ->willReturn(self::CLASS_URI);

        $this->sut->setSelectedClass(null);
        $this->sut->setParentClass(null);

        $this->assertEquals(['uri' => self::CLASS_URI], $this->sut->jsonSerialize());
    }

    private function createClass(string $uri, string $label): core_kernel_classes_Class
    {
        $class = $this->createMock(core_kernel_classes_Class::class);
        $class
            ->expects($this->once())
            ->method('getUri')
            ->willReturn($uri);
        $class
            ->expects($this->once())
            ->method('getLabel')
            ->willReturn($label);

        return $class;
    }
}

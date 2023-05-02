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
use oat\generis\model\data\event\ResourceDeleted;
use oat\generis\test\TestCase;

class ResourceDeletedTest extends TestCase
{
    private const RESOURCE_URI = 'resourceUri';

    /** @var ResourceDeleted */
    private $sut;

    protected function setUp(): void
    {
        $this->sut = new ResourceDeleted(self::RESOURCE_URI);
    }

    public function testGetName(): void
    {
        $this->assertEquals(ResourceDeleted::class, $this->sut->getName());
    }

    public function testGetId(): void
    {
        $this->assertEquals(self::RESOURCE_URI, $this->sut->getId());
    }

    public function testJsonSerializeWithoutAnyData(): void
    {
        $this->assertEquals(['uri' => self::RESOURCE_URI], $this->sut->jsonSerialize());
    }

    public function testJsonSerializeWithSelectedClass(): void
    {
        $this->sut->setSelectedClass($this->createClass('selectedClassUri', 'selectedClassLabel'));

        $this->assertEquals(
            [
                'uri' => self::RESOURCE_URI,
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
        $this->sut->setParentClass($this->createClass('parentClassUri', 'parentClassLabel'));

        $this->assertEquals(
            [
                'uri' => self::RESOURCE_URI,
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
        $this->sut->setSelectedClass($this->createClass('selectedClassUri', 'selectedClassLabel'));
        $this->sut->setParentClass($this->createClass('parentClassUri', 'parentClassLabel'));

        $this->assertEquals(
            [
                'uri' => self::RESOURCE_URI,
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
        $this->sut->setSelectedClass(null);
        $this->sut->setParentClass(null);

        $this->assertEquals(['uri' => self::RESOURCE_URI], $this->sut->jsonSerialize());
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

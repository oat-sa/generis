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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\generis\test\unit\model\resource;

use oat\generis\test\TestCase;
use core_kernel_classes_Property;
use oat\generis\model\resource\DependsOnPropertyCollection;

class DependsOnPropertyCollectionTest extends TestCase
{
    /** @var DependsOnPropertyCollection */
    private $sut;

    protected function setUp(): void
    {
        $this->sut = new DependsOnPropertyCollection();
    }

    public function testIsEqual(): void
    {
        $dependsOnPropertyCollection = new DependsOnPropertyCollection();
        $this->assertTrue($this->sut->isEqual($dependsOnPropertyCollection));

        $firstProperty = $this->createMock(core_kernel_classes_Property::class);
        $firstProperty
            ->method('getUri')
            ->willReturn('firstProperty');
        $dependsOnPropertyCollection->append($firstProperty);
        $this->assertFalse($this->sut->isEqual($dependsOnPropertyCollection));

        $secondProperty = $this->createMock(core_kernel_classes_Property::class);
        $secondProperty
            ->method('getUri')
            ->willReturn('secondProperty');
        $this->sut->append($secondProperty);
        $this->assertFalse($this->sut->isEqual($dependsOnPropertyCollection));

        $dependsOnPropertyCollection->append($secondProperty);
        $this->assertFalse($this->sut->isEqual($dependsOnPropertyCollection));

        $this->sut->append($firstProperty);
        $this->assertTrue($this->sut->isEqual($dependsOnPropertyCollection));
    }

    public function testGetPropertyUris(): void
    {
        $this->assertEmpty($this->sut->getPropertyUris());

        $firstProperty = $this->createMock(core_kernel_classes_Property::class);
        $firstProperty
            ->method('getUri')
            ->willReturn('firstProperty');
        $this->sut->append($firstProperty);
        $this->assertEquals(['firstProperty'], $this->sut->getPropertyUris());

        $secondProperty = $this->createMock(core_kernel_classes_Property::class);
        $secondProperty
            ->method('getUri')
            ->willReturn('secondProperty');
        $this->sut->append($secondProperty);
        $this->assertEquals(['firstProperty', 'secondProperty'], $this->sut->getPropertyUris());
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue($this->sut->isEmpty());

        $this->sut->append('value');
        $this->assertFalse($this->sut->isEmpty());
    }
}

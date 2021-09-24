<?php

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
}

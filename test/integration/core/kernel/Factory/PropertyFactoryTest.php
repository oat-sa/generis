<?php

namespace oat\generis\test\integration\core\kernel\Factory;

use core_kernel_classes_Property;
use LogicException;
use oat\generis\model\kernel\Factory\PropertyFactory;
use oat\generis\test\TestCase;

class PropertyFactoryTest extends TestCase
{
    /** @var PropertyFactory */
    private $propertyFactory;

    public function setUp()
    {
        $this->propertyFactory = new PropertyFactory();
    }

    public function testCreate()
    {
        $this->assertInstanceOf(core_kernel_classes_Property::class, $this->propertyFactory->create('test'));
    }

    public function testCreateWithDebug()
    {
        $this->assertInstanceOf(core_kernel_classes_Property::class, $this->propertyFactory->create('test', 'debug'));
    }

    /**
     * @expectedException LogicException
     */
    public function testCreateLogicException()
    {
        $propertyFactoryMock = $this->getMock(PropertyFactory::class, ['getClass']);

        $propertyFactoryMock
            ->method('getClass')
            ->willReturn(null);

        $propertyFactoryMock->create('test');
    }

    public function testGetClass()
    {
        $this->assertEquals(core_kernel_classes_Property::class, $this->propertyFactory->getClass());
    }
}

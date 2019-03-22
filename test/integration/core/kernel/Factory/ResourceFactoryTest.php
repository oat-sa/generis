<?php

namespace oat\generis\test\integration\core\kernel\Factory;

use core_kernel_classes_Resource;
use LogicException;
use oat\generis\model\kernel\Factory\ResourceFactory;
use oat\generis\test\TestCase;

class ResourceFactoryTest extends TestCase
{
    /** @var ResourceFactory */
    private $resourceFactory;

    public function setUp()
    {
        $this->resourceFactory = new ResourceFactory();
    }

    public function testCreate()
    {
        $this->assertInstanceOf(core_kernel_classes_Resource::class, $this->resourceFactory->create('test'));
    }

    public function testCreateWithDebug()
    {
        $this->assertInstanceOf(core_kernel_classes_Resource::class, $this->resourceFactory->create('test', 'debug'));
    }

    /**
     * @expectedException LogicException
     */
    public function testCreateLogicException()
    {
        $resourceFactoryMock = $this->getMock(ResourceFactory::class, ['getClass']);

        $resourceFactoryMock
            ->method('getClass')
            ->willReturn(null);

        $resourceFactoryMock->create('test');
    }

    public function testGetClass()
    {
        $this->assertEquals(core_kernel_classes_Resource::class, $this->resourceFactory->getClass());
    }
}

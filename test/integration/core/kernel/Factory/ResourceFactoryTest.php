<?php

namespace oat\generis\test\integration\core\kernel\Factory;

use core_kernel_classes_Property;
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

    /**
     * @dataProvider fqcnDataProvider
     * @param string $fqcn
     */
    public function testCreateMethodReturnsTheCorrectInstance($fqcn)
    {
        $this->assertInstanceOf(core_kernel_classes_Resource::class, $this->resourceFactory->create(
            $fqcn,
            'test'
        ));
    }

    /**
     * @dataProvider fqcnDataProvider
     * @param string $fqcn
     */
    public function testCreateMethodWithDebugReturnsTheCorrectInstance($fqcn)
    {
        $this->assertInstanceOf(core_kernel_classes_Resource::class, $this->resourceFactory->create(
            $fqcn,
            'test',
            'debug'
        ));
    }

    /**
     * @expectedException LogicException
     */
    public function testCreateMethodThrowsLogicExceptionWhenClassNotExists()
    {
        $this->resourceFactory->create('test', 'test');
    }

    /**
     * @expectedException LogicException
     */
    public function testCreateMethodThrowsLogicExceptionWhenNotIsNotInstanceOfResource()
    {
        $this->resourceFactory->create(ResourceFactory::class, 'test');
    }

    public function fqcnDataProvider()
    {
        return [
            [core_kernel_classes_Resource::class],
            [core_kernel_classes_Property::class],
        ];
    }
}

<?php

namespace oat\generis\test\integration\core\kernel\Factory;

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
        $class = $this->resourceFactory->create($fqcn, 'test');

        $this->assertInstanceOf($fqcn, $class);
        $this->assertEquals('test', $class->getUri());
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessageRegExp /Class not exists/
     */
    public function testCreateMethodThrowsLogicExceptionWhenClassNotExists()
    {
        $this->resourceFactory->create('test', 'test');
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessageRegExp /Creating new class instance failed/
     */
    public function testCreateMethodThrowsLogicExceptionWhenNotIsNotInstanceOfResource()
    {
        $this->resourceFactory->create(ResourceFactory::class, 'test');
    }

    public function fqcnDataProvider()
    {
        return [
            [\core_kernel_classes_Resource::class],
            [\core_kernel_classes_Property::class],
            [\core_kernel_rules_Term::class],
            [\core_kernel_rules_Operation::class],
            [\core_kernel_rules_Expression::class],
            [\core_kernel_rules_Rule::class],
        ];
    }
}

<?php

namespace oat\generis\Model\DependencyInjection;

use ReflectionClass;

/**
 * Class Autowiring
 *
 * @author Martijn Swinkels <m.swinkels@taotesting.com>
 */
class AutoWiring
{

    /**
     * @var array
     */
    private $autoWiringConfig;

    /**
     * AutoWiring constructor.
     */
    public function __construct()
    {
        if ($this->autoWiringConfig === null) {
            $initializer = new AutoWiringInitializer();
            $this->autoWiringConfig = $initializer->initialize();
        }
    }

    /**
     * @param string $className
     */
    public function resolve($className)
    {
        $reflector = new ReflectionClass($className);
        $this->resolveClassDependency($reflector);

        return new $className();
    }

    private function resolveClassDependency(ReflectionClass $dependencyClass)
    {
        $dependencyClassName = $dependencyClass->getName();
        $params = $dependencyClass->getConstructor()->getParameters();

        foreach ($params as $index => $param) {
            $hoi = $param;
        }
    }
}
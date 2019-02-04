<?php

namespace oat\generis\Model\DependencyInjection;

use ReflectionClass;
use ReflectionNamedType;

/**
 * Class ObjectFactory
 *
 * @author Martijn Swinkels <m.swinkels@taotesting.com>
 */
class ObjectFactory
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
    public function create($className)
    {
        if (!class_exists($className)) {
            $oeps = 'jeetje';
        }
        $reflector = new ReflectionClass($className);
        $dependencies = $this->resolveClassDependencies($reflector);

        return new $className(...$dependencies);
    }

    private function resolveClassDependencies(ReflectionClass $class)
    {
        $dependencies = [];
        $constructor = $class->getConstructor();
        if ($constructor === null) {
            return $dependencies;
        }

        $params = $constructor->getParameters();

        foreach ($params as $index => $param) {
            if (isset($params[$param->getName()])) {
                $dependencies[$index] = $params[$param->getName()];
            } elseif ($param->isDefaultValueAvailable() === false) {
                if ($param->getType() instanceof ReflectionNamedType) {
                    $dependencies[$index] = $this->create($param->getType()->getName());
                    continue;
                }
                throw new \Exception('Unable to resolve dependencies for class "' . $class->getName() . '". Failed to resolve parameter "' . $param->getName() . '"');
            }
        }

        return $dependencies;
    }
}
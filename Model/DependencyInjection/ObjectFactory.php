<?php

namespace oat\generis\Model\DependencyInjection;

use LogicException;
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
     * @param string $className
     * @return mixed
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function create($className)
    {
        $reflector = new ReflectionClass($className);
        $dependencies = $this->resolveClassDependencies($reflector);

        return new $className(...$dependencies);
    }

    /**
     * Gather the resolved dependencies needed to instantiate a class.
     *
     * @param ReflectionClass $class
     * @return array
     * @throws \Exception
     */
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
                throw new LogicException('Unable to resolve dependencies for class "' . $class->getName() . '". Failed to resolve parameter "' . $param->getName() . '"');
            }
        }

        return $dependencies;
    }
}

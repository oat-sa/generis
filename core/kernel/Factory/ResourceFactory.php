<?php

namespace oat\generis\model\kernel\Factory;

use core_kernel_classes_Resource;
use LogicException;
use oat\oatbox\service\ConfigurableService;

class ResourceFactory extends ConfigurableService
{
    const SERVICE_ID = 'generis/ResourceFactory';

    /**
     * Factory method
     *
     * @param string $uri
     * @param string $debug
     * @throws LogicException if the class not exists or not instance of 'core_kernel_classes_Resource'
     * @return core_kernel_classes_Resource
     */
    public function create($uri, $debug = '')
    {
        $className = $this->getClass();

        if (!class_exists($className)) {
            throw new LogicException(
                sprintf(
                    'Class not exists: "%s"',
                    $className
                )
            );
        }

        $class = new $className($uri, $debug);

        if (!$class instanceof core_kernel_classes_Resource) {
            throw new LogicException(
                sprintf(
                    'Invalid class provided to Factory: "%s". It must be instance of "%s"',
                    $className,
                    core_kernel_classes_Resource::class
                )
            );
        }

        return $class;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return core_kernel_classes_Resource::class;
    }
}

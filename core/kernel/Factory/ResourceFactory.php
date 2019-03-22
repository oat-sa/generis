<?php

namespace oat\generis\model\kernel\Factory;

use core_kernel_classes_Resource;
use oat\oatbox\service\ConfigurableService;

class ResourceFactory extends ConfigurableService
{
    const SERVICE_ID = 'generis/ResourceFactory';

    /**
     * Factory method
     *
     * @param string $classFQCN
     * @param string $uri
     * @param string $debug
     * @return core_kernel_classes_Resource
     */
    public function create($classFQCN, $uri, $debug = '')
    {
        if (!class_exists($classFQCN)) {
            throw new \LogicException(
                sprintf(
                    'Class not exists: "%s"',
                    $classFQCN
                )
            );
        }

        try {
            $class = new $classFQCN($uri, $debug);
        } catch (\TypeError $e) {
            throw new \LogicException(
                sprintf(
                    'Creating new class instance failed: %s',
                    $e->getMessage()
                )
            );
        }

        if (!$class instanceof core_kernel_classes_Resource) {
            throw new \LogicException(
                sprintf(
                    'Invalid class provided to Factory: "%s". It must be instance of "%s"',
                    $classFQCN,
                    core_kernel_classes_Resource::class
                )
            );
        }

        return $class;
    }
}

<?php

namespace oat\generis\model\kernel\Factory;

use core_kernel_classes_Property;

class PropertyFactory extends ResourceFactory
{
    const SERVICE_ID = 'generis/PropertyFactory';

    /**
     * @return string
     */
    public function getClass()
    {
        return core_kernel_classes_Property::class;
    }
}

<?php

return new oat\oatbox\service\config\ServiceInjectorRegistry(
            [
    \oat\oatbox\service\factory\TaoServiceManager::class => 
        [
            'service' => common_ext_ConfigDriver::singleton(),
        ],
    \oat\oatbox\service\factory\ZendServiceManager::class => 
        [
            'shared'     => 
                [
                    'common.resource'    => false,
                    'common.class'       => false,
                    'common.property'    => false,
                ],
            'invokables' => 
                [
                    'common.resource'        => '\\core_kernel_classes_Resource' ,
                    'common.class'           => '\\core_kernel_classes_Class' ,
                    'common.property'        => '\\core_kernel_classes_Property' ,
                    'event.manager'          => '\\oat\\oatbox\\event\\EventManager',
                ]
        ],
     ]
    );


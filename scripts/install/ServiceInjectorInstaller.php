<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace oat\generis\scripts\install;

/**
 * Description of ServiceInjectorInstaller
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class ServiceInjectorInstaller extends \common_ext_action_InstallAction {

    public function __invoke($params) {

        $this->setServiceInjectorConfig(
                [
                    \oat\oatbox\service\factory\TaoServiceManager::class =>
                    [
                        'driver' => 'ConfigDriver',
                    ],
                    \oat\oatbox\service\factory\ZendServiceManager::class =>
                    [
                        'shared' =>
                        [
                            'common.resource' => false,
                            'common.class' => false,
                            'common.property' => false,
                        ],
                        'invokables' =>
                        [
                            'common.resource' => '\\core_kernel_classes_Resource',
                            'common.class' => '\\core_kernel_classes_Class',
                            'common.property' => '\\core_kernel_classes_Property',
                            'event.manager' => '\\oat\\oatbox\\event\\EventManager',
                        ]
                    ],
                ]
        );
    }

}

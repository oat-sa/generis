<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 *
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
    }

}

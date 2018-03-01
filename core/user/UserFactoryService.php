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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\generis\model\user;

use core_kernel_classes_Resource;
use oat\oatbox\service\ConfigurableService;
use oat\taoEncryption\Service\Session\EncryptedUser;

class UserFactoryService extends ConfigurableService
{
    const SERVICE_ID = 'generis/userFactory';

    const OPTION_CLASS_USER = 'class_user';

    /**
     * @param core_kernel_classes_Resource $userResource
     * @param string $password
     * @return \common_user_User
     * @throws \Exception
     */
    public function createUser(core_kernel_classes_Resource $userResource, $password)
    {
        $class = $this->getOption(static::OPTION_CLASS_USER);
        if ($class === EncryptedUser::class){
            $user = new $class($userResource, $password);
        } else{
            $user = new $class($userResource);
        }

        if (!$user instanceof \common_user_User) {
            throw new \Exception('Incorrect user class provided to the factory.');
        }

        $this->propagate($user);

        return $user;
    }
}
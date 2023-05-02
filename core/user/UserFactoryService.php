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
 */

namespace oat\generis\model\user;

use common_user_User;
use core_kernel_classes_Resource;
use core_kernel_users_GenerisUser;
use Exception;
use oat\oatbox\service\ConfigurableService;

class UserFactoryService extends ConfigurableService implements UserFactoryServiceInterface
{
    /**
     * @param core_kernel_classes_Resource $userResource
     * @param string $hashForEncryption
     *
     * @throws Exception
     *
     * @return common_user_User
     */
    public function createUser(core_kernel_classes_Resource $userResource, $hashForEncryption = null)
    {
        $user = new core_kernel_users_GenerisUser($userResource);

        $this->propagate($user);

        return $user;
    }
}

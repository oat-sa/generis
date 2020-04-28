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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA
 */

declare(strict_types=1);

namespace oat\generis\model\data\permission;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\session\SessionService;
use oat\oatbox\user\User;

class PermissionHelper extends ConfigurableService
{
    /**
     * Filters resources by the provided right if it is supported
     * @return array identifiers of resources that have permission for the provided right
     */
    public function filterByPermission(array $resourceIds, string $right): array
    {
        $provider = $this->getServiceLocator()->get(PermissionInterface::SERVICE_ID);
        if (!in_array($right, $provider->getSupportedRights())) {
            return $resourceIds;
        }
        $permissions = $provider->getPermissions($this->getCurrentUser(),$resourceIds);

        return array_filter($resourceIds, function($id) use ($right, $permissions){ return in_array($right, $permissions[$id]); });
    }

    private function getCurrentUser(): User
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getServiceLocator()->get(SessionService::SERVICE_ID)->getCurrentUser();
    }
}

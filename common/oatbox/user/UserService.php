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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\oatbox\user;

interface UserService
{
    const SERVICE_ID = 'generis/UserService';

    /**
     * Will return the list of the first X users matching the search string
     * @param string $searchString
     * @return User[] List of user matching the search string
     */
    public function findUser($searchString);

    /**
     * @param string $userId
     * @return User
     */
    public function getUser($userId);

    /**
     * @param string[] $userIds
     * @return User[]
     */
    public function getUsers($userIds);
}

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
 * Copyright (c) 2020 (original work) Open Assessment Technologies;
 *
 *
 */

namespace oat\oatbox\user;

use common_user_auth_AuthFailedException;
use common_exception_UserReadableException;

/**
 * Exception indicating that all authentication attempts failed
 *
 * @author Joel Bout <joel@taotesting.com>
 */
class LoginFailedException extends common_user_auth_AuthFailedException
{
    private $exceptions;
    
    public function __construct(array $exceptions)
    {
        $this->exceptions = $exceptions;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_exception_UserReadableException::getUserMessage()
     */
    public function getUserMessage()
    {
        if (count($this->exceptions) == 1) {
            $e = reset($this->exceptions);
            return $e->getUserMessage();
        } else {
            return __('Invalid login or password. Please try again.');
        }
    }
}

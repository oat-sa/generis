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
namespace oat\oatbox\session;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\user\AnonymousUser;
/**
 * Represents a Session on Generis.
 *
 * @access private
 * @author Joel Bout, <joel@taotesting.com>
 * @package generis
 
 */
class SessionService extends ConfigurableService
{
    const SERVICE_ID = 'generis/session';

    /**
     * Returns the currently active session
     * @return \common_session_Session
     */
    public function getCurrentSession()
    {
        return \common_session_SessionManager::getSession();
    }

    /**
     * Returns the current user
     * @throws \common_exception_Error
     * @return \oat\oatbox\user\User
     */
    public function getCurrentUser()
    {
        return $this->getCurrentSession()->getUser();
    } 

    /**
     * Is the current session anonymous or associated to a user?
     * @return boolean
     */
    public function isAnonymous() {
        return $this->getCurrentUser() instanceof AnonymousUser;
    }    
    
}

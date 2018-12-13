<?php
use oat\generis\model\GenerisRdf;

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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * @author CRP Henri Tudor - TAO Team
 * @license GPLv2
 *
 */

class common_test_TestUserSession implements common_session_StatelessSession
{
    /**
     * Code of the current data language to use
     * 
     * @var string
     */
    private $dataLanguage = DEFAULT_LANG;
    
    private $uiLanguage = DEFAULT_LANG;

    /**
     * @var common_test_TestUser
     */
    private $user;
    
    /**
     * Code of the timezone to use during the test
     * 
     * @var string
     */
    private $timezone = TIME_ZONE;
    
    public function __construct() {
        $this->user = new common_test_TestUser([
            GenerisRdf::PROPERTY_USER_DEFLG => [DEFAULT_LANG],
            GenerisRdf::PROPERTY_USER_UILG => [DEFAULT_LANG]
        ]);
    }
    
    public function getUser() {
        return $this->user;
    }
    
    /**
     * {@inheritDoc}
     * @see common_session_Session::getUserUri()
     */
    public function getUserUri() {
        return $this->getUser()->getIdentifier();
    }
    
    /**
     * {@inheritDoc}
     * @see common_session_Session::getUserLabel()
     */
    public function getUserLabel() {
        return 'Virtual Test User';
    }
    
    /**
     * {@inheritDoc}
     * @see common_session_Session::getUserRoles()
     */
    public function getUserRoles() {
        return array();
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getUserPropertyValues()
     */
    public function getUserPropertyValues($property) {
        return array();
    }
    
    
    /**
     * changes the current data language
     * 
     * @param string $languageCode
     */
    public function setDataLanguage($languageCode) {
        $this->getUser()->setPropertyValues(GenerisRdf::PROPERTY_USER_DEFLG, [$languageCode]);
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getDataLanguage()
     */
    public function getDataLanguage() {
        $values = $this->getUser()->getPropertyValues(GenerisRdf::PROPERTY_USER_DEFLG);
        return $values[0];
    }
    
    /**
     * Changes the current interface language
     * 
     * @param string $languageCode
     */
    public function setInterfaceLanguage($languageCode) {
        return $this->getUser()->setPropertyValues(GenerisRdf::PROPERTY_USER_UILG, [$languageCode]);
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getInterfaceLanguage()
     */
    public function getInterfaceLanguage() {
        $values = $this->getUser()->getPropertyValues(GenerisRdf::PROPERTY_USER_UILG);
        return $values[0];
    }
    
    /**
     * Changes the timezone of the test session
     * 
     * @param string $timezone
     */
    public function setTimeZone($timezone) {
        $this->timezone = $timezone;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::getTimeZone()
     */
    public function getTimeZone() {
        return $this->timezone;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_session_Session::refresh()
     */
    public function refresh() {
        // nothign to do
    }
}
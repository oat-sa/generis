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

namespace oat\oatbox\user;

use oat\oatbox\service\ConfigurableService;
use oat\generis\model\GenerisRdf;

/**
 * class UserLanguageService
 * @package oat\oatbox\user
 */
class UserLanguageService extends ConfigurableService implements UserLanguageServiceInterface
{

    const OPTION_LOCK_DATA_LANGUAGE = 'lock_data_language';

    /**
     * {@inheritDoc}
     * @see \oat\oatbox\user\UserLanguageServiceInterface::getDefaultLanguage()
     */
    public function getDefaultLanguage()
    {
        return DEFAULT_LANG;
    }

    /**
     * @inheritdoc
     */
    public function getDataLanguage(User $user)
    {
        $result = $this->getDefaultLanguage();
        if ($this->isDataLanguageEnabled()) {
            $lang = $user->getPropertyValues(GenerisRdf::PROPERTY_USER_DEFLG);
            $result = empty($lang) ? $this->getDefaultLanguage() : (string)current($lang);
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getInterfaceLanguage(User $user)
    {
        $lang = $user->getPropertyValues(GenerisRdf::PROPERTY_USER_UILG);
        return empty($lang) ? $this->getDefaultLanguage() : (string)current($lang);
    }

    /**
     * @inheritdoc
     */
    public function isDataLanguageEnabled()
    {
        return !$this->hasOption(self::OPTION_LOCK_DATA_LANGUAGE) || $this->getOption(self::OPTION_LOCK_DATA_LANGUAGE) === false;
    }
}

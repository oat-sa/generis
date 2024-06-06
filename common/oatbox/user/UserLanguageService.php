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
 * Copyright (c) 2018-2024 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\oatbox\user;

use oat\oatbox\service\ConfigurableService;
use oat\generis\model\GenerisRdf;
use oat\tao\helpers\Layout;

class UserLanguageService extends ConfigurableService implements UserLanguageServiceInterface
{
    public const OPTION_LOCK_DATA_LANGUAGE = 'lock_data_language';
    public const OPTION_AUTHORING_LANGUAGE = 'authoring_language';
    public const OPTION_INTERFACE_LANGUAGE = 'interface_language';

    public const LANG_PREFIX = '-S';

    /** @var ?string */
    private $customInterfaceLanguage;

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
        if (!empty($this->customInterfaceLanguage)) {
            return $this->customInterfaceLanguage;
        }

        $lang = $user->getPropertyValues(GenerisRdf::PROPERTY_USER_UILG);
        if (empty($lang)) {
            $lang = $this->getOption(self::OPTION_INTERFACE_LANGUAGE, DEFAULT_LANG);
        } else {
            $lang = (string)current($lang);
        }

        $lang = $this->checkPrefix($lang);

        return $lang;
    }

    /**
     * @inheritdoc
     */
    public function isDataLanguageEnabled()
    {
        return !$this->hasOption(self::OPTION_LOCK_DATA_LANGUAGE)
            || $this->getOption(self::OPTION_LOCK_DATA_LANGUAGE) === false;
    }

    public function getAuthoringLanguage(): string
    {
        return $this->getOption(self::OPTION_AUTHORING_LANGUAGE, $this->getDefaultLanguage());
    }

    /**
     * @inheritdoc
     */
    public function setCustomInterfaceLanguage(?string $customInterfaceLanguage): void
    {
        $this->customInterfaceLanguage = $customInterfaceLanguage;
    }

    /**
     * Short description of method notContainPrefix
     *
     * @param string $language
     * @access private
     * @author Sultan Sagi, <sultan.sagiyev@taotesting.com>
     * @return bool
     */
    private function notContainPrefix($language)
    {
        $pattern = '/' . self::LANG_PREFIX . '$/';

        return Layout::isSolarDesignEnabled() && !preg_match($pattern, $language, $matches);
    }

    /**
     * Short description of method addPrefix
     *
     * @param string $language
     * @access private
     * @author Sultan Sagi, <sultan.sagiyev@taotesting.com>
     * @return string
     */
    private function addPrefix($language)
    {
        return $language . self::LANG_PREFIX;
    }

    /**
     * Short description of method addPrefix
     *
     * @param string $language
     * @access private
     * @author Sultan Sagi, <sultan.sagiyev@taotesting.com>
     * @return string
     */
    private function checkPrefix($language)
    {
        if ($this->notContainPrefix($language)) {
            $localesDir = 'tao/views/locales';
            $dir = dirname(__FILE__) . '/../../../../' . $localesDir . '/' . $this->addPrefix($language);
            if (is_dir($dir)) {
                $language = $this->addPrefix($language);
            }
        }

        return $language;
    }
}

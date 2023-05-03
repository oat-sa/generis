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

namespace oat\oatbox\user;

/**
 * Interface UserLanguageServiceInterface
 *
 * @package oat\oatbox\user
 */
interface UserLanguageServiceInterface
{
    public const SERVICE_ID = 'generis/UserLanguageService';

    /**
     * @return string language code (e.g. 'en-US')
     */
    public function getDefaultLanguage();

    /**
     * @param User $user
     *
     * @return string language code (e.g. 'en-US')
     */
    public function getDataLanguage(User $user);

    /**
     * @param User $user
     *
     * @return string language code (e.g. 'en-US')
     */
    public function getInterfaceLanguage(User $user);

    /**
     * Whether users data language enabled or not
     *
     * @return boolean
     */
    public function isDataLanguageEnabled();

    /**
     * When a custom interface language is set, it overrides the interface language retrieved in the
     * getInterfaceLanguage method.
     *
     * @param ?string $customInterfaceLanguage
     */
    public function setCustomInterfaceLanguage(?string $customInterfaceLanguage): void;
}

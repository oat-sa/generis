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
 * Copyright (c) 2017 Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\oatbox\service;

/**
 * Class ConfigurationService
 *
 * Wrapper of array configuration to accept only ConfigurableService as config
 *
 * @package oat\oatbox\service
 */
class ConfigurationService extends ConfigurableService
{
    const OPTION_CONFIG = 'config';

    /**
     * Add Configuration helper to default header
     *
     * @return string
     */
    public function getHeader()
    {
        $header = parent::getHeader();
        $header .= PHP_EOL .
            '/**' . PHP_EOL .
            ' * To avoid your config to be wrapped, use a ConfigurableService :)' . PHP_EOL .
            ' */ ' . PHP_EOL;
        return $header;
    }

}
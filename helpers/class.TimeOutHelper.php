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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author "Lionel Lecaque, <lionel@taotesting.com>"
 * @license GPLv2
 *
 * @package generis
 */
class helpers_TimeOutHelper
{
    public const LONG = 200;
    public const MEDIUM = 100;
    public const SHORT = 30;
    public const NO_TIMEOUT = 0;

    public static function setTimeOutLimit($value = self::LONG)
    {
        $configValue = ini_get('max_execution_time');

        if ($value > 0 && $configValue > 0) {
            set_time_limit(max($configValue, $value));
        } else {
            set_time_limit(self::NO_TIMEOUT);
        }
    }

    public static function reset()
    {
        set_time_limit(ini_get('max_execution_time'));
    }
}

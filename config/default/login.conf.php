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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 */

use oat\oatbox\user\LoginService;

return new LoginService(array(
    LoginService::OPTION_DISABLE_AUTO_COMPLETE => false,
    LoginService::OPTION_BLOCK_IFRAME_USAGE => true,
    LoginService::OPTION_USE_CAPTCHA => false,
    LoginService::OPTION_USE_HARD_LOCKOUT => false,
    LoginService::OPTION_CAPTCHA_FAILED_ATTEMPTS => 2,
    LoginService::OPTION_LOCKOUT_FAILED_ATTEMPTS => 5,
    LoginService::OPTION_SOFT_LOCKOUT_PERIOD => 'PT15M',
    LoginService::OPTION_TRUSTED_TERMINAL_TTL => 180,
));

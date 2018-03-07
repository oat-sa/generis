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
 */

use oat\generis\Helper\SystemHelper;


/**
 * A utility class focusing on the current instance.
 * 
 * @author Joel Bout <joel@taotesting.com>
 * @deprecated
 */
class helpers_PlatformInstance
{
    /**
     * Returns a whenever or not the current instance is used as demo instance
     *
     * @deprecated use ApplicationHelper::isDemo
     * @return boolean
     */
    static public function isDemo() {
        common_Logger::w('Deprecated fucntion helpers_PlatformInstance::isDemo called, please use oat\tao\helpers\ApplicationHelper');
        return false;
    }
    
    /**
     * Returns whenever or not Tao is installed on windows
     * @deprecated use SystemHelper::isWindows
     * @return boolean
     */
    static public function isWindows() {
        return SystemHelper::isWindows();
    }
}
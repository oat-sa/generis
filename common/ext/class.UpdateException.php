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
 * Copyright (c) 2017 Open Assessment Technologies SA
 * 
 */

/**
 * This exception must be thrown when an error occurs while an extension
 * is being updated and indicates a failure to update
 */
class common_ext_UpdateException extends common_ext_ExtensionException implements common_log_SeverityLevel
{
    /**
     * (non-PHPdoc)
     * @see common_ext_ExtensionException::getSeverity()
     */
    public function getSeverity()
    {
        return common_Logger::ERROR_LEVEL;
    }
}

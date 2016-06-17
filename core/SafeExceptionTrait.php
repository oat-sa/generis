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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA
 *
 */

namespace oat\generis\model;
use Exception;

/**
 * Trait for classes that want to access the ontology
 *
 * @author Mikhail Kamarouski <kamarouski@1pt.com>
 */
trait SafeExceptionTrait
{
    /**
     * Removes any information about internal essence out of exception
     * @param $e
     * @return Exception
     */
    private function sanitizeException(Exception $e)
    {
        if (DEBUG_MODE) {
            return $e;
        }
        $exceptionClass = get_class($e);
        return new $exceptionClass('An unexpected error');
    }

}
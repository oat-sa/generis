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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

/**
 * Class common_persistence_sql_pdo_sqlite_SchemaManager
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class common_persistence_sql_pdo_sqlite_SchemaManager extends common_persistence_sql_pdo_SchemaManager
{
    /**
     * Short description of method getIndexAlreadyExistsErrorCode
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return string
     */
    public function getIndexAlreadyExistsErrorCode()
    {
        return (string) "42P07";
    }

    /**
     * (non-PHPdoc)
     * @see common_persistence_sql_SchemaManager::getColumnNotFoundErrorCode()
     */
    public function getColumnNotFoundErrorCode(){
        return '42703';
    }

}
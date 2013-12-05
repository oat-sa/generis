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
 * @author Lionel Lecaque  <lionel@taotesting.com>
 * @license GPLv2
 * @package core
 * @subpackage persistence
 *
 */
class common_persistence_SqlPersistence extends common_persistence_Persistence
{

    public function __construct($params, common_persistence_driver $driver){
        parent::__construct($params, $driver);
        if(isset($params["driver"]) && $params["driver"]== 'pdo_mysql'){
            //activate mysql ansi quotes support
            $this->exec('SET SESSION SQL_MODE=\'ANSI_QUOTES\';');
        }
    }

    /**
     * @access
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param mixed $statement
     */
    public function exec($statement)
    {
        return $this->getDriver()->exec($statement);
    }


    /**
     * @access
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param mixed $statement
     */
    public function query($statement)
    {
        return $this->getDriver()->query($statement);
    }
}

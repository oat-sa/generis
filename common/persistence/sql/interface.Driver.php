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
 * @package generis
 *
 */

use Doctrine\DBAL\DBALException;

interface common_persistence_sql_Driver extends common_persistence_Driver
{
    /**
     * @inheritdoc
     * @throws DBALException;
     */
    public function query($statement,$params, array $types = []);

    /**
     * @inheritdoc
     * @throws DBALException;
     */
    public function exec($statement,$params, array $types = []);
    
    /**
     * Insert a single row into the database.
     * 
     * column names and values will be encoded
     * 
     * @param string $tableName name of the table
     * @param array $data An associative array containing column-value pairs.
     * @return integer The number of affected rows.
     * @throws DBALException
     */
    public function insert($tableName, array $data, array $types = []);
    
    public function insertMultiple($tableName, array $data);

    /**
     * @param string $tableName
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function updateMultiple($tableName, array $data);

    public function getSchemaManager();
    
    public function getPlatForm();
    
    public function lastInsertId($name = null);
    
    public function quote($parameter, $parameter_type = PDO::PARAM_STR);

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getDbalConnection();
}

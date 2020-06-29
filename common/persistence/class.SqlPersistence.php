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
 * Copyright (c) 2013-2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Lionel Lecaque  <lionel@taotesting.com>
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 *
 */

use Doctrine\DBAL\Driver\Statement;

/**
 * Persistence base on SQL
 */
class common_persistence_SqlPersistence extends common_persistence_Persistence
{
    /**
     * @return common_persistence_sql_SchemaManager
     */
    public function getSchemaManager()
    {
        return $this->getDriver()->getSchemaManager();
    }

    /**
     * @return common_persistence_sql_Platform
     */
    public function getPlatForm()
    {
        return $this->getDriver()->getPlatform();
    }

    /**
     * Execute a statement.
     *
     * @param string $statement
     * @param array $params
     * @param array $types
     * @return int number of updated rows
     */
    public function exec($statement, array $params = [], array $types = [])
    {
        return $this->getDriver()->exec($statement, $params, $types);
    }

    /**
     * Inserts one row.
     *
     * @param string $tableName
     * @param array $data
     * @param array $types
     * @return int number of updated rows
     */
    public function insert($tableName, array $data, array $types = [])
    {
        return $this->getDriver()->insert($tableName, $data, $types);
    }

    /**
     * Inserts multiple rows.
     *
     * @param $tableName
     * @param array $data
     * @param array $types
     * @return int number of updated rows
     */
    public function insertMultiple($tableName, array $data, array $types = [])
    {
        return $this->getDriver()->insertMultiple($tableName, $data, $types);
    }

    /**
     * Update multiple rows.
     *
     * @param string $table
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function updateMultiple($table, array $data)
    {
        return $this->getDriver()->updateMultiple($table, $data);
    }

    /**
     * Executes parameterized query.
     *
     * @param string $statement
     * @param array $params
     * @param array $types
     * @return Statement
     */
    public function query($statement, $params = [], array $types = [])
    {
        return $this->getDriver()->query($statement, $params, $types);
    }
    

    /**
     * Convenience access to quote.
     *
     * @param string $parameter The parameter to quote.
     * @param int $parameter_type A PDO PARAM_XX constant.
     * @return string The quoted string.
     */
    public function quote($parameter, $parameter_type = PDO::PARAM_STR)
    {
        return $this->getDriver()->quote($parameter, $parameter_type);
    }
    
    
    /**
     * Convenience access to lastInsertId.
     *
     * @param string $name
     * @return string The quoted string.
     */
    public function lastInsertId($name = null)
    {
        return $this->getDriver()->lastInsertId($name);
    }


    /**
     * Execute a function within a transaction.
     *
     * @param Closure $func The function to execute in a transactional way.
     *
     * @return mixed The value returned by $func
     *
     * @throws Throwable
     */
    public function transactional(Closure $func)
    {
        return $this->getDriver()->transactional($func);
    }
}

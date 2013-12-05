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
 * @package 
 * @subpackage 
 *
 */
class common_persistence_SqlDriver implements common_persistence_Driver
{

    private $connection;

    /**
     * @param array $params
     * @return \Doctrine\DBAL\Connection;
     */
    function connect($id, array $params)
    {
        $config = new \Doctrine\DBAL\Configuration();
        $this->connection = \Doctrine\DBAL\DriverManager::getConnection($params,$config);
        return new common_persistence_SqlPersistence($params, $this);
    }
    
    /**
     * 
     * @return string
     */
    public function getPersistenceClass(){
        return "common_persistence_SqlPersistence";
    }

    /**
     * @return \Doctrine\DBAL\Schema\AbstractSchemaManager;
     */
    public function getSchemaManager(){
        return $this->connection->getSchemaManager();
    }

    /**
     * @return \Doctrine\DBAL\Doctrine\DBAL\Platforms;
     */
    public function getDatabasePlatform(){
        return $this->connection->getDatabasePlatform();
    }
    
    /**
     * @access
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param mixed $statement
     */
    public function exec($statement)
    {
        return $this->connection->exec($statement);
    }
    
    
    /**
     * @access
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param mixed $statement
     */
    public function query($statement)
    {
        return $this->connection->query($statement);
    }


}

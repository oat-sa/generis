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
 */

/**
 * Dbal Driver 
 * 
 * @author Lionel Lecaque  <lionel@taotesting.com>
 * @license GPLv2
 * @package generis
 *
 */
class common_persistence_sql_dbal_Driver implements common_persistence_sql_Driver
{
    use common_persistence_sql_MultipleOperations;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var string
     */
    private $driverManagerClass;

    /**
     * Connect to Dbal
     * 
     * @param string $id
     * @param array $params
     * @return common_persistence_Persistence|common_persistence_SqlPersistence
     * @throws \Doctrine\DBAL\DBALException
     */
    public function connect($id, array $params)
    {
        $isMysqlDbal = false;
        if (isset($params['connection'])) {
            $connectionParams = $params['connection'];
            $isMysqlDbal = isset($connectionParams['driver']) && $connectionParams['driver'] === 'pdo_mysql';
        } else {
            $connectionParams = $params;
            $connectionParams['driver'] = str_replace('dbal_', '', $connectionParams['driver']);
        }

        $this->persistentConnect($connectionParams);

        if ($isMysqlDbal) {
            $this->exec('SET SESSION SQL_MODE=\'ANSI_QUOTES\';');
        }

        return new common_persistence_SqlPersistence($params, $this);
    }

    /**
     * Endless connection
     *
     * @param $connectionParams
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function persistentConnect($connectionParams)
    {
        $config = new \Doctrine\DBAL\Configuration();
//          $logger = new Doctrine\DBAL\Logging\EchoSQLLogger();
//          $config->setSQLLogger($logger);

        $connLimit = 3; // Max connection attempts.
        $counter = 0; // Connection attempts counter.

        while (true) {
            try {
                /** @var \Doctrine\DBAL\Connection connection */
                $this->connection = $this->getConnection($connectionParams, $config);
                // to generate DBALException if no connection
                $this->connection->ping();
                break;
            } catch (\Doctrine\DBAL\DBALException $e) {
                $this->connection = null;
                $counter++;

                if ($counter === $connLimit){
                    // Connection attempts exceeded.
                    throw $e;
                }
            }
        }
    }

    /**
     * @param $params
     * @param $config
     * @return \Doctrine\DBAL\Connection
     *
     * @throws \Doctrine\DBAL\DBALException
     *
     */
    private function getConnection($params, $config)
    {
        return call_user_func($this->getDriverManagerClass() . '::getConnection', $params, $config);
    }

    /**
     * @return string
     */
    private function getDriverManagerClass()
    {
        if (!$this->driverManagerClass || !class_exists($this->driverManagerClass)) {
            $this->driverManagerClass = \Doctrine\DBAL\DriverManager::class;
        }
        return $this->driverManagerClass;
    }

    /**
     * @param string $driverManagerClass
     */
    protected function setDriverManagerClass($driverManagerClass)
    {
        $this->driverManagerClass = $driverManagerClass;
    }

    /**
     * (non-PHPdoc)
     * @see common_persistence_sql_Driver::getPlatForm()
     */
    public function getPlatForm(){
        return new common_persistence_sql_Platform($this->getDbalConnection());
    }
    
    /**
     * (non-PHPdoc)
     * @see common_persistence_sql_Driver::getSchemaManager()
     */
    public function getSchemaManager()
    {
        return new common_persistence_sql_dbal_SchemaManager($this->connection->getSchemaManager());
    }
    
    /**
     * Execute the statement with provided params
     *
     * @param mixed $statement
     * @param array $params
     * @param array $types
     * @return integer number of affected row
     */
    public function exec($statement, $params = [], array $types = [])
    {
        return $this->connection->executeUpdate($statement, $params, $types);
    }
    
    /**
     * Query  the statement with provided params
     * 
     * @param mixed $statement
     * @param array $params
     * @param array $types
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function query($statement, $params = [], array $types = [])
    {
        return $this->connection->executeQuery($statement, $params, $types);
    }
    
    /**
     * Convenience access to PDO::quote.
     *
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param string $parameter The parameter to quote.
     * @param int $parameter_type A PDO PARAM_XX constant.
     * @return string The quoted string.
     */
    public function quote($parameter, $parameter_type = PDO::PARAM_STR){
        return $this->connection->quote($parameter, $parameter_type);
    }
    
    /**
     * @inheritdoc
     */
    public function insert($tableName, array $data, array $types = [])
    {
        $cleanColumns = array();
        foreach ($data as $columnName => $value) {
            $cleanColumns[$this->getPlatForm()->quoteIdentifier($columnName)] = $value;
        }
        return $this->connection->insert($tableName, $cleanColumns, $types);
    }
    
    /**
     * Convenience access to PDO::lastInsertId.
     *
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param string $name
     * @return string The quoted string.
     */
    public function lastInsertId($name = null){
        return $this->connection->lastInsertId($name);
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getDbalConnection()
    {
        return $this->connection;
    }
    
    /**
     * Returns the name of the connections database
     * @return string
     */
    public function getDataBase()
    {
        return $this->connection->getDatabase();
    }
    
}

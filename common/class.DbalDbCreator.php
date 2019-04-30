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
 * @package tao
 *
 */

use Doctrine\DBAL\DBALException;
use oat\generis\model\RdsSchema;

class common_DbalDbCreator {

    public function setupDatabase(common_persistence_SqlPersistence $p)
    {
        $this->createDatabase($p);
        $this->initTaoDataBase($p);
    }


	/**
	 * @author "Lionel Lecaque, <lionel@taotesting.com>"
	 */
    private function createDatabase(common_persistence_SqlPersistence $p, $dbName)
	{
        $schemaManager = $p->getSchemaManager()->getDbalSchemaManager();
	    if ($this->dbExists($dbName)) {
	        
	        try {
	            //If the target Sgbd is mysql select the database after creating it
	            if ($installData['db_driver'] == 'pdo_mysql'){
	                $dbCreator->setDatabase($installData['db_name']);
	            }
	            $dbCreator->cleanDb($dbName);
	            
	        } catch (Exception $e){
	            $this->log('i', 'Problem cleaning db will try to erase the whole db: '.$e->getMessage());
	            try {
	                $dbCreator->destroyTaoDatabase($dbName);
	            } catch (Exception $e){
	                $this->log('i', 'isssue during db cleaning : ' . $e->getMessage());
	            }
	        }
	        $this->log('i', "Dropped all tables");
	    }
	    // Else create it
	    else {
	        try {
	            $escapedName = $schemaManager->getDatabasePlatform()->quoteIdentifier($dbName);
	            $schemaManager->createDatabase($escapedName);
	            $this->log('i', "Created database ".$installData['db_name']);
	        } catch (Exception $e){
	            throw new tao_install_utils_Exception('Unable to create the database, make sure that '.$installData['db_user'].' is granted to create databases. Otherwise create the database with your super user and give to  '.$installData['db_user'].' the right to use it.');
	        }
	        
	        //If the target Sgbd is mysql select the database after creating it
	        if ($installData['db_driver'] == 'pdo_mysql'){
	            $dbCreator->setDatabase($installData['db_name']);
	        }
	        
	    }
	}
	
	/**
	 * @author "Lionel Lecaque, <lionel@taotesting.com>"
	 */
	public function initTaoDataBase(common_persistence_SqlPersistence $p)
	{
	    $generisSchemaGenerator = new RdsSchema();
	    $queries = $generisSchemaGenerator->getSchema()->toSql($p->getPlatForm());
	    foreach ($queries as $query){
	        $p->exec($query);
	    }
	}
	
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param array $params
     * @throws tao_install_utils_Exception
     */
    public function __construct($params){
   		try{
            $this->connection = $this->buildDbalConnection($params);
            $this->dbConfiguration = $params;
            $this->buildSchema();

   		}
   		catch(Exception $e){
   			$this->connection = null;
            common_Logger::e($e->getMessage() . $e->getTraceAsString());
   			throw new tao_install_utils_Exception('Unable to connect to the database ' . $params['dbname'] . ' with the provided credentials: ' . $e->getMessage());
   		}
   	}

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $dbName
     */
    public function dbExists($dbName){
        $sm = $this->getSchemaManager();
		common_Logger::d('Check if database with name \'' .$dbName. '\' exists for driver ' . $this->dbConfiguration['driver']);
        if($this->dbConfiguration['driver'] == 'pdo_oci'){
        	common_Logger::d('Oracle special query dbExist');
        	return in_array(strtoupper($dbName),$sm->listDatabases());
        }
        else {
        	return in_array($dbName,$sm->listDatabases());
        }
    }
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $tableName
     */
    public function tableExists($tableName){
    	$sm = $this->getSchemaManager();
    	return $sm->tableExists($tableName);
    }
    
    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $name
     */
    public function setDatabase($name){
        $this->connection->close();
        common_Logger::d('Switch to database ' . $name);
        $this->dbConfiguration['dbname'] = $name;
        $this->connection = $this->buildDbalConnection($this->dbConfiguration);
        
    }

    /**
     * @param $params
     * @return \Doctrine\DBAL\Connection
     */
    private function buildDbalConnection($params)
    {
        $config = new Doctrine\DBAL\Configuration();
        return  \Doctrine\DBAL\DriverManager::getConnection($params, $config);
    }

    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    public function listDatabases(){
    	$sm = $this->getSchemaManager();
    	return $sm->listDatabases();
    }

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param unknown $file
     */
    public function loadProc($file){
        
        $procedureCreator = new tao_install_utils_ProceduresCreator($this->dbConfiguration['driver'],$this->connection);
        $procedureCreator->load($file);

    }
    

    public function addModel($modelId,$namespace){
        common_Logger::d('add modelid :' . $k . ' with NS :' . $v);
        $this->connection->insert("models" , array('modelid' => $k , 'modeluri' => $v ));
    }
    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    public function addModels(){
        foreach ($this->modelArray as $k => $v){
            $this->addModel();
        }
    } 
    
    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    public function removeGenerisUser(){
       $this->connection->executeUpdate("DELETE FROM statements WHERE subject = ?" , array('http://www.tao.lu/Ontologies/TAO.rdf#installator'));    
    }
    

    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    public function destroyTaoDatabase(){
    	$platform = $this->connection->getDatabasePlatform();
    	$queries = $this->schema->toDropSql($platform);
    	foreach ($queries as $query){
    		$this->connection->executeUpdate($query);
    	}
    	//drop sequence
    	$sm = $this->getSchemaManager();
    	$sequences = $sm->listSequences();
    	foreach($sequences as $name){
    		$sm->dropSequence($name);
    	}
    	
    	
    }
    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    public function listTables(){
    	$sm = $this->getSchemaManager();
    	return $sm->listTableNames();
    
    }
    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param unknown $database
     */
    public function dropDatabase($database){
        $sm = $this->getSchemaManager();
        return $sm->dropDatabase($database);

    }

    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function cleanDb(){
        $sm = $this->getSchemaManager();
        $platform = $this->connection->getDatabasePlatform();
        $tables = $sm->listTableNames();
        
        while (!empty($tables)) {
            $oldCount = count($tables);
            foreach(array_keys($tables) as $id){
                $name = $tables[$id];
                try {
                    $sm->dropTable($name);
                    common_Logger::d('Droped table: '  . $name);
                    unset($tables[$id]);
                } catch (DBALException $e) {
                    common_Logger::w('Failed to drop: '  . $name);
                }
            }
            if (count($tables) == $oldCount) {
                throw new common_exception_Error('Unable to clean DB');
            }
        }
    }

    /**
     * @return Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    private function getSchemaManager()
    {
        return $this->connection->getSchemaManager();

    }


}
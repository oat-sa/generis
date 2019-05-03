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
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use oat\oatbox\log\LoggerAwareTrait;
use Psr\Log\LoggerAwareInterface;

class common_DbalDbCreator implements LoggerAwareInterface {
    
    use LoggerAwareTrait;

    public function setupDatabase(common_persistence_SqlPersistence $p)
    {
        $dbalDriver = $p->getDriver();
        if (!$dbalDriver instanceof common_persistence_sql_dbal_Driver) {
            throw new common_exception_InconsistentData('Non DBAL driver no longer supported');
        }
        $dbName = $dbalDriver->getDataBase();
        $this->createDatabase($p, $dbName);
        $this->initTaoDataBase($p);
    }


	/**
	 * @author "Lionel Lecaque, <lionel@taotesting.com>"
	 */
    private function createDatabase(common_persistence_SqlPersistence $p, $dbName)
	{
        $schemaManager = $p->getSchemaManager()->getDbalSchemaManager();
        if ($this->dbExists($schemaManager, $dbName)) {
	        
	        try {
	            $this->cleanDb($p);
	        } catch (Exception $e){
	            $this->logInfo('Problem cleaning db will try to erase the whole db: '.$e->getMessage());
	            try {
	                $this->destroyTaoDatabase($dbName);
	            } catch (Exception $e){
	                $this->logInfo('isssue during db cleaning : ' . $e->getMessage());
	            }
	        }
	        $this->logInfo("Dropped all tables");
	    }
	    // Else create it
	    else {
	        try {
	            $escapedName = $schemaManager->getDatabasePlatform()->quoteIdentifier($dbName);
	            $schemaManager->createDatabase($escapedName);
	            $this->logInfo("Created database ".$installData['db_name']);
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
	    $queries = $p->getPlatForm()->schemaToSql($generisSchemaGenerator->getSchema());
	    foreach ($queries as $query){
	        $p->exec($query);
	    }
	}

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $dbName
     */
   	public function dbExists(AbstractSchemaManager $schemaManager, $dbName){
   	    return in_array($dbName,$schemaManager->listDatabases());
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
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function cleanDb(common_persistence_SqlPersistence $p)
    {
        $sm = $p->getSchemaManager()->getDbalSchemaManager();
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
}
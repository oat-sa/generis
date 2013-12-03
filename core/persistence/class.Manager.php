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
 *
 * Very close to Doctrine/DBAL/Sharding/ShardManager but may also handle other persistence graph oriented
 *
 * @author Lionel Lecaque  <lionel@taotesting.com>
 * @license GPLv2
 * @package core
 * @subpackage persistence
 *
 */
class core_persistence_Manager
{
    /**
     * @var array
     */
    private $availableConnections = array();

    /**
     * @var array
     */
    private $configuration;

    /**
     * @var core_persistence_Persistence
     */
    private $current;

    /**
     * @var core_persistence_Manager
     */
    private static $instance = null;

    /**
     * @var array
     */
    private static $driverMap = array(
           'pdo_mysql'  => 'core_persistence_SqlDriver',
           'pdo_sqlite' => 'core_persistence_SqlDriver',
           'pdo_pgsql'  => 'core_persistence_SqlDriver',
           'pdo_oci'    => 'core_persistence_SqlDriver',
           'pdo_ibm'    => 'core_persistence_SqlDriver',
           'pdo_sqlsrv' => 'core_persistence_SqlDriver',
           'phpredis'   => 'core_persistence_PhpRedisDriver',
           'phpfile'    => 'core_persistence_PhpFileDriver'
    );


    /**
     * Constructor
     * 
     * @access private
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param unknown $configuration
     */
    private function __construct(){
        if(!isset($GLOBALS['generis_persistences'])) {
            throw new common_Exception('Persistence Configuration not found');
        }
        $this->configuration = $GLOBALS['generis_persistences'];
        foreach($this->configuration as $persistenceId => $config){
            //common_Logger::d('Checking persistence ' . $persistenceId);
            $driverStr = $config['driver'];
            $driverClassName = self::$driverMap[$driverStr];
            if (class_exists($driverClassName)){
                $driver = new $driverClassName();
            }
            else{
                common_Logger::e('Driver not found check your database configuration');
            }
            $persistenceClass =  $driver->getPersistenceClass();
            if (class_exists($persistenceClass)){
                $conn= new $persistenceClass($config,$driver);
                $this->availableConnections[$persistenceId] = $conn;
            } else {
                common_Logger::e('Persistence not found, bad persistence implementation');
            }

        }
        $this->selectPersistence('default');
    }

    /**
     * Singleton
     * 
     * @access public 
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param unknown $configuration
     */
    public static function singleton(){
        $returnValue = null;
		if (!isset(self::$instance)) {

            $c = __CLASS__;
            self::$instance = new $c();
        }
        return self::$instance;
    }

    /**
     * Set the current persistence
     * 
     * @access
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $persistenceId
     * @return core_persistence_Persistence
     */
    public function selectPersistence($persistenceId){
        $returnValue = null;
        if(isset($this->availableConnections[$persistenceId])) {
            $returnValue = $this->availableConnections[$persistenceId];
            $returnValue->connect();
            $this->current = array($persistenceId => $returnValue);
        }
        else{
            common_Logger::e('Could not found persistence ' . $persistenceId );
        }
        return $returnValue;
    }

    /**
     * return currecnt connection
     * 
     * @access public
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @return mixed
     */
    public function getCurrentPersistence(){
        return current($this->current);

    }

    /**
     * return current persistence id
     * 
     * @access public
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @return string
     */
    public function getPersistenceId(){
        return key($this->current);
    }

    /**
     * @access
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @return array
     */
    public function getPersistences(){
        return $this->availableConnections;
    }

}

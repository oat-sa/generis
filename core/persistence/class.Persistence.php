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
abstract class core_persistence_Persistence
{
    /**
     * connection to the persistence 
     * 
     * @var mixed
     */
    private $connection;
    /**
     * Driver of the persistence
     * 
     * @var core_persistence_Driver
     */
    private $driver;
    /**
     * Persistence parameters
     * 
     * @var array
     */
    private $params = array();
    /**
     * Is this persistence actually connect
     * 
     * @var boolean
     */
    private $isConnected = false;

    /**
     * Connect the persistence
     * 
     * @access public
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    public abstract function connect();
    
    /**
     * Name of the persistence
     * 
     * @access public
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    public abstract function getName();

    /**
     * Constructor
     * 
     * @access public
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param array $params
     * @param core_persistence_driver $driver
     */
    public function __construct($params = array(), core_persistence_driver $driver){
        $this->setParams($params);
        $this->setDriver($driver);

        if (isset($params['connection'])) {
            $this->setConnection($params['connection']);
            $this->setConnected(true);
        }
    }

    /**
     * Retrieve persistence's driver
     * 
     * @access public
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @return core_persistence_Driver
     */
    public function getDriver(){
        return $this->driver;
    }

    /**
     * Retrieve persistence'c connection
     * 
     * @access public
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Set the persistence
     * 
     * @access protected
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param core_persistence_Driver $driver
     */
    protected  function setDriver(core_persistence_Driver $driver){
        $this->driver=$driver;
    }

    /**
     * Set the connection
     * 
     * @access protected
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param unknown $connection
     */
    protected function setConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Retrieve persistence's parameters
     * 
     * @access protected
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @return multitype:
     */
    protected function getParams(){
        return $this->params;
    }

    /**
     * set persistence's parameters
     * 
     * @access protected
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param unknown $params
     */
    protected function setParams($params){
        $this->params = $params;
    }

    /**
     * change actual persistence status to connected
     * 
     * @access public
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param unknown $isConnected
     */
    protected function setConnected($isConnected)
    {
        $this->isConnected = $isConnected;
    }

    /**
     * retrieve persistence status
     * 
     * @access protected
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @return boolean
     */
    protected function isConnected()
    {
        return $this->isConnected;
    }
}

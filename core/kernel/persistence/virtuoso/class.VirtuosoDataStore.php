<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 07.07.2011, 17:23:09 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_virtuoso
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--3a4c22:13104bcfe8d:-8000:0000000000002303-includes begin
// section 127-0-1-1--3a4c22:13104bcfe8d:-8000:0000000000002303-includes end

/* user defined constants */
// section 127-0-1-1--3a4c22:13104bcfe8d:-8000:0000000000002303-constants begin

define(VIRTUOSO_ODBC_NAME, 'VOS');
define(VIRTUOSO_LOGINE, 'dba');
define(VIRTUOSO_PASSWORD, 'tao');

// section 127-0-1-1--3a4c22:13104bcfe8d:-8000:0000000000002303-constants end

/**
 * Short description of class core_kernel_persistence_virtuoso_VirtuosoDataStore
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_virtuoso
 */
class core_kernel_persistence_virtuoso_VirtuosoDataStore
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute dbConnector
     *
     * @access private
     * @var void
     */
    private $dbConnector = null;

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var VirtuosoDataStore
     */
    private static $instance = null;

    /**
     * Short description of attribute currentGraph
     *
     * @access private
     * @var string
     */
    private $currentGraph = '';

    // --- OPERATIONS ---

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return core_kernel_persistence_virtuoso_VirtuosoDataStore
     */
    public function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:000000000000230A begin
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c();

        }
        $returnValue = self::$instance;
        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:000000000000230A end

        return $returnValue;
    }

    /**
     * Short description of method execQuery
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string query
     * @return void
     */
    public function execQuery($query)
    {
        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:000000000000230C begin
        
        if(!$this->dbConnector){
                throw new core_kernel_persistence_virtuoso_Exception("[VIRTUOSO ERROR] Virtuoso is not connected");
        }

        $res = odbc_exec($this->dbConnector, 'sparql ' . $query);

        $err = odbc_errormsg($this->dbConnector);
        if(!empty($err)){
                throw new core_kernel_persistence_virtuoso_Exception("[VIRTUOSO ERROR] $err");
        }

        return $res;
        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:000000000000230C end
    }

    /**
     * Short description of method __construct
     *
     * @access private
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    private function __construct()
    {
        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:000000000000230F begin
        
        $this->dbConnector = odbc_connect(VIRTUOSO_ODBC_NAME, VIRTUOSO_LOGIN, VIRTUOSO_PASSWORD);
	if(!$this->dbConnector){
                throw new core_kernel_persistence_virtuoso_Exception('[VIRTUOSO ERROR] Enable to connect to '.VIRTUOSO_ODBC_NAME. ': '.VIRTUOSO_LOGIN.'/'.VIRTUOSO_PASSWORD);
        }
        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:000000000000230F end
    }

    /**
     * Short description of method __destruct
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function __destruct()
    {
        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:0000000000002311 begin
        if(!is_null($this->dbConnector)){
    		odbc_close($this->dbConnector);
    	}
        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:0000000000002311 end
    }

    /**
     * Short description of method getCurrentGraph
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return string
     */
    public function getCurrentGraph()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:000000000000231A begin
        
        $returnValue = 'localNSgraph';//since in practice, only local data could be changed...
        
        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:000000000000231A end

        return (string) $returnValue;
    }

} /* end of class core_kernel_persistence_virtuoso_VirtuosoDataStore */

?>
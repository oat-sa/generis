<?php

error_reporting(E_ALL);

/**
 * Simple utility class that allow you to wrap the database connector.
 * You can retrieve an instance evreywhere using the singleton.
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1--31--647ec317:119141cd117:-8000:00000000000008DB-includes begin
require_once('includes/adodb5/adodb.inc.php');
// section 10-13-1--31--647ec317:119141cd117:-8000:00000000000008DB-includes end

/* user defined constants */
// section 10-13-1--31--647ec317:119141cd117:-8000:00000000000008DB-constants begin
// section 10-13-1--31--647ec317:119141cd117:-8000:00000000000008DB-constants end

/**
 * Simple utility class that allow you to wrap the database connector.
 * You can retrieve an instance evreywhere using the singleton.
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 */
class core_kernel_classes_DbWrapper
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute dbConnector
     *
     * @access public
     * @var AdoDB
     */
    public $dbConnector = null;

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var DbWrapper
     */
    private static $instance = null;

    // --- OPERATIONS ---

    /**
     * Entry point.
     * Enables you to retrieve staticly the DbWrapper instance
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string database
     * @return core_kernel_classes_DbWrapper
     */
    public static function singleton($database = "")
    {
        $returnValue = null;

        // section 10-13-1--31--647ec317:119141cd117:-8000:00000000000008F3 begin
		if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c($database);

        }
        $returnValue = self::$instance;

        // section 10-13-1--31--647ec317:119141cd117:-8000:00000000000008F3 end

        return $returnValue;
    }

    /**
     * Initialize the storage engine connection
     *
     * @access private
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string database
     * @return void
     */
    private function __construct($database)
    {
        // section 10-13-1--31-6e1b148f:1192d5c62ab:-8000:00000000000009A7 begin
		$this->dbConnector = NewADOConnection(SGBD_DRIVER);
		$this->dbConnector->Connect(DATABASE_URL, DATABASE_LOGIN, DATABASE_PASS, $database);
        $this->dbConnector->debug = false;
	
        if(defined('GENERIS_CACHE_PATH')){
        	$ADODB_CACHE_DIR = GENERIS_CACHE_PATH;
        	$this->dbConnector->cacheSecs = 3600*24;	//24 hours cache for ado db
        }
        
		//to manage utf8
        $this->dbConnector->Execute('SET NAMES \'UTF8\';');
        
        //specific code to execute for each sgbd
    	switch (SGBD_DRIVER){
        	case 'mysql':
        		// enable ansi quotes to escape fieldname like it is mentionned in the standard with double qotes
       			$this->dbConnector->Execute('SET SESSION SQL_MODE=\'ANSI_QUOTES\';');
       			break;
        }
        
        if($this->dbConnector->errorNo() !== 0){
			throw new Exception("Database error ".$this->dbConnector->errorMsg());
		}

        // section 10-13-1--31-6e1b148f:1192d5c62ab:-8000:00000000000009A7 end
    }

    /**
     * Used to close the database connection on destruction
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return mixed
     */
    public function __destruct()
    {
        // section 127-0-1-1-35ccf597:12f4de46ade:-8000:0000000000001423 begin
        
    	if(!is_null($this->dbConnector)){
    		$this->dbConnector->close();
    	}
    	
        // section 127-0-1-1-35ccf597:12f4de46ade:-8000:0000000000001423 end
    }

    /**
     * Short description of method __clone
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_classes_DbWrapper
     */
    public function __clone()
    {
        $returnValue = null;

        // section 127-0-0-1-71ce5466:11938f47d30:-8000:0000000000000AA2 begin
		trigger_error('You cannot clone a singleton', E_USER_ERROR);
        // section 127-0-0-1-71ce5466:11938f47d30:-8000:0000000000000AA2 end

        return $returnValue;
    }

    /**
     * Execute an SQL query.
     * The second argument is used only for prepared like statements
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string sqlQuery
     * @param  array parameters query parameters in the order the ? are found
     * @return common_Object
     */
    public function execSql($sqlQuery, $parameters = false)
    {
        $returnValue = null;

        // section 10-13-1--31--7714f845:11984dc9fef:-8000:0000000000000B1F begin
        $returnValue = $this->dbConnector->Execute($sqlQuery, $parameters);	
        // section 10-13-1--31--7714f845:11984dc9fef:-8000:0000000000000B1F end

        return $returnValue;
    }

    /**
     * Short description of method getSetting
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string name
     * @return string
     */
    public function getSetting($name)
    {
        $returnValue = (string) '';

        // section -87--2--3--76--148ee98a:12452773959:-8000:000000000000235A begin
		
		throw new Exception(__CLASS__ .'::'.__METHOD__. ' is deprecated');
		
        // section -87--2--3--76--148ee98a:12452773959:-8000:000000000000235A end

        return (string) $returnValue;
    }

    /**
     * Short description of method getLastInsertId
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return Integer
     */
    public function getLastInsertId()
    {
        $returnValue = null;

        // section 127-0-1-1--642cfc1e:13160cfbaf5:-8000:0000000000001628 begin
        
        $returnValue = $this->dbConnector->Insert_ID();
        
        // section 127-0-1-1--642cfc1e:13160cfbaf5:-8000:0000000000001628 end

        return $returnValue;
    }

} /* end of class core_kernel_classes_DbWrapper */

?>
<?php

error_reporting(E_ALL);

/**
 * Simple utility class that allow you to wrap the database connector.
 * You can retrieve an instance evreywhere using the singleton.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 */
class core_kernel_classes_DbWrapper
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var DbWrapper
     */
    private static $instance = null;

    /**
     * An established PDO connection object.
     *
     * @access public
     * @var PDO
     */
    public $dbConnector = null;

    /**
     * Short description of attribute nrQueries
     *
     * @access private
     * @var int
     */
    private $nrQueries = 0;

    /**
     * Short description of attribute preparedExec
     *
     * @access public
     * @var boolean
     */
    public $preparedExec = false;

    /**
     * Short description of attribute lastPreparedExecStatement
     *
     * @access public
     * @var PDOStatement
     */
    public $lastPreparedExecStatement = null;

    /**
     * Short description of attribute statements
     *
     * @access public
     * @var array
     */
    public $statements = array();

    // --- OPERATIONS ---

    /**
     * Entry point.
     * Enables you to retrieve staticly the DbWrapper instance
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_DbWrapper
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 10-13-1--31--647ec317:119141cd117:-8000:00000000000008F3 begin
		if (!isset(self::$instance)) {
            self::$instance = new self();

        }
        $returnValue = self::$instance;

        // section 10-13-1--31--647ec317:119141cd117:-8000:00000000000008F3 end

        return $returnValue;
    }

    /**
     * Initialize the storage engine connection
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    private function __construct()
    {
        // section 10-13-1--31-6e1b148f:1192d5c62ab:-8000:00000000000009A7 begin
        $connLimit = 3; // Max connection attempts.
        $counter = 0; // Connection attemps counter.
        
        while (true){
	        $driver = strtolower(SGBD_DRIVER);
	        $dsn = $driver . ':dbname=' . DATABASE_NAME . ';host=' . DATABASE_URL . ';charset=utf8';
	        $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_BOTH,
	        				 PDO::ATTR_PERSISTENT => false,
	        				 PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
	        				 PDO::ATTR_EMULATE_PREPARES => false);
	        
	       
	        try{
	        	$this->dbConnector = @new PDO($dsn, DATABASE_LOGIN, DATABASE_PASS, $options);
	    	
		        //specific code to execute for each sgbd
		    	switch ($driver){
		        	case 'mysql':
		        		// enable ansi quotes to escape fieldname like it is mentionned in the standard with double qotes
		       			$this->exec('SET SESSION SQL_MODE=\'ANSI_QUOTES\';');
		       			break;
		        }
				
		        // We are connected. Get out of the loop.
		        break;
	        }
	        catch (PDOException $e){
	        	$this->dbConnector = null;
	        	$counter++;
	        	
	        	if ($counter == $connLimit){
	        		// Connection attempts exceeded.
	        		throw $e;
	        	}
	        }
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
    		$this->dbConnector = null;
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
     * Short description of method getSetting
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @deprecated
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
     * Returns the ammount of queries executed sofar
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return int
     */
    public function getNrOfQueries()
    {
        $returnValue = (int) 0;

        // section 127-0-1-1-4275bef6:136722a6279:-8000:00000000000019AA begin
        $returnValue = $this->nrQueries;
        // section 127-0-1-1-4275bef6:136722a6279:-8000:00000000000019AA end

        return (int) $returnValue;
    }

    /**
     * Short description of method query
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string statement
     * @param  array params
     * @return PDOStatement
     */
    public function query($statement, $params = array())
    {
        $returnValue = null;

        // section 10-13-1-85--1639374a:13a883294da:-8000:0000000000001B41 begin
        $this->preparedExec = false;
        
        if (count($params) > 0){
        	$sth = $this->dbConnector->prepare($statement);
        	$sth->execute($params);
        }
        else{
        	$sth = $this->dbConnector->query($statement);
        }
        
        $returnValue = $sth;
        $this->nrQueries++;
        // section 10-13-1-85--1639374a:13a883294da:-8000:0000000000001B41 end

        return $returnValue;
    }

    /**
     * Short description of method exec
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string statement
     * @param  array params
     * @return int
     */
    public function exec($statement, $params = array())
    {
        $returnValue = (int) 0;

        // section 10-13-1-85--1639374a:13a883294da:-8000:0000000000001B50 begin
        if (count($params) > 0){
        	$sth = $this->dbConnector->prepare($statement);
        	$this->preparedExec = true;
        	$this->lastPreparedExecStatement = $sth;
        	$sth->execute($params);
        	$returnValue = $sth->rowCount();
        }
        else{
        	$this->preparedExec = false;
        	$returnValue = $this->dbConnector->exec($statement);
        }
        
        $this->nrQueries++;
        // section 10-13-1-85--1639374a:13a883294da:-8000:0000000000001B50 end

        return (int) $returnValue;
    }

    /**
     * Short description of method prepare
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string statement
     * @return PDOStatement
     */
    public function prepare($statement)
    {
        $returnValue = null;

        // section 10-13-1-85--1639374a:13a883294da:-8000:0000000000001B5B begin
        $this->preparedExec = false;
        $returnValue = $this->getStatement($statement);
        $this->nrQueries++;
        // section 10-13-1-85--1639374a:13a883294da:-8000:0000000000001B5B end

        return $returnValue;
    }

    /**
     * Short description of method errorCode
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function errorCode()
    {
        $returnValue = (string) '';

        // section 10-13-1-85-8c38d91:13a93112c47:-8000:0000000000001B57 begin
    	if ($this->preparedExec == false){
    		$returnValue = $this->dbConnector->errorCode();
    	}
    	else{
    		$returnValue = $this->lastPreparedExecStatement->errorCode();
    	}
        // section 10-13-1-85-8c38d91:13a93112c47:-8000:0000000000001B57 end

        return (string) $returnValue;
    }

    /**
     * Short description of method errorMessage
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function errorMessage()
    {
        $returnValue = (string) '';

        // section 10-13-1-85-8c38d91:13a93112c47:-8000:0000000000001B59 begin
        if ($this->preparedExec == false){
    		$info = $this->dbConnector->errorInfo();
    	}
    	else{
    		$info = $this->lastPreparedExecStatement->errorInfo();
    	}
    	
    	if (!empty($info[2])){
    		$returnValue = $info[2];
    	}
    	else if (!empty($info[1])){
    		$returnValue = 'Driver error: ' . $info[1];
    	}
    	else if (!empty($info[0])){
    		$returnValue = 'SQLSTATE: ' . $info[0];
    	}
    	
    	$returnValue = 'No error message to display.';
        // section 10-13-1-85-8c38d91:13a93112c47:-8000:0000000000001B59 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getTables
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getTables()
    {
        $returnValue = array();

        // section 10-13-1-85-8c38d91:13a93112c47:-8000:0000000000001B5B begin
        $result = $this->query('SHOW TABLES');
    	$returnValue = array();
    	while ($row = $result->fetch(PDO::FETCH_NUM)){
    		$returnValue[] = $row[0];
    	}
        // section 10-13-1-85-8c38d91:13a93112c47:-8000:0000000000001B5B end

        return (array) $returnValue;
    }

    /**
     * Short description of method getStatement
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string statement
     * @return PDOStatement
     */
    protected function getStatement($statement)
    {
        $returnValue = null;

        // section 10-13-1-85-8c38d91:13a93112c47:-8000:0000000000001B5D begin
        $key = $this->getStatementKey($statement);
    	$sth = null;
    	
    	if (!empty($this->statements[$key])){
    		$sth = $this->statements[$key];
    	}
    	else{
    		$sth = $this->dbConnector->prepare($statement);
    		$this->statements[$key] = $sth;
    	}
    	
    	$returnValue = $sth;
        // section 10-13-1-85-8c38d91:13a93112c47:-8000:0000000000001B5D end

        return $returnValue;
    }

    /**
     * Short description of method getStatementKey
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string statement
     * @return string
     */
    public function getStatementKey($statement)
    {
        $returnValue = (string) '';

        // section 10-13-1-85-8c38d91:13a93112c47:-8000:0000000000001B60 begin
        $returnValue = hash('crc32b', $statement);
        // section 10-13-1-85-8c38d91:13a93112c47:-8000:0000000000001B60 end

        return (string) $returnValue;
    }

} /* end of class core_kernel_classes_DbWrapper */

?>
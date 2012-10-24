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
    
    protected $preparedExec = false;
    
    protected $lastPreparedExecStatement = null;
    
    protected $statements = array();

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
     * Execute an SQL query.
     * The second argument is used only for prepared like statements
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string sqlQuery
     * @param  array parameters query parameters in the order the ? are found
     * @return common_Object
     */
    /* --- TO DELETE ---public function execSql($sqlQuery, $parameters = false)
    {
        $returnValue = null;

        // section 10-13-1--31--7714f845:11984dc9fef:-8000:0000000000000B1F begin
        $returnValue = $this->dbConnector->Execute($sqlQuery, $parameters);	
        $this->nrQueries++;
        // section 10-13-1--31--7714f845:11984dc9fef:-8000:0000000000000B1F end

        return $returnValue;
    }*/

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
     * Short description of method getAffectedRows
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return Integer
     */
    /* --- TO DELETE --- public function getAffectedRows()
    {
        $returnValue = null;

        // section 127-0-1-1-4f08ff91:131764e4b1f:-8000:000000000000163A begin
        
        if (!empty($this->lastStatement)){
        	$returnValue = $this->;
        }
        else{
        	$returnValue = 0;
        }
        
        // section 127-0-1-1-4f08ff91:131764e4b1f:-8000:000000000000163A end

        return $returnValue;
    }*/

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
        return $this->getStatement($statement);
        // section 10-13-1-85--1639374a:13a883294da:-8000:0000000000001B5B end

        return $returnValue;
    }
    
    public function errorCode(){
    	if ($this->preparedExec == false){
    		return $this->dbConnector->errorCode();
    	}
    	else{
    		return $this->lastPreparedExecStatement->errorCode();
    	}
    }
    
    public function errorMessage(){
    	if ($this->preparedExec == false){
    		$info = $this->dbConnector->errorInfo();
    	}
    	else{
    		$info = $this->lastPreparedExecStatement->errorInfo();
    	}
    	
    	if (!empty($info[2])){
    		return $info[2];
    	}
    	else if (!empty($info[1])){
    		return 'Driver error: ' . $info[1];
    	}
    	else if (!empty($info[0])){
    		return 'SQLSTATE: ' . $info[0];
    	}
    	
    	return 'No error message to display.';
    }
    
    public function getTables(){
    	$result = $this->query('SHOW TABLES');
    	$returnValue = array();
    	while ($row = $result->fetch(PDO::FETCH_NUM)){
    		$returnValue[] = $row[0];
    	}
    }
    
    protected function getStatement($statement){
    	$key = $this->getStatementKey($statement);
    	$sth = null;
    	
    	if (!empty($this->statements[$key])){
    		$sth = $this->statements[$key];
    	}
    	else{
    		$sth = $this->dbConnector->prepare($statement);
    		$this->statements[$key] = $sth;
    	}
    	
    	return $sth;
    }
    
    protected function getStatementKey($statement){
    	$key = hash('crc32b', $statement);
    	return $key;
    }

} /* end of class core_kernel_classes_DbWrapper */

?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 12.07.2011, 11:41:56 with ArgoUML PHP module 
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
     * @var resource
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
     * @param  string outputFormat
     * @return resource
     */
    public function execQuery($query, $outputFormat = 'Array')
    {
        $returnValue = null;

        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:000000000000230C begin
        
        if(!$this->dbConnector){
                throw new core_kernel_persistence_virtuoso_Exception("[VIRTUOSO ERROR] Virtuoso is not connected");
        }

        $result = odbc_exec($this->dbConnector, 'sparql ' . $query);
        if(strtolower($outputFormat) == 'array'){
                $returnValue = $this->resultToArray($result);
        }else if(strtolower ($outputFormat) == 'boolean'){
                $returnValue = $this->resultToBoolean($result);
        }
        
        
        $error = odbc_errormsg($this->dbConnector);
        if(!empty($error)){
                throw new core_kernel_persistence_virtuoso_Exception("[VIRTUOSO ERROR] $error");
        }

        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:000000000000230C end

        return $returnValue;
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
        
        $returnValue = VIRTUOSO_GRAPH_TAO;//all data store in a single graph
        
        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:000000000000231A end

        return (string) $returnValue;
    }

    /**
     * Short description of method resultToArray
     *
     * @access private
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  result
     * @return array
     */
    private function resultToArray($result)
    {
        $returnValue = array();

        // section 127-0-1-1--18467de:13108b1b06a:-8000:00000000000015D7 begin
        
        if(!empty($result)){
                $row = 0;
                $count = odbc_num_fields($result);
                while(odbc_fetch_row($result)){
                        $returnValue[$row] = array();
                        for($col=0; $col < $count; $col++){
                                $returnValue[$row][$col] = odbc_result($result, $col+1);
                        }
                        $row ++;
                }
        }
        // section 127-0-1-1--18467de:13108b1b06a:-8000:00000000000015D7 end

        return (array) $returnValue;
    }

    /**
     * Short description of method resultToBoolean
     *
     * @access private
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  result
     * @return boolean
     */
    private function resultToBoolean($result)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--18467de:13108b1b06a:-8000:00000000000015DA begin
        if(!empty($result)){
                $resultArray = $this->resultToArray($result);
                if(isset($resultArray[0]) && isset($resultArray[0][0])){
                        if(preg_match('/done$/', $resultArray[0][0])){
                                $returnValue = true;
                        }
                }
                
        }
        // section 127-0-1-1--18467de:13108b1b06a:-8000:00000000000015DA end

        return (bool) $returnValue;
    }

    /**
     * Short description of method filterLanguageValue
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string lg
     * @return string
     */
    public function filterLanguageValue($lg)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-732c983d:1311db156b2:-8000:00000000000015E7 begin
        $lg = trim($lg);
        if(preg_match("/[A-Z_]{2,5}$/",$lg)){
                $returnValue .= '@'.strtolower($lg);
        }
        // section 127-0-1-1-732c983d:1311db156b2:-8000:00000000000015E7 end

        return (string) $returnValue;
    }

    /**
     * Short description of method filterObjectValue
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string object
     * @param  string lg
     * @return string
     */
    public function filterObjectValue($object, $lg = '')
    {
        $returnValue = (string) '';

        // section 127-0-1-1-732c983d:1311db156b2:-8000:00000000000015EA begin
        if(!empty($lg)){
                $lg = $this->filterLanguageValue($lg);
        }
        
        if(!empty($lg)){
                $returnValue = '"' . $object . '"@'.$lg;
        }else{
                if (common_Utils::isUri($object)) {
                        $returnValue = $object;//do not alter it
                } else {
                        $returnValue = '"' . $object . '"';
                }
        }
        
        
        // section 127-0-1-1-732c983d:1311db156b2:-8000:00000000000015EA end

        return (string) $returnValue;
    }

} /* end of class core_kernel_persistence_virtuoso_VirtuosoDataStore */

?>
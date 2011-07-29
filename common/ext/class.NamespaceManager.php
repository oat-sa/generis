<?php

error_reporting(E_ALL);

/**
 * Enables you to manage the module namespaces
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package common
 * @subpackage ext
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_ext_Namespace
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('common/ext/class.Namespace.php');

/* user defined includes */
// section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001589-includes begin
// section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001589-includes end

/* user defined constants */
// section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001589-constants begin
// section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001589-constants end

/**
 * Enables you to manage the module namespaces
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package common
 * @subpackage ext
 */
class common_ext_NamespaceManager
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * the single instance of the NamespaceManager
     *
     * @access private
     * @var NamespaceManager
     */
    private static $instance = null;

    /**
     * Stock the list of all module's namespace, to be retrieved more
     *
     * @access protected
     * @var array
     */
    protected $namespaces = array();

    // --- OPERATIONS ---

    /**
     * Private constructor to force the use of the singleton
     *
     * @access private
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return mixed
     */
    private function __construct()
    {
        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001591 begin
        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001591 end
    }

    /**
     * Main entry point to retrieve the unique NamespaceManager instance
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return common_ext_NamespaceManager
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001593 begin
        
        if(is_null(self::$instance)){
        	$class = __CLASS__;				//used in case of subclassing
        	self::$instance = new $class();
        }
        $returnValue = self::$instance;
        
        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001593 end

        return $returnValue;
    }

    /**
     * Get the list of all module's namespaces
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array
     */
    public function getAllNamespaces()
    {
        $returnValue = array();

        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001595 begin
        
        if(count($this->namespaces) == 0){
        	$db = core_kernel_classes_DbWrapper::singleton(DATABASE_NAME);
        	$query = 'SELECT "modelID", "baseURI" FROM "models"';
			$result = $db->execSql($query);
			while (!$result-> EOF){
				$id 	= $result->fields['modelID'];
				$uri 	= $result->fields['baseURI'];
				$this->namespaces[$id] = $uri;
				$result->MoveNext();
			}
        }
        
        foreach($this->namespaces as $id => $uri){
        	$returnValue[$uri] = new common_ext_Namespace($id, $uri);
        }
        
        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001595 end

        return (array) $returnValue;
    }

    /**
     * Conveniance method to retrieve the local Namespace
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return common_ext_Namespace
     */
    public function getLocalNamespace()
    {
        $returnValue = null;

        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001597 begin
        
        $session = core_kernel_classes_Session::singleton();
		$localModel = $session->getNameSpace();
		if(!preg_match("/#$/", $localModel)){
			$localModel.= '#';
		}
    	if(count($this->namespaces) == 0){
        	$this->getAllNamespaces();	//load the namespaces attribute 
        }
        if( ($modeId = array_search($localModel, $this->namespaces, true)) !== false ){
        	$returnValue = new common_ext_Namespace($modeId, $this->namespaces[$modeId]);
        }
        
        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001597 end

        return $returnValue;
    }

    /**
     * Get a namesapce identified by the modelId or modelUri
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  modelID
     * @return common_ext_Namespace
     */
    public function getNamespace($modelID)
    {
        $returnValue = null;

        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001599 begin
    
        if(count($this->namespaces) == 0){
        	$this->getAllNamespaces();	//load the namespaces attribute 
       	}
        
        //get modelId from modelUri
        if(is_string($modelID)){
        	$modelID = array_search($modelID, $this->namespaces);
        }
        
    	//get namespace from modelId
    	if(is_int($modelID)){
        	if(isset($this->namespaces[$modelID])){
        		$returnValue = new common_ext_Namespace($modelID, $this->namespaces[$modelID]);
        	}
        }
        
        // section 127-0-1-1-1cf6e8c2:12dbd7e3b2a:-8000:0000000000001599 end

        return $returnValue;
    }

} /* end of class common_ext_NamespaceManager */

?>
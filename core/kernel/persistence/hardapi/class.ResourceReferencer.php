<?php

error_reporting(E_ALL);

/**
 * This class helps you to manage meta references to resources
 * (classes and instances). 
 * You can define the caching method by resource kind.
 * By default, the classes reference is cached in memory
 * and the instances are not cached
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardapi
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000162A-includes begin
// section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000162A-includes end

/* user defined constants */
// section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000162A-constants begin
// section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000162A-constants end

/**
 * This class helps you to manage meta references to resources
 * (classes and instances). 
 * You can define the caching method by resource kind.
 * By default, the classes reference is cached in memory
 * and the instances are not cached
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardapi
 */
class core_kernel_persistence_hardapi_ResourceReferencer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * single self instance
     *
     * @access private
     * @var ResourceReferencer
     */
    private static $_instance = null;

    /**
     * Short description of attribute CACHE_NONE
     *
     * @access public
     * @var int
     */
    const CACHE_NONE = 0;

    /**
     * Short description of attribute CACHE_MEMORY
     *
     * @access public
     * @var int
     */
    const CACHE_MEMORY = 1;

    /**
     * Short description of attribute classMode
     *
     * @access protected
     * @var int
     */
    protected $classMode = 0;

    /**
     * Short description of attribute instanceMode
     *
     * @access protected
     * @var int
     */
    protected $instanceMode = 0;

    /**
     * Short description of attribute _classes
     *
     * @access private
     * @var array
     */
    private static $_classes = array();

    /**
     * Short description of attribute _resources
     *
     * @access private
     * @var array
     */
    private static $_resources = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access private
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return mixed
     */
    private function __construct()
    {
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001633 begin
        
    	//default cache values
		$this->classMode 	= self::CACHE_MEMORY;
		$this->instanceMode = self::CACHE_NONE;
    	
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001633 end
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_persistence_hardapi_ResourceReferencer
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001635 begin
        
        if (is_null(self::$_instance)){
			$class = __CLASS__;
        	self::$_instance = new $class();
        }
        $returnValue = self::$_instance;
        
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001635 end

        return $returnValue;
    }

    /**
     * Short description of method setClassCache
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  int mode
     * @return mixed
     */
    public function setClassCache($mode)
    {
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000164C begin
        
    	if($mode == self::CACHE_NONE || $mode == self::CACHE_MEMORY){
			$this->classMode = $mode;
		}
    	
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000164C end
    }

    /**
     * Short description of method setInstanceCache
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  int mode
     * @return mixed
     */
    public function setInstanceCache($mode)
    {
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000164F begin
        
    	if($mode == self::CACHE_NONE || $mode == self::CACHE_MEMORY){
			$this->instanceMode = $mode;
		}
    	
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000164F end
    }

    /**
     * Short description of method loadClasses
     *
     * @access private
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  boolean force
     * @return mixed
     */
    private function loadClasses($force = false)
    {
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001666 begin
        
    	if(count($_classes) == 0 || $force){
			$dbWrapper = core_kernel_classes_DbWrapper::singleton();
			$result = $dbWrapper->execSql("SELECT uri, table FROM class_to_table");
			 while ($row = $result->FetchRow()) {
	        	self::$_classes[$row['uri']] = $row['table'];
	        }
		}
    	
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001666 end
    }

    /**
     * Short description of method isClassReferenced
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Class class
     * @return boolean
     */
    public function isClassReferenced( core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001652 begin
        
        if(!is_null($class)){
			switch($this->classMode){
				
				case self::CACHE_NONE:
					$dbWrapper = core_kernel_classes_DbWrapper::singleton();
					$result = $dbWrapper->execSql("SELECT id FROM class_to_table WHERE uri = ?", array($class->uriResource));
					if($result->RecordCount() > 0){
						$returnValue = true;
					}
					break;
					
				case self::CACHE_MEMORY:
					
					$this->loadResources();
					$returnValue = array_key_exists($class->uriResource, self::$_classes);
					break;
			}
		}
        
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001652 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method referenceClass
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Class class
     * @return boolean
     */
    public function referenceClass( core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001655 begin
        
        if(!$this->isClassReferenced($class)){
			
			$tableName = core_kernel_persistence_hardapi_Utils::getShortName($class);
			
			$dbWrapper = core_kernel_classes_DbWrapper::singleton();
			
			$query = "INSERT INTO class_to_table (uri, `table`) VALUES (?,?)";
			$result = $dbWrapper->execSql($query, array(
				$class->uriResource, 
				$tableName
			));
			if($result !== false){
				$returnValue = true;
				if($this->classMode == self::CACHE_MEMORY){
					self::$_classes[$class->uriResource] = $tableName;
				}
			}
		}
        
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001655 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method unReferenceClass
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Class class
     * @return boolean
     */
    public function unReferenceClass( core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001658 begin
        
        if($this->isClassReferenced($class)){
			
			$tableName = core_kernel_persistence_hardapi_Utils::getShortName($class);
			
			$dbWrapper = core_kernel_classes_DbWrapper::singleton();
			
//			$query = "DELETE class_to_table.*, resource_has_class.*, resource_to_table.* FROM class_to_table 
//								INNER JOIN resource_has_class ON resource_has_class.class_id = class_to_table.id
//								INNER JOIN resource_to_table ON resource_has_class.resource_id = resource_to_table.id
//								WHERE class_to_table.`table` = '{$tableName}' OR resource_to_table.`table` = '{$tableName}'";
		
			$query = "DELETE class_to_table.*, resource_to_table.* FROM class_to_table 
								INNER JOIN resource_to_table ON class_to_table.id = resource_to_table.id
								WHERE class_to_table.`table` = '{$tableName}' OR resource_to_table.`table` = '{$tableName}'";
			
			$result = $dbWrapper->execSql($query);
			if($result !== false){
				$returnValue = true;
				if($this->classMode == self::CACHE_MEMORY){
					unset(self::$_classes[$class->uriResource]);
				}
			}
		}
        
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001658 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method loadResources
     *
     * @access private
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  boolean force
     * @return mixed
     */
    private function loadResources($force = false)
    {
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000166E begin
        
    	if(count(self::$_resources) == 0 || $force){
			$dbWrapper = core_kernel_classes_DbWrapper::singleton();
			$result = $dbWrapper->execSql("SELECT uri, `table` FROM resource_to_table");
			while ($row = $result->FetchRow()) {
	        	self::$_resources[] = $row;
	        }
		}
    	
    	
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000166E end
    }

    /**
     * Short description of method isResourceReferenced
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isResourceReferenced( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000165B begin
        
        if(!is_null($resource)){
			switch($this->instanceMode){
				
				case self::CACHE_NONE:
					$dbWrapper = core_kernel_classes_DbWrapper::singleton();
					$result = $dbWrapper->execSql("SELECT id FROM resource_to_table WHERE uri = ?", array($resource->uriResource));
					if($result->RecordCount() > 0){
						$returnValue = true;
					}
					break;
					
				case self::CACHE_MEMORY:
					
					$this->loadResources();
					foreach( self::$_resources as $res){
						if($res['uri'] == $resource->uriResource){
							$returnValue = true;
							break;
						}
					}
					break;
			}
		}
        
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000165B end

        return (bool) $returnValue;
    }

    /**
     * Short description of method referenceResource
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  array types
     * @param  boolean referenceClassLink
     * @return boolean
     */
    public function referenceResource( core_kernel_classes_Resource $resource, $types = null, $referenceClassLink = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000165E begin
        $types = isset($types) ? $types : $resource->getType();
        
        if(!$this->isResourceReferenced($resource)){
			$dbWrapper = core_kernel_classes_DbWrapper::singleton();
			
			$rows = array();
			foreach($types as $class){
				$tableName = core_kernel_persistence_hardapi_Utils::getShortName($class);
				
				$query = "INSERT INTO resource_to_table (uri, `table`) VALUES (?,?)";
				$insertResult = $dbWrapper->execSql($query, array($resource->uriResource, $tableName));
				if($dbWrapper->dbConnector->errorNo() !== 0){
					throw new core_kernel_persistence_hardapi_Exception("Unable to reference the resource : {$resource->uriResource} / {$table} : " .$dbWrapper->dbConnector->errorMsg());
				}
				if($referenceClassLink && $insertResult !== false){
					$query = "SELECT * FROM resource_to_table WHERE uri = ? AND `table` = ?";
					$result = $dbWrapper->execSql($query, array($resource->uriResource, $tableName));
					while($row = $result->fetchRow()){
						$row['class'] = $class->uriResource;
						$rows[] = $row;
					}
				}
				$returnValue = (bool) $insertResult;
			}
			if($referenceClassLink){
				foreach($rows as $row){
					if($this->isClassReferenced(new core_kernel_classes_Class($row['class']))){
						
						$query = "SELECT id FROM class_to_table WHERE uri = ? AND `table` = ?";
						$result = $dbWrapper->execSql($query, array($resource->uriResource, $tableName));
						if($result->RecordCount() == 1){
							while($classRow = $result->fetchRow()){
								$query = "INSERT INTO resource_has_class (resource_id, class_id) VALUES (?,?)";
								$dbWrapper->execSql($query, array($row['id'],$classRow['id']));
							}
						}
					}
				}
			}
		}
		if($returnValue && $this->instanceMode == self::CACHE_MEMORY){
			foreach($rows as $row){
				self::$_resources[] = array(
					'uri' 	=> $row['uri'],
					'table'	=> $row['table']
				);
			}
		}
		
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:000000000000165E end

        return (bool) $returnValue;
    }

    /**
     * Short description of method unReferenceResource
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function unReferenceResource( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001661 begin
        
        if($this->isResourceReferenced($resource)){
			$dbWrapper = core_kernel_classes_DbWrapper::singleton();
			
//			$query = "DELETE resource_to_table.*, resource_has_class.* FROM resource_to_table 
//						INNER JOIN resource_has_class ON resource_has_class.resource_id = resource_to_table.id
//						WHERE resource_to_table.uri = ?";
		$query = "DELETE resource_to_table.* FROM resource_to_table 
						WHERE resource_to_table.uri = ?";
			$result = $dbWrapper->execSql($query, array($resource->uriResource));
			if($result !== false){
				$returnValue = true;
				if($this->instanceMode == self::CACHE_MEMORY ){
					foreach( self::$_resources as $key =>  $res){
						if($res['uri'] == $resource->uriResource){
							self::$_resources[$key];
						}
					}
				}
			}
		}
        
        // section 127-0-1-1-8da8919:12f7878e80a:-8000:0000000000001661 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method resourceLocation
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return string
     */
    public function resourceLocation( core_kernel_classes_Resource $resource)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-56674b31:12fbf31d598:-8000:0000000000001505 begin
        
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        
        $query = "SELECT `table` FROM resource_to_table WHERE uri=? LIMIT 1";
    	$result = $dbWrapper->execSql($query, array ($resource->uriResource));
		if($dbWrapper->dbConnector->errorNo() !== 0){
			
			throw new core_kernel_persistence_hardapi_Exception("Unable to define where is the hardified resource: " .$dbWrapper->dbConnector->errorMsg());
		} 
		else {
			
			if (!$result->EOF){
				$returnValue = $result->fields['table'];
			}
		}
        
        // section 127-0-1-1-56674b31:12fbf31d598:-8000:0000000000001505 end

        return (string) $returnValue;
    }

} /* end of class core_kernel_persistence_hardapi_ResourceReferencer */

?>
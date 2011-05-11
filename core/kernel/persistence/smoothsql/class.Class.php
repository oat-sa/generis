<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 04.05.2011, 14:40:23 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_smoothsql
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_persistence_PersistenceImpl
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('core/kernel/persistence/class.PersistenceImpl.php');

/**
 * include core_kernel_persistence_ClassInterface
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('core/kernel/persistence/interface.ClassInterface.php');

/* user defined includes */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001399-includes begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001399-includes end

/* user defined constants */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001399-constants begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001399-constants end

/**
 * Short description of class core_kernel_persistence_smoothsql_Class
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_smoothsql
 */
class core_kernel_persistence_smoothsql_Class
    extends core_kernel_persistence_PersistenceImpl
        implements core_kernel_persistence_ClassInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access public
     * @var Resource
     */
    public static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method getSubClasses
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getSubClasses( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014EB begin

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlQuery = "select subject FROM statements where predicate = '".RDF_SUBCLASSOF."' and object = '".$resource->uriResource."'";
		$returnValue = array();
		$sqlResult = $dbWrapper->execSql($sqlQuery);
		while (!$sqlResult-> EOF){
			$subClass = new core_kernel_classes_Class($sqlResult->fields['subject']);
			$returnValue[$subClass->uriResource] = $subClass;
			if($recursive == true ){
				$plop = $subClass->getSubClasses(true);
				$returnValue = array_merge($returnValue , $plop);
			}
			$sqlResult->MoveNext();
		}

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014EB end

        return (array) $returnValue;
    }

    /**
     * Short description of method isSubClassOf
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  Class parentClass
     * @return boolean
     */
    public function isSubClassOf( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $parentClass)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F0 begin

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();

		$query = "SELECT object FROM statements
					WHERE subject = ?
					AND predicate = ? AND object = ?";
		$result = $dbWrapper->execSql($query, array(
			$resource->uriResource,
			RDF_SUBCLASSOF,
			$parentClass->uriResource
		));
		while(!$result-> EOF){
			
			$returnValue =  true;
			$result->moveNext();
			break;
		}
		if(!$returnValue){
			
			foreach ($parentClass->getSubClasses(true) as $subClass){
				if ($subClass->uriResource == $resource->uriResource) {
					$returnValue =  true;
					break;
				}
			}
		}

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F0 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getParentClasses
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getParentClasses( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F5 begin
		//		$classes = core_kernel_classes_Session::singleton()->model->getindirectSuperClasses($resource->uriResource);
		$returnValue =  array();
		$sqlQuery ="SELECT object FROM statements WHERE subject = '". $resource->uriResource."' AND (predicate = '".
		RDF_SUBCLASSOF."' OR predicate = '".RDF_TYPE."')";

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlResult = $dbWrapper->execSql($sqlQuery);

		while (!$sqlResult-> EOF){

			$parentClass = new core_kernel_classes_Class($sqlResult->fields['object']);

			$returnValue[$parentClass->uriResource] = $parentClass ;
			if($recursive == true && $parentClass->uriResource != RDF_CLASS && $parentClass->uriResource != RDF_RESOURCE){
				$plop = $parentClass->getParentClasses(true);
				$returnValue = array_merge($returnValue,$plop);
			}
			$sqlResult->MoveNext();
		}

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F5 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getProperties
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getProperties( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014FA begin
		$sqlQuery = "select subject from statements where predicate = '". RDF_DOMAIN."' and object = '".$resource->uriResource."'";
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlResult = $dbWrapper->execSql($sqlQuery);

		while (!$sqlResult-> EOF){
			$property = new core_kernel_classes_Property($sqlResult->fields['subject']);
			$returnValue[$property->uriResource] = $property;
			$sqlResult->MoveNext();
		}
		if($recursive == true) {
			$parentClasses = $resource->getParentClasses(true);
			foreach ($parentClasses as $parent) {
				if($parent->uriResource != RDF_CLASS) {
					$returnValue = array_merge($returnValue,$parent->getProperties(true));
				}
			}
		}

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014FA end

        return (array) $returnValue;
    }

    /**
     * Short description of method getInstances
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @param  array params
     * @return array
     */
    public function getInstances( core_kernel_classes_Resource $resource, $recursive = false, $params = array())
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001500 begin
		
        $sqlQuery = "SELECT DISTINCT `subject` FROM `statements` 
						WHERE predicate = '".RDF_TYPE."'  
							AND object = '".$resource->uriResource."' ";
		if(isset($params['limit'])){
			$limit = intval($params['limit']);
			if($limit){
				$offset = 0;
				if(isset($params['offset'])){
					$offset = intval($params['offset']);
				}
				$sqlQuery .= "LIMIT {$offset},{$limit} ";
			}
		}
		
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlResult = $dbWrapper->execSql($sqlQuery);

		while (!$sqlResult-> EOF){

			$instance = new core_kernel_classes_Resource($sqlResult->fields['subject']);

			$returnValue[$instance->uriResource] = $instance ;

			//In case of a meta class, subclasses of instances may be returned*/
			if (($instance->uriResource!=RDF_CLASS)
			&& ($resource->uriResource == RDF_CLASS)
			&& ($instance->uriResource!=RDF_RESOURCE)) {

				$instanceClass = new core_kernel_classes_Class($instance->uriResource);
				$subClasses = $instanceClass->getSubClasses(true);

				foreach($subClasses as $subClass) {
					$returnValue[$subClass->uriResource] = $subClass;
				}
			}
			$sqlResult->MoveNext();
		}
		if($recursive == true){
			$subClasses = $resource->getSubClasses(true);
			foreach ($subClasses as $subClass){
				$returnValue = array_merge($returnValue,$subClass->getInstances(true));
			}
		}

//		if(defined('ENABLE_SUBSCRIPTION') && ENABLE_SUBSCRIPTION && $resource->uriResource != CLASS_SUBCRIPTION){
//			$typeProp = new core_kernel_classes_Property(RDF_TYPE);
//			$subcriptions = core_kernel_subscriptions_Service::singleton()->getSubscriptions(null,$typeProp,$resource);
//
//			foreach ($subcriptions as $sub){
//				$subcriptionResource = new core_kernel_classes_Resource($sub);
//				$subcriptionsInstances = core_kernel_subscriptions_Service::singleton()->getInstancesFromSubscription($subcriptionResource,$resource);
//				$returnValue = array_merge($returnValue,$subcriptionsInstances);
//			}
//		}

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001500 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setInstance
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  Resource instance
     * @return core_kernel_classes_Resource
     */
    public function setInstance( core_kernel_classes_Resource $resource,  core_kernel_classes_Resource $instance)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001506 begin
		$rdfType = new core_kernel_classes_Property(RDF_TYPE);
		$newInstance = clone $instance;	//call Resource::__clone
		$newInstance->setPropertyValue($rdfType, $resource->uriResource);

		$returnValue = $newInstance;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001506 end

        return $returnValue;
    }

    /**
     * Short description of method setSubClassOf
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  Class iClass
     * @return boolean
     */
    public function setSubClassOf( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $iClass)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000150F begin

		$subClassOf = new core_kernel_classes_Property(RDF_SUBCLASSOF);
		$returnValue = $resource->setPropertyValue($subClassOf,$iClass->uriResource);

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000150F end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setProperty
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return boolean
     */
    public function setProperty( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001512 begin

		$domain = new core_kernel_classes_Property(RDF_DOMAIN,__METHOD__);
		$instanceProperty = new core_kernel_classes_Resource($property->uriResource,__METHOD__);
		$returnValue = $instanceProperty->setPropertyValue($domain, $resource->uriResource);

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001512 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method createInstance
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Resource
     */
    public function createInstance( core_kernel_classes_Resource $resource, $label = '', $comment = '', $uri = '')
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F27 begin
        
        if($uri == ''){
			$subject = common_Utils::getNewUri();
		}
		else {
			//$uri should start with # and be well formed
			$modelUri = core_kernel_classes_Session::singleton()->getNameSpace();
			$subject = $modelUri . $uri;
		}

		$returnValue = new core_kernel_classes_Resource($subject,__METHOD__);

		$rdfType = new core_kernel_classes_Property(RDF_TYPE);
		$returnValue->setPropertyValue($rdfType, $resource->uriResource);

		if ($label != '') {
			$returnValue->setLabel($label);
		}
		if( $comment != '') {
			$returnValue->setComment($comment);
		}
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F27 end

        return $returnValue;
    }

    /**
     * Short description of method createSubClass
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @return core_kernel_classes_Class
     */
    public function createSubClass( core_kernel_classes_Resource $resource, $label = '', $comment = '')
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F32 begin
        
        $class = new core_kernel_classes_Class(RDF_CLASS,__METHOD__);
		$intance = $class->createInstance($label,$comment);
		$returnValue = new core_kernel_classes_Class($intance->uriResource);
		$returnValue->setSubClassOf($resource);
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F32 end

        return $returnValue;
    }

    /**
     * Short description of method createProperty
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @param  boolean isLgDependent
     * @return core_kernel_classes_Property
     */
    public function createProperty( core_kernel_classes_Resource $resource, $label = '', $comment = '', $isLgDependent = false)
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F3C begin
        
    	$property = new core_kernel_classes_Class(RDF_PROPERTY,__METHOD__);
		$propertyInstance = $property->createInstance($label,$comment);
		$returnValue = new core_kernel_classes_Property($propertyInstance->uriResource,__METHOD__);
		$returnValue->setLgDependent($isLgDependent);

		if (!$resource->setProperty($returnValue)){
			throw new common_Exception('proplem creating property');
		}
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F3C end

        return $returnValue;
    }

    /**
     * Short description of method searchInstances
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  array propertyFilters
     * @param  array options
     * @return array
     */
    public function searchInstances( core_kernel_classes_Resource $resource, $propertyFilters = array(), $options = array())
    {
        $returnValue = array();

        // section 10-13-1--128--26678bb4:12fbafcb344:-8000:00000000000014F0 begin
		if(count($propertyFilters) == 0){
			return $returnValue;
		}
		
		$dbWrapper = core_kernel_classes_DbWrapper::singleton(DATABASE_NAME);
		
		//add the type check to the filters
		$propertyFilters[RDF_TYPE] = $resource->uriResource;
		
		$langToken = '';
		if(isset($options['lang'])){
			if(preg_match('/^[a-zA-Z]{2,4}$/', $options['lang'])){
				$langToken = " AND (l_language = '' OR l_language = '{$options['lang']}') ";
			}
		}
		$like = true;
		if(isset($options['like'])){
			$like = ($options['like'] === true);
		}

		$query = "SELECT DISTINCT `subject` FROM `statements` WHERE ";

		$conditions = array();
		foreach($propertyFilters as $propUri => $pattern){
			
			$propUri = $dbWrapper->dbConnector->escape($propUri);
			
			if(is_string($pattern)){
				if(!empty($pattern)){

					$pattern = $dbWrapper->dbConnector->escape($pattern);
					
					if($like){
						$object = trim(str_replace('*', '%', $pattern));
						if(!preg_match("/^%/", $object)){
							$object = "%".$object;
						}
						if(!preg_match("/%$/", $object)){
							$object = $object."%";
						}
						$conditions[] = " (`predicate` = '{$propUri}' AND `object` LIKE '{$object}' $langToken ) ";
					}
					else{
						$conditions[] = " (`predicate` = '{$propUri}' AND `object` = '{$pattern}' $langToken ) ";
					}
				}
			}
			if(is_array($pattern)){
				if(count($pattern) > 0){
					$multiCondition =  " (`predicate` = '{$propUri}' AND  ";
					foreach($pattern as $i => $patternToken){
						
						$patternToken = $dbWrapper->dbConnector->escape($patternToken);
						
						if($i > 0){
							$multiCondition .= " OR ";
						}
						$object = trim(str_replace('*', '%', $patternToken));
						if(!preg_match("/^%/", $object)){
							$object = "%".$object;
						}
						if(!preg_match("/%$/", $object)){
							$object = $object."%";
						}
						$multiCondition .= " `object` LIKE '{$object}' ";
					}
					$conditions[] = "{$multiCondition} {$langToken} ) ";
				}
			}
		}
		if(count($conditions) == 0){
			return $returnValue;
		}

		$intersect = true;
		if(isset($options['chaining'])){
			if($options['chaining'] == 'or'){
				$intersect = false;
			}
		}
		
		$matchingUris = array();
		if(count($conditions) > 0){
			$i = 0;
			foreach($conditions as $condition){
				$tmpMatchingUris = array();
				$result = $dbWrapper->execSql($query . $condition);
				while (!$result->EOF){
					$tmpMatchingUris[] = $result->fields['subject'];
					$result->MoveNext();
				}
				if($intersect){
					//EXCLUSIVES CONDITIONS
					if($i == 0){
						$matchingUris = $tmpMatchingUris;
					}
					else{
						$matchingUris = array_intersect($matchingUris, $tmpMatchingUris);
					}
				}
				else{
					//INCLUSIVES CONDITIONS
					$matchingUris = array_merge($matchingUris, $tmpMatchingUris);
				}
				$i++;
			}
		}
		
		foreach($matchingUris as $matchingUri){
			$returnValue[$matchingUri] = new core_kernel_classes_Resource($matchingUri);
		}
		
		
		//Check in the subClasses recurslively.
		// Be carefull, it can be perf consuming with large data set and subclasses
		(isset($options['recursive'])) ? $recursive = (bool)$options['recursive'] : $recursive = false;
		if($recursive){
			//the recusivity depth is set to one level
			foreach($resource->getSubClasses(true) as $subClass){
				unset($propertyFilters[RDF_TYPE]);//reset the RDF_TYPE filter for recursive searching!
				$returnValue = array_merge(
					$returnValue, 
					$subClass->searchInstances($propertyFilters, array_merge($options, array('recursive' => false)))
				);
			}
		}

        // section 10-13-1--128--26678bb4:12fbafcb344:-8000:00000000000014F0 end

        return (array) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001493 begin
        
        if (core_kernel_persistence_smoothsql_Class::$instance == null){
        	core_kernel_persistence_smoothsql_Class::$instance = new core_kernel_persistence_smoothsql_Class();
        }
        $returnValue = core_kernel_persistence_smoothsql_Class::$instance;
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001493 end

        return $returnValue;
    }

    /**
     * Short description of method isValidContext
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isValidContext( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F4E begin
        
        $returnValue = true;
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F4E end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_persistence_smoothsql_Class */

?>
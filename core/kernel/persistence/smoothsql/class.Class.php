<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 06.12.2011, 15:05:57 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_persistence_smoothsql
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_persistence_PersistenceImpl
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('core/kernel/persistence/class.PersistenceImpl.php');

/**
 * include core_kernel_persistence_ClassInterface
 *
 * @author firstname and lastname of author, <author@example.org>
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
 * @author firstname and lastname of author, <author@example.org>
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
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getSubClasses( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014EB begin

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlQuery = 'SELECT "subject" FROM "statements" WHERE "predicate" = ? and "object" = ?';
		$sqlResult = $dbWrapper->execSql($sqlQuery, array(
			RDF_SUBCLASSOF,
			$resource->uriResource
		));
		while (!$sqlResult-> EOF){
			$subClass = new core_kernel_classes_Class($sqlResult->fields['subject']);
			$returnValue[$subClass->uriResource] = $subClass;
			if($recursive == true ){
				$plop = $subClass->getSubClasses(true);
				$returnValue = array_merge($returnValue, $plop);
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
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  Class parentClass
     * @return boolean
     */
    public function isSubClassOf( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $parentClass)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F0 begin

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();

		$query = 'SELECT "object" FROM "statements"
					WHERE "subject" = ?
					AND "predicate" = ? AND "object" = ?';
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
			$parentSubClasses = $parentClass->getSubClasses(true);
			foreach ($parentSubClasses as $subClass){
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
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getParentClasses( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F5 begin
        $returnValue =  array();
		
        $sqlQuery = 'SELECT "object" FROM "statements" 
        			WHERE "subject" = ? 
        			AND ("predicate" = ? OR "predicate" = ?)';

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlResult = $dbWrapper->execSql($sqlQuery, array (
			$resource->uriResource,
			RDF_SUBCLASSOF,
			RDF_TYPE
		));

		while (!$sqlResult-> EOF){

			$parentClass = new core_kernel_classes_Class($sqlResult->fields['object']);

			$returnValue[$parentClass->uriResource] = $parentClass ;
			if($recursive == true && $parentClass->uriResource != RDF_CLASS && $parentClass->uriResource != RDF_RESOURCE){
				$plop = $parentClass->getParentClasses(true);
				$returnValue = array_merge($returnValue, $plop);
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
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getProperties( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014FA begin
		$sqlQuery = 'SELECT "subject" FROM "statements"
			WHERE "predicate" = ? 
			AND "object" = ?';
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlResult = $dbWrapper->execSql($sqlQuery, array(
			RDF_DOMAIN,
			$resource->uriResource
		));

		while (!$sqlResult-> EOF){
			$property = new core_kernel_classes_Property($sqlResult->fields['subject']);
			$returnValue[$property->uriResource] = $property;
			$sqlResult->MoveNext();
		}
		if($recursive == true) {
			$parentClasses = $resource->getParentClasses(true);
			foreach ($parentClasses as $parent) {
				if($parent->uriResource != RDF_CLASS) {
					$returnValue = array_merge($returnValue, $parent->getProperties(true));
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
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  boolean recursive
     * @param  array params
     * @return array
     */
    public function getInstances( core_kernel_classes_Resource $resource, $recursive = false, $params = array())
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001500 begin
		
        $sqlQuery = 'SELECT "subject" FROM "statements" 
						WHERE "predicate" = ?  
							AND "object" = ? ';
		if(isset($params['limit'])){
			$offset = 0;
			$limit = intval($params['limit']);
			if ($limit==0){
				$limit = 1000000;
			}
			if(isset($params['offset'])){
				$offset = intval($params['offset']);
			}
			$sqlQuery .= "LIMIT {$limit} OFFSET {$offset}";
		}
		
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlResult = $dbWrapper->execSql($sqlQuery, array (
			RDF_TYPE,
			$resource->uriResource
		));
    	if($dbWrapper->dbConnector->errorNo() !== 0){
			throw new core_kernel_persistence_smoothsql_Exception('Unable to get instances of a class ('.$resource->uriResource.') : '.$dbWrapper->dbConnector->errorMsg());
		}

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
				$returnValue = array_merge($returnValue, $subClass->getInstances(true));
			}
		}

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001500 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setInstance
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
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
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  Class iClass
     * @return boolean
     */
    public function setSubClassOf( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $iClass)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000150F begin

		$subClassOf = new core_kernel_classes_Property(RDF_SUBCLASSOF);
		$returnValue = $resource->setPropertyValue($subClassOf, $iClass->uriResource);

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000150F end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setProperty
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  Property property
     * @return boolean
     */
    public function setProperty( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001512 begin

		$domain = new core_kernel_classes_Property(RDF_DOMAIN, __METHOD__);
		$instanceProperty = new core_kernel_classes_Resource($property->uriResource, __METHOD__);
		$returnValue = $instanceProperty->setPropertyValue($domain, $resource->uriResource);

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001512 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method createInstance
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
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
			if ($uri[0]=='#'){
				$modelUri = core_kernel_classes_Session::singleton()->getNameSpace();
				$subject = $modelUri . $uri;
			} else {
				$subject = $uri;
			}
		}

		$returnValue = new core_kernel_classes_Resource($subject, __METHOD__);
		if(!$returnValue->hasType($resource)){
			$returnValue->setType($resource);
		}

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
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function createSubClass( core_kernel_classes_Resource $resource, $label = '', $comment = '', $uri = '')
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F32 begin
        
        $class = new core_kernel_classes_Class(RDF_CLASS, __METHOD__);
		$intance = $class->createInstance($label, $comment, $uri);
		$returnValue = new core_kernel_classes_Class($intance->uriResource);
		$returnValue->setSubClassOf($resource);
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F32 end

        return $returnValue;
    }

    /**
     * Short description of method createProperty
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
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
        
    	$property = new core_kernel_classes_Class(RDF_PROPERTY, __METHOD__);
		$propertyInstance = $property->createInstance($label,$comment);
		$returnValue = new core_kernel_classes_Property($propertyInstance->uriResource, __METHOD__);
		$returnValue->setLgDependent($isLgDependent);

		if (!$resource->setProperty($returnValue)){
			throw new common_Exception('problem creating property');
		}
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F3C end

        return $returnValue;
    }

    /**
     * Short description of method searchInstances
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  array propertyFilters
     * @param  array options
     * @return array
     */
    public function searchInstances( core_kernel_classes_Resource $resource, $propertyFilters = array(), $options = array())
    {
        $returnValue = array();

        // section 10-13-1--128--26678bb4:12fbafcb344:-8000:00000000000014F0 begin

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$query = $this->getFilteredQuery($resource, $propertyFilters, $options);
		$result = $dbWrapper->execSql($query);
		if($dbWrapper->dbConnector->errorNo() !== 0){
			throw new core_kernel_persistence_smoothsql_Exception($dbWrapper->dbConnector->errorMsg());
		}
		
		
		while (!$result->EOF){
			$foundInstancesUri = $result->fields['subject'];
			$returnValue[$foundInstancesUri] = new core_kernel_classes_Resource($foundInstancesUri);
			$result->MoveNext();
		}
	
        // section 10-13-1--128--26678bb4:12fbafcb344:-8000:00000000000014F0 end

        return (array) $returnValue;
    }

    /**
     * Short description of method countInstances
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  array propertyFilters
     * @param  array options
     * @return Integer
     */
    public function countInstances( core_kernel_classes_Resource $resource, $propertyFilters = array(), $options = array())
    {
        $returnValue = null;

        // section 127-0-1-1--700ce06c:130dbc6fc61:-8000:000000000000159D begin

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();

		if (isset($propertyFilters) && count($propertyFilters)) {
			if (isset($options['limit_start'])) unset($options['limit_start']);
			if (isset($options['limit_length'])) unset($options['limit_length']);
			$query = $this->getFilteredQuery($resource, $propertyFilters, $options);
			$sqlResult = $dbWrapper->execSql($query);
			$returnValue = $sqlResult->RecordCount();
		}
		else {
			$sqlQuery = 'SELECT count("subject") as count FROM "statements"
							WHERE "predicate" = ?  
								AND "object" = ? ';
			
			$sqlResult = $dbWrapper->execSql($sqlQuery, array(
				RDF_TYPE,
				$resource->uriResource
			));

			if (!$sqlResult->EOF) {
				$returnValue = $sqlResult->fields['count'];
			}
		}

        
        // section 127-0-1-1--700ce06c:130dbc6fc61:-8000:000000000000159D end

        return $returnValue;
    }

    /**
     * Short description of method getInstancesPropertyValues
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  Property property
     * @param  array propertyFilters
     * @param  array options
     * @return array
     */
    public function getInstancesPropertyValues( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $propertyFilters = array(), $options = array())
    {
        $returnValue = array();

        // section 127-0-1-1--120bf54f:13142fdf597:-8000:000000000000312D begin
        
    	$distinct = isset($options['distinct']) ? $options['distinct'] : false;
    	$recursive = isset($options['recursive']) ? $options['recursive'] : false;
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $uris = '';
        $searchInstancesOptions = array (
        	'recursive' 	=> $recursive
        );
        
        // Search all instances for the property filters paramter
        $instances = $resource->searchInstances($propertyFilters, $searchInstancesOptions);
        if ($instances){
	        foreach ($instances as $instance){
	        	$uris .= '\''.$instance->uriResource.'\',';
	        } 
	        $uris = substr($uris, 0, strlen($uris)-1);
	        
	        // Get all the available property values in the subset of instances
	        $query = 'SELECT';
	        if($distinct){
	        	$query .= ' DISTINCT';
	        }
	        $query .= ' "object" FROM "statements"
	        	WHERE "predicate" = ?
	        	AND "subject" IN ('.$uris.')';
			$sqlResult = $dbWrapper->execSql($query, array(
				$property->uriResource
			));
	    	
	    	if($dbWrapper->dbConnector->errorNo() !== 0){
				throw new core_kernel_persistence_smoothsql_Exception('Unable to get instances\' property values : '.$dbWrapper->dbConnector->errorMsg());
			}
			
			while (!$sqlResult->EOF){
				if(!common_Utils::isUri($sqlResult->fields['object'])) {
	                $returnValue[] = new core_kernel_classes_Literal($sqlResult->fields['object']);
	            }
	            else {
	                $returnValue[] = new core_kernel_classes_Resource($sqlResult->fields['object']);
	            }
				$sqlResult->moveNext();
			}
        }
        
        // section 127-0-1-1--120bf54f:13142fdf597:-8000:000000000000312D end

        return (array) $returnValue;
    }

    /**
     * Short description of method unsetProperty
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource resource
     * @param  Property property
     * @return boolean
     */
    public function unsetProperty( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4f08ff91:131764e4b1f:-8000:00000000000031F8 begin
        
		$domain = new core_kernel_classes_Property(RDF_DOMAIN, __METHOD__);
		$instanceProperty = new core_kernel_classes_Resource($property->uriResource, __METHOD__);
		$returnValue = $instanceProperty->removePropertyValues($domain, array('pattern' => $resource->uriResource));
        
        // section 127-0-1-1-4f08ff91:131764e4b1f:-8000:00000000000031F8 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
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
     * @author firstname and lastname of author, <author@example.org>
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

    /**
     * Short description of method getFilteredQuery
     *
     * @access public
     * @author Jehan Bihin
     * @param  Resource resource
     * @param  array propertyFilters
     * @param  array options
     * @return string
     * @version 1.0
     */
    public function getFilteredQuery( core_kernel_classes_Resource $resource, $propertyFilters = array(), $options = array())
    {
        $returnValue = (string) '';

        // section 127-0-1-1--1bdaa580:13412f85251:-8000:00000000000017CC begin
				/*
		options lists:
		like			: (bool) 	true/false (default: true)
		chaining		: (string) 	'or'/'and' (default: 'and')
		recursive		: (int) 	recursivity depth (default: 0)
		lang			: (string) 	e.g. 'EN', 'FR' (default: '') for all properties!
		limit_start		: default 0
		limit_length	: default select all
		*/

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		
		//add the type check to the filters
		if (isset($propertyFilters[RDF_TYPE])) {
			if (!is_array($propertyFilters[RDF_TYPE])) $propertyFilters[RDF_TYPE] = array($propertyFilters[RDF_TYPE], $resource->uriResource);
			else $propertyFilters[RDF_TYPE] = array_merge($propertyFilters[RDF_TYPE], array($resource->uriResource));
		}
		else $propertyFilters[RDF_TYPE] = $resource->uriResource;

		//Check in the subClasses recurslively.
		if ($options['recursive']) {
			$rdftypes = $propertyFilters[RDF_TYPE];
			if (!is_array($rdftypes)) $rdftypes = array($rdftypes);
			$subclasses = $this->getSubClasses($resource, $options['recursive']);
			foreach($subclasses as $sc) {
			    $rdftypes[] = $sc->uriResource;
			}
			$propertyFilters[RDF_TYPE] = $rdftypes;
		}

		$langToken = '';
		if(isset($options['lang'])){
			if(preg_match('/^[a-zA-Z]{2,4}$/', $options['lang'])){
				$langToken = ' AND ("l_language" = \'\' OR "l_language" = \''.$options['lang'].'\') ';
			}
		}
		$like = true;
		if(isset($options['like'])){
			$like = ($options['like'] === true);
		}

		$query = 'SELECT "subject" FROM "statements" WHERE ';

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
						$conditions[] = ' ("predicate" = \''.$propUri.'\' AND "object" LIKE \''.$object.'\' '.$langToken.' ) ';
					}
					else{
						$conditions[] = ' ("predicate" = \''.$propUri.'\' AND "object" = \''.$pattern.'\' '.$langToken.' ) ';
					}
				}
			}
			else if(is_array($pattern)){
				if(count($pattern) > 0){
					$multiCondition =  '';
					foreach($pattern as $i => $patternToken){
						
						$patternToken = $dbWrapper->dbConnector->escape($patternToken);
						
						if($i > 0){
							$multiCondition .= " OR ";
						}
						
						if($like){
							$object = trim(str_replace('*', '%', $patternToken));
							if(!preg_match("/^%/", $object)){
								$object = "%".$object;
							}
							if(!preg_match("/%$/", $object)){
								$object = $object."%";
							}
							$multiCondition .= ' "object" LIKE \''.$object.'\' ';
						}else{
							$multiCondition .= ' "object" = \''.$patternToken.'\' ';
						}
					}
					$conditions[] = " (\"predicate\" = '{$propUri}' AND ({$multiCondition}) {$langToken} ) ";
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

		$limit = "";
		if (isset($options['limit_start'])){
		  $limit = intval($options['limit_start']);
		}
		if (isset($options['limit_length'])){
		  if (isset($options['limit_start'])) $limit .= ', '.intval($options['limit_length']);
		  else $limit = '0, '.intval($options['limit_length']);
		}
		if (isset($options['limit_start']) || isset($options['limit_length'])) $limit = ' ORDER BY "id" LIMIT '.$limit;

		$q = '';
		if ($intersect) {
			foreach ($conditions as $condition) {
				if (!strlen($q)) $q = $query . $condition;
				else $q = $query . $condition . ' AND "subject" IN (' . $q . ')';
			}
			$query = $q;
		}
		else $query = join('OR', $conditions);

		$returnValue = $query . $limit;
        // section 127-0-1-1--1bdaa580:13412f85251:-8000:00000000000017CC end

        return (string) $returnValue;
    }

} /* end of class core_kernel_persistence_smoothsql_Class */

?>
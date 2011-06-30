<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/persistence/hardsql/class.Class.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 29.06.2011, 16:28:36 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_persistence_hardsql
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
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A7-includes begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A7-includes end

/* user defined constants */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A7-constants begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A7-constants end

/**
 * Short description of class core_kernel_persistence_hardsql_Class
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_persistence_hardsql
 */
class core_kernel_persistence_hardsql_Class
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
		if(class_exists('core_kernel_persistence_smoothsql_Class')){
			//the model is not hardened and remains in the soft table:
			$returnValue = core_kernel_persistence_smoothsql_Class::singleton()->getSubClasses($resource, $recursive);
		}else{
			throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
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
		if(class_exists('core_kernel_persistence_smoothsql_Class')){
			//the model is not hardened and remains in the soft table:
			$returnValue = core_kernel_persistence_smoothsql_Class::singleton()->isSubClassOf($resource, $parentClass);
		}else{
			throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
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
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
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
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
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

		if(isset($params['limit'])){
			$limit = intval($params['limit']);
			if($limit){
				$offset = 0;
				if(isset($params['offset'])){
					$offset = intval($params['offset']);
					if ($offset>0){
						throw new core_kernel_persistence_hardapi_Exception("The offset options is not allowed in this persistence mode");
					}
				}
			}
		}
		
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$classLocations = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->classLocations($resource);
		foreach ($classLocations as $classLocation){
				
			$tableName = $classLocation['table'];
			$sqlQuery = "SELECT uri FROM `{$tableName}` WHERE 1";
			if (isset($limit)) {
				$limit = $limit - count($returnValue);
				if ($limit > 0) {
					$sqlQuery .= " LIMIT {$limit}";
				} else {
					break;
				}
			}
			
			$sqlResult = $dbWrapper->execSql($sqlQuery);
			while (!$sqlResult->EOF){

				$instance = new core_kernel_classes_Resource($sqlResult->fields['uri']);
				$returnValue[$instance->uriResource] = $instance ;
				$sqlResult->MoveNext();
			}
			if($recursive == true){
					
				$subClasses = $resource->getSubClasses(true);
				foreach ($subClasses as $subClass){

					if (isset($limit)){
						
						$limit = $limit - count($returnValue);
						if ($limit > 0){
							$returnValue = array_merge($returnValue, $subClass->getInstances(true), array('limit'=>$limit));
						} else {
							break 2;
						}
					}else{
						$returnValue = array_merge($returnValue, $subClass->getInstances(true));
					}
				}
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
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
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
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
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
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
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

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();

		if($uri == ''){
			 
			$subject = common_Utils::getNewUri();
		}
		else {
				
			//$uri should start with # and be well formed
			$modelUri = core_kernel_classes_Session::singleton()->getNameSpace();
			$subject = $modelUri . $uri;
		}

		$returnValue = new core_kernel_classes_Resource($subject,__METHOD__);

		$table = '_'.core_kernel_persistence_hardapi_Utils::getShortName ($resource);
		$query = "INSERT INTO `{$table}` (`uri`) VALUES (?)";
		$result = $dbWrapper->execSql($query, array($subject));
		 
		if($dbWrapper->dbConnector->errorNo() !== 0){
			throw new core_kernel_persistence_hardapi_Exception("Unable to create instance for the resource {$resource->uriResource} in the table {$table} : " .$dbWrapper->dbConnector->errorMsg());
		} else {
				
			// reference the newly created instance
			core_kernel_persistence_hardapi_ResourceReferencer::singleton()->referenceResource($returnValue, $table, array($resource), true);
			 
			if (!empty($label)){
				$returnValue->setPropertyValue(new core_kernel_classes_Property(RDFS_LABEL), $label);
			}
			if (!empty($comment)){
				$returnValue->setPropertyValue(new core_kernel_classes_Property(RDFS_COMMENT), $comment);
			}
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
     * @return core_kernel_classes_Class
     */
    public function createSubClass( core_kernel_classes_Resource $resource, $label = '', $comment = '')
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F32 begin
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
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
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
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
		/*
			options lists:
			like			: (bool) 	true/false (default: true)
			chaining		: (string) 	'or'/'and' (default: 'and')
			recursive		: (bool) 	true/false (default: true)
			lang			: (string) 	e.g. 'EN', 'FR' (default: '')
			*/
		if(count($propertyFilters) == 0 || !core_kernel_persistence_hardapi_ResourceReferencer::singleton()->isClassReferenced(new core_kernel_classes_Class($resource->uriResource))){
			return $returnValue;
		}

		$dbWrapper = core_kernel_classes_DbWrapper::singleton(DATABASE_NAME);

		$like = true;
		if(isset($options['like'])){
			$like = ($options['like'] === true);
		}

		$tableName = '';
		$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		$classLocations = $referencer->classLocations($resource);
		if(isset($classLocations[0])){
			$tableName = $classLocations[0]['table'];
		}else{
			return $returnValue;
		}

		$tablePropertiesName = $tableName.'Props';
		$tableNames = array('t0' => $tableName);

		$conditions = array();
		foreach($propertyFilters as $propUri => $pattern){
				
			$property = new core_kernel_classes_Property($propUri);
			$propName = core_kernel_persistence_hardapi_Utils::getShortName($property);
				
			$propsTabIndex = '00';

			$propertyLocation = $referencer->propertyLocation($property);
			if(in_array($tablePropertiesName, $propertyLocation)){
				$classPropsTabIndex = count($tableNames);
				$tableNames['t'.$classPropsTabIndex] = $tablePropertiesName;

				$langToken = "";
				if(isset($options['lang']) && $property->isLgDependent()){
					if(preg_match('/^[a-zA-Z]{2,4}$/', $options['lang'])){
						$langToken = " AND ( t{$classPropsTabIndex}.l_language = '' OR t{$classPropsTabIndex}.l_language = '{$options['lang']}')";
					}
				}

				$condition = "";

				if(is_string($pattern)){

					if(!empty($pattern)){

						$searchPattern = core_kernel_persistence_hardapi_Utils::buildSearchPattern($pattern, $like);
						$condition = " ( (t{$classPropsTabIndex}.property_value {$searchPattern} OR t{$classPropsTabIndex}.property_foreign_uri {$searchPattern}) {$langToken})";
					}
				}
				else if(is_array($pattern)){
					if(count($pattern) > 0){
						$multiCondition =  "(";
						foreach($pattern as $i => $patternToken){
								
							if(!empty($patternToken)){
								$searchPattern = core_kernel_persistence_hardapi_Utils::buildSearchPattern($patternToken, $like);

								if($i > 0){
									$multiCondition .= " OR ";
								}
								$multiCondition .= " ( (t{$classPropsTabIndex}.property_value {$searchPattern} OR t{$classPropsTabIndex}.property_foreign_uri {$searchPattern}) {$langToken})";
							}
						}
						$condition = "{$multiCondition} ) ";
					}
				}
				if(!empty($condition)){
					$conditions[] = " ( t0.id = t{$classPropsTabIndex}.instance_id AND t{$classPropsTabIndex}.property_uri = \"{$propUri}\" AND {$condition} )";
				}

			}
			elseif(in_array($tableName, $propertyLocation)){
					

				$propsTabIndex = count($tableNames);

				if(is_string($pattern)){
					if(!empty($pattern)){
						$searchPattern = core_kernel_persistence_hardapi_Utils::buildSearchPattern($pattern, $like);
						$conditions[] = " ( t0.{$propName} {$searchPattern} )";
					}
				}
				else if(is_array($pattern)){
					if(count($pattern) > 0){
						$multiCondition =  "(";
						foreach($pattern as $i => $patternToken){
								
							$searchPattern = core_kernel_persistence_hardapi_Utils::buildSearchPattern($patternToken, $like);
								
							if($i > 0){
								$multiCondition .= " OR ";
							}
							$multiCondition .= " ( t0.{$propName} {$searchPattern} ) ";
						}
						$conditions[] = "{$multiCondition}) ";
					}
				}

			}
		}

		if(count($conditions) == 0){
			return $returnValue;
		}

		$sqlQuery = "SELECT `uri` FROM ";

		$i = 0;
		foreach($tableNames as $tableIdentifier => $tableName){
			if($i > 0){
				$sqlQuery .= ", ";
			}
			$sqlQuery .= "{$tableName} {$tableIdentifier}";
			$i++;
		}

		$sqlQuery .= " WHERE ";

		if(count($conditions) > 0){
			$intersect = true;
			if(isset($options['chaining'])){
				if($options['chaining'] == 'or'){
					$intersect = false;
				}
			}
				
			$j = 0;
			foreach($conditions as $condition){
				if($j > 0){
					$sqlQuery .= ($intersect)?' AND ':' OR ';
				}
				$sqlQuery .= $condition;
				$j++;
			}
		}

		$sqlResult = $dbWrapper->execSql($sqlQuery);
		while (!$sqlResult->EOF){

			$instance = new core_kernel_classes_Resource($sqlResult->fields['uri']);
			$returnValue[$instance->uriResource] = $instance ;
				
			$sqlResult->MoveNext();
		}

		//Check in the subClasses recurslively.
		// Be carefull, it can be perf consuming with large data set and subclasses
		(isset($options['recursive'])) ? $recursive = (bool)$options['recursive'] : $recursive = false;
		if($recursive){
			//the recusivity depth is set to one level
			foreach($resource->getSubClasses(true) as $subclass){
				$returnValue = array_merge(
				$returnValue,
				$subclass->searchInstances($propertyFilters, array_merge($options, array('recursive' => false)))
				);
			}
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
     * @return Integer
     */
    public function countInstances( core_kernel_classes_Resource $resource)
    {
        $returnValue = null;

        // section 127-0-1-1--700ce06c:130dbc6fc61:-8000:000000000000159D begin
        // section 127-0-1-1--700ce06c:130dbc6fc61:-8000:000000000000159D end

        return $returnValue;
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

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001495 begin

		if (core_kernel_persistence_hardsql_Class::$instance == null){
			core_kernel_persistence_hardsql_Class::$instance = new core_kernel_persistence_hardsql_Class();
		}
		$returnValue = core_kernel_persistence_hardsql_Class::$instance;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001495 end

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

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F57 begin

		if (core_kernel_persistence_hardapi_ResourceReferencer::singleton()->isClassReferenced ($resource)){
			$returnValue = true;
		}

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F57 end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_persistence_hardsql_Class */

?>
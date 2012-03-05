<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 22.02.2012, 16:37:20 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardsql
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_persistence_PersistenceImpl
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('core/kernel/persistence/class.PersistenceImpl.php');

/**
 * include core_kernel_persistence_ResourceInterface
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('core/kernel/persistence/interface.ResourceInterface.php');

/* user defined includes */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000135E-includes begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000135E-includes end

/* user defined constants */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000135E-constants begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000135E-constants end

/**
 * Short description of class core_kernel_persistence_hardsql_Resource
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardsql
 */
class core_kernel_persistence_hardsql_Resource
    extends core_kernel_persistence_PersistenceImpl
        implements core_kernel_persistence_ResourceInterface
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
     * Short description of method getType
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return array
     */
    public function getType( core_kernel_classes_Resource $resource)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001298 begin

		$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
		$query = 'SELECT "class_to_table"."uri"
    		FROM "class_to_table"
    		INNER JOIN "resource_has_class" ON "resource_has_class"."class_id" = "class_to_table"."id"
    		INNER JOIN "resource_to_table" ON "resource_to_table"."id" = "resource_has_class"."resource_id"
    		WHERE "resource_to_table"."uri" = ?';
		$result	= $dbWrapper->execSql($query, array(
				$resource->uriResource
			)
		);
		if($dbWrapper->dbConnector->errorNo() !== 0){
			throw new core_kernel_persistence_hardsql_Exception("Unable to getType of the resource {$resource->uriResource} : " .$dbWrapper->dbConnector->errorMsg());
		} 
		else {	
			while (!$result->EOF){
				$returnValue[] = new core_kernel_classes_Class ($result->fields['uri']);
				$result->moveNext();
			}
		}

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001298 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getPropertyValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  array options
     * @return array
     */
    public function getPropertyValues( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $options = array())
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000129B begin
        
		$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		// hmmmm ! Perplexe
		$table = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->resourceLocation($resource);
		if(empty($table)){
			return $returnValue;
		}
		
		$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
		$propertyAlias = core_kernel_persistence_hardapi_Utils::getShortName($property);
		$propertyLocation = $referencer->propertyLocation($property);

		// Select in the properties table of the class
		if (in_array("{$table}Props", $propertyLocation)
		|| ! $referencer->isPropertyReferenced($property)){
			
			// Check if we have to return first or last entry
			$one = isset($options['one']) && $options['one'] == true ? true : false;
			$last = isset($options['last']) && $options['last'] == true ? true : false;
			
			$tableProps = $table."Props";
			$session = core_kernel_classes_Session::singleton();
			// Define language if required
			$lang = '';
			$defaultLg = '';
			if (isset($options['lg'])){
				$lang = $options['lg'];
			}
			else{
				($session->getLg() != '') ? $lang = $session->getLg() : $lang = $session->defaultLg;
				$defaultLg = ' OR "l_language" = \''.$session->defaultLg.'\' ';
			}
            
			$query = 'SELECT "property_value", "property_foreign_uri"
				FROM "'.$table.'"
				INNER JOIN "'.$tableProps.'" on "'.$table.'"."id" = "'.$tableProps.'"."instance_id"
			   	WHERE "'.$table.'"."uri" = ?
				AND "'.$tableProps.'"."property_uri" = ?
				AND ( "l_language" = ? OR "l_language" = \'\' '.$defaultLg.')';
			
			// Select first
			if ($one) {
				$result	= $dbWrapper->dbConnector->selectLimit($query, 1, -1, array(
					$resource->uriResource
					, $property->uriResource
					, $lang
				));
			}
			// Select Last
			else if ($last) {
				$result	= $dbWrapper->execSql($query, array(
					$resource->uriResource
					, $property->uriResource
					, $lang
				));
				if (!$result->EOF){
					$result->moveLast();
				}
			}
			// Select All
			else {
				$result	= $dbWrapper->execSql($query, array(
					$resource->uriResource
					, $property->uriResource
					, $lang
				));
			}
				
			if($dbWrapper->dbConnector->errorNo() !== 0){
				throw new core_kernel_persistence_hardsql_Exception("Unable to get property (multiple) values for {$resource->uriResource} in {$table} : " .$dbWrapper->dbConnector->errorMsg());
			}
			while (!$result->EOF){
				$returnValue[] = $result->fields['property_value'] != null ? $result->fields['property_value'] : $result->fields['property_foreign_uri'];
				$result->moveNext();
			}
			
		}
		// Select in the main table of the class
		else{
			
			$query =  'SELECT "'.$propertyAlias.'" as "propertyValue" FROM "'.$table.'" WHERE "uri" = ?';
			$result	= $dbWrapper->execSql($query, array(
			$resource->uriResource
			));
			
			if ($dbWrapper->dbConnector->errorNo() == 1054) {
				// Column doesn't exists is not an error. Try to get a property which does not exist is allowed
			}
			else if ($dbWrapper->dbConnector->errorNo() !== 0){ 
				throw new core_kernel_persistence_hardsql_Exception("Unable to get property (single) values for {$resource->uriResource} in {$table} : " .$dbWrapper->dbConnector->errorMsg());
			} 
			else {
				while (!$result->EOF){
					if ($result->fields['propertyValue']!=null){
						$returnValue[] = $result->fields['propertyValue'];
					}
					$result->moveNext();
				}
			}
			
		}

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000129B end

        return (array) $returnValue;
    }

    /**
     * Short description of method getPropertyValuesCollection
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return core_kernel_classes_ContainerCollection
     */
    public function getPropertyValuesCollection( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000129F begin

		$returnValue = new core_kernel_classes_ContainerCollection($resource);
		$values = $resource->getAllPropertyValues($property);
			
		foreach ($values as $value){
			$returnValue->add($value);
		}

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000129F end

        return $returnValue;
    }

    /**
     * Short description of method getOnePropertyValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  boolean last
     * @return core_kernel_classes_Container
     */
    public function getOnePropertyValue( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $last = false)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012A3 begin

		$options = array(
			'forceDefaultLg' => true
		);  
		if($last){
			$options['last'] = true;
		}else{
			$options['one'] = true;
		}

		$value = $resource->getAllPropertyValues($property, $options);
		if (count($value)){
			$returnValue = $value[0];
		}

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012A3 end

        return $returnValue;
    }

    /**
     * Short description of method getPropertyValuesByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  string lg
     * @return core_kernel_classes_ContainerCollection
     */
    public function getPropertyValuesByLg( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $lg)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012A9 begin

		$options = array('lg'=>$lg);
		$returnValue = new core_kernel_classes_ContainerCollection($resource);
        $propertiesValues = $resource->getAllPropertyValues($property, $options);
        $returnValue->sequence = $propertiesValues;
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012A9 end

        return $returnValue;
    }

    /**
     * Short description of method setPropertyValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  string object
     * @param  string lg
     * @return boolean
     */
    public function setPropertyValue( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $object, $lg = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012AE begin

		// Get the table name
		$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		$tableName = $referencer->resourceLocation ($resource);
		if(empty($tableName)){
			return $returnValue;
		}

		$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
		$object  = $object instanceof core_kernel_classes_Resource ? $object->uriResource : (string) $object;
		$instanceId = null;
		$propertyValue = null;
		$propertyForeignUri = null;
		$propertyRange = $property->getRange();

		// Get property instance
		$instanceId = core_kernel_persistence_hardsql_Utils::getInstanceId($resource);

		// Get the property value or property foreign id
		if(!is_null($propertyRange)){

			// Foregin resource
			if ($propertyRange->uriResource != RDFS_LITERAL){
				$propertyForeignUri = $object;
			}
			// The object is a literal
			else {
				$propertyValue = $object;
			}
		}

		$propertyLocation = $referencer->propertyLocation($property);
		if (in_array("{$tableName}Props", $propertyLocation)
		|| !$referencer->isPropertyReferenced($property)){
			 
			$session 	= core_kernel_classes_Session::singleton();
			$lang = "";
			// Define language if required
			if ($property->isLgDependent()){
				if ($lg!=null){
					$lang = $lg;
				} else if ($session->getLg() != ''){
					$lang = $session->getLg();
				} else {
					$lang = $session->defaultLg;
				}
			}

			$query = 'INSERT INTO "'.$tableName.'Props"
        		("instance_id", "property_uri", "property_value", "property_foreign_uri", "l_language") 
        		VALUES (?, ?, ?, ?, ?)';
			$result	= $dbWrapper->execSql($query, array(
			$instanceId,
			$property->uriResource,
			$propertyValue,
			$propertyForeignUri,
			$lang
			));
			if($dbWrapper->dbConnector->errorNo() !== 0){
				throw new core_kernel_persistence_hardsql_Exception("Unable to set property (multiple) Value for the instance {$resource->uriResource} in {$tableName} : " .$dbWrapper->dbConnector->errorMsg());
			}else{
				$returnValue = true;
			}
		} else {
			 
			$propertyName = core_kernel_persistence_hardapi_Utils::getShortName ($property);
			$query = 'UPDATE "'.$tableName.'" SET "'.$propertyName.'" = ? WHERE id = ?';
			$result	= $dbWrapper->execSql($query, array(
			$propertyValue != null ? $propertyValue : $propertyForeignUri
			, $instanceId
			));
			if($dbWrapper->dbConnector->errorNo() !== 0){
				throw new core_kernel_persistence_hardsql_Exception("Unable to set property (single) Value for the instance {$resource->uriResource} in {$tableName} : " .$dbWrapper->dbConnector->errorMsg());
			}else{
				$returnValue = true;
			}
		}

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012AE end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setPropertiesValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  array properties
     * @return boolean
     */
    public function setPropertiesValues( core_kernel_classes_Resource $resource, $properties)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012B3 begin
		if (is_array($properties)) {
			if (count($properties) > 0) {

				// Get the table name
				$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
				$tableName = $referencer->resourceLocation($resource);
				if (empty($tableName)) {
					return $returnValue;
				}

				$instanceId = core_kernel_persistence_hardsql_Utils::getInstanceId($resource);
				$dbWrapper = core_kernel_classes_DbWrapper::singleton();
				$session = core_kernel_classes_Session::singleton();

				$queryProps = '';
				$hardPropertyNames = array();

				foreach ($properties as $propertyUri => $value) {

					$property = new core_kernel_classes_Property($propertyUri);
					$propertyLocation = $referencer->propertyLocation($property);

					if (in_array("{$tableName}Props", $propertyLocation)
						|| !$referencer->isPropertyReferenced($property)) {

						$propertyRange = $property->getRange();
						$lang = ($property->isLgDependent() ? ( $session->getLg() != '' ? $session->getLg() : $session->defaultLg) : '');
						$formatedValues = array();
						if ($value instanceof core_kernel_classes_Resource) {
							$formatedValues[] = $value->uriResource;
						} else if (is_array($value)) {
							foreach ($value as $val) {
								if ($val instanceof core_kernel_classes_Resource) {
									$formatedValues[] = $val->uriResource;
								} else {
									$formatedValues[] = $dbWrapper->dbConnector->escape($val);
								}
							}
						} else {
							$formatedValues[] = $dbWrapper->dbConnector->escape($value);
						}

						if ($propertyRange->uriResource == RDFS_LITERAL) {
							foreach ($formatedValues as $formatedValue) {
								$queryProps .= " ({$instanceId}, '{$property->uriResource}', '{$formatedValue}', null, '{$lang}'),";
							}
						} else {
							foreach ($formatedValues as $formatedValue) {
								$queryProps .= " ({$instanceId}, '{$property->uriResource}', null, '{$formatedValue}', '{$lang}'),";
							}
						}
					} else {

						$propertyName = core_kernel_persistence_hardapi_Utils::getShortName($property);
						if ($value instanceof core_kernel_classes_Resource) {
							$value = $value->uriResource;
						} else if (is_array($value)) {
							throw new core_kernel_persistence_hardsql_Exception("try setting multivalue for the non multiple property {$property->getLabel()} ({$property->uriResource})");
						} else {
							$value = $dbWrapper->dbConnector->escape($value);
						}

						$hardPropertyNames[$propertyName] = $value;
					}
				}

				if (!empty($queryProps)) {
					$query = 'INSERT INTO "' . $tableName . 'Props" ("instance_id", "property_uri", "property_value", "property_foreign_uri", "l_language") VALUES ' . $queryProps;
					$query = substr($query, 0, strlen($query) - 1);
					$result = $dbWrapper->execSql($query);
					if ($dbWrapper->dbConnector->errorNo() !== 0) {
						throw new core_kernel_persistence_hardsql_Exception("Unable to set properties (multiple) Value for the instance {$resource->uriResource} in {$tableName} : " . $dbWrapper->dbConnector->errorMsg());
					} else {
						$returnValue = true;
					}
				}

				if (!empty($hardPropertyNames)) {
					$variables = array();
					$query = 'UPDATE "' . $tableName . '" SET ';
					$i = 0;
					foreach ($hardPropertyNames as $hardPropertyName => $value) {
						if ($i) {
							$query .= ', ';
						}
						$query .= '"' . $hardPropertyName . '" = ?';
						$variables[] = $value;
					}
					$variables[] = $instanceId;
					$result = $dbWrapper->execSql($query, $variables);
					if ($dbWrapper->dbConnector->errorNo() !== 0) {
						throw new core_kernel_persistence_hardsql_Exception("Unable to set properties (single) Value for the instance {$resource->uriResource} in {$tableName} : " . $dbWrapper->dbConnector->errorMsg());
					} else {
						$returnValue = true;
					}
				}
			}
		}

		// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012B3 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setPropertyValueByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  string value
     * @param  string lg
     * @return boolean
     */
    public function setPropertyValueByLg( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $value, $lg)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012B7 begin

		$returnValue = $this->setPropertyValue ($resource, $property, $value, $lg);

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012B7 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method removePropertyValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  array options
     * @return boolean
     */
    public function removePropertyValues( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $options = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012BD begin

		// Get the table name
		$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		$tableName = $referencer->resourceLocation ($resource);
		if(empty($tableName)){
			return $returnValue;
		}

		$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();

		// Optional params
		$pattern = isset($options['pattern']) && !is_null($options['pattern']) ? $options['pattern'] : null;
		$like = isset($options['like']) && $options['like'] == true ? true : false;

		$propertyLocation = $referencer->propertyLocation($property);
		if (in_array("{$tableName}Props", $propertyLocation)
		|| !$referencer->isPropertyReferenced($property, $tableName)){
			 
                        $resourceId = core_kernel_persistence_hardapi_Utils::getResourceIdByTable($resource, $tableName);
                        
                        if($resourceId){
                                
                                $propsTableName = $tableName.'Props';
                                $query = 'DELETE FROM "'.$propsTableName.'" WHERE "property_uri" = \''.$property->uriResource.'\' AND "instance_id" = \''.$resourceId.'\' ';

                                //build additionnal conditions:
                                $additionalConditions = array();
                                if(!is_null($pattern)){
                                        if(is_string($pattern)){
                                                $searchPattern = core_kernel_persistence_hardapi_Utils::buildSearchPattern($pattern, $like);
                                                $additionalConditions[] = ' ("property_value" '.$searchPattern.' OR "property_foreign_uri" '.$searchPattern.') ';
                                        }else if(is_array($pattern)){
                                                if(count($pattern) > 0){
                                                        $multiCondition =  "(";
                                                        foreach($pattern as $i => $patternToken){
                                                                $searchPattern = core_kernel_persistence_hardapi_Utils::buildSearchPattern($patternToken, $like);
                                                                if($i > 0){
                                                                	$multiCondition .= " OR ";
                                                                }
                                                                $multiCondition .= ' ("property_value" '.$searchPattern.' OR "property_foreign_uri" '.$searchPattern.') ';
                                                        }
                                                        $additionalConditions[] = "{$multiCondition}) ";
                                                }
                                        }
                                }

                                foreach($additionalConditions as $i => $additionalCondition){
                                        $query .= " AND ( {$additionalCondition} ) ";
                                }

                                $result	= $dbWrapper->execSql($query);
                                if($dbWrapper->dbConnector->errorNo() !== 0){
                                        throw new core_kernel_persistence_hardsql_Exception("Unable to delete property values (multiple) for the instance {$resource->uriResource} : " .$dbWrapper->dbConnector->errorMsg());
                                } else {
                                        $returnValue = true;
                                }
                        }
		} else {
			 
			$propertyName = core_kernel_persistence_hardapi_Utils::getShortName ($property);
			$query = 'UPDATE "'.$tableName.'" SET "'.$propertyName.'" = NULL WHERE uri = ?';
				
			//build additionnal conditions:
			$additionalConditions = array();
			if(!is_null($pattern)){
				if(is_string($pattern)){
					$searchPattern = core_kernel_persistence_hardapi_Utils::buildSearchPattern($pattern, $like);
					$additionalConditions[] = ' ("'.$propertyName.'" '.$searchPattern.') ';
				}else if(is_array($pattern)){
					if(count($pattern) > 0){
						$multiCondition =  "(";
						foreach($pattern as $i => $patternToken){
							$searchPattern = core_kernel_persistence_hardapi_Utils::buildSearchPattern($patternToken, $like);
							if($i > 0){
								$multiCondition .= " OR ";
							}
							$multiCondition .= ' ("'.$tableName.'"."'.$propertyName.'" '.$searchPattern.') ';
						}
						$additionalConditions[] = "{$multiCondition}) ";
					}
				}
			}
				
			foreach($additionalConditions as $i => $additionalCondition){
				$query .= " AND ( {$additionalCondition} ) ";
			}
			
			// Not supported by postgresql
			//$query .= " LIMIT 1";
			
			//slow!!!
			$result	= $dbWrapper->execSql($query, array(
				$resource->uriResource
			));
			if($dbWrapper->dbConnector->errorNo() !== 0){
				throw new core_kernel_persistence_hardsql_Exception("Unable to delete property values (single) for the instance {$resource->uriResource} : " .$dbWrapper->dbConnector->errorMsg());
			} else {
				$returnValue = true;
			}
		}

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012BD end

        return (bool) $returnValue;
    }

    /**
     * Short description of method removePropertyValueByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  string lg
     * @param  array options
     * @return boolean
     */
    public function removePropertyValueByLg( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $lg, $options = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C1 begin

		$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();

		// Optional params
		$pattern = isset($options['pattern']) && !is_null($options['pattern']) ? $options['pattern'] : null;
		$like = isset($options['like']) && $options['like'] == true ? true : false;

		// Get the table name
		$tableName = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->resourceLocation($resource);
		if($property->isLgDependent()){
                        
                        $resourceId = core_kernel_persistence_hardapi_Utils::getResourceIdByTable($resource, $tableName);
                        if($resourceId){
                                
                                $propsTableName = $tableName.'Props';
                                $query = 'DELETE FROM "'.$propsTableName.'"
                                        WHERE "property_uri" = \''.$property->uriResource.'\' 
                                        AND "instance_id" = \''.$resourceId.'\'
                                        AND "l_language" = \''.$lg.'\' ';

                                //build additionnal conditions:
                                $additionalConditions = array();
                                if(!is_null($pattern)){
                                        if(is_string($pattern)){ 
                                                $searchPattern = core_kernel_persistence_hardapi_Utils::buildSearchPattern($pattern, $like);
                                                $additionalConditions[] = ' ("property_value" '.$searchPattern.' OR "property_foreign_uri" '.$searchPattern.') ';
                                        }else if(is_array($pattern)){
                                                if(count($pattern) > 0){
                                                        $multiCondition =  "(";
                                                        foreach($pattern as $i => $patternToken){
                                                                $searchPattern = core_kernel_persistence_hardapi_Utils::buildSearchPattern($patternToken, $like);
                                                                if($i > 0){
                                                                	$multiCondition .= " OR ";
                                                                }
                                                                $multiCondition .= ' ("property_value" '.$searchPattern.' OR "property_foreign_uri" '.$searchPattern.') ';
                                                        }
                                                        $additionalConditions[] = "{$multiCondition}) ";
                                                }
                                        }
                                }

                                foreach($additionalConditions as $i => $additionalCondition){
                                        $query .= " AND ( {$additionalCondition} ) ";
                                }

                                $result	= $dbWrapper->execSql($query);
                                if($dbWrapper->dbConnector->errorNo() !== 0){
                                        throw new core_kernel_persistence_hardsql_Exception("Unable to delete property values (multiple) for the instance {$resource->uriResource} : " .$dbWrapper->dbConnector->errorMsg());
                                } else {
                                        //var_dump($dbWrapper->dbConnector->Affected_Rows());
                                        $returnValue = true;
                                }
                        
                        }
		}

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C1 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getRdfTriples
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return core_kernel_classes_ContainerCollection
     */
    public function getRdfTriples( core_kernel_classes_Resource $resource)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C6 begin
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C6 end

        return $returnValue;
    }

    /**
     * Short description of method getUsedLanguages
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return array
     */
    public function getUsedLanguages( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C9 begin
		
		$tableName = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->resourceLocation($resource);
		$sqlQuery = 'SELECT "'.$tableName.'Props"."l_language" FROM "'.$tableName.'Props" 
			LEFT JOIN "'.$tableName.'" ON "'.$tableName.'".id = "'.$tableName.'Props".instance_id
			WHERE "'.$tableName.'"."uri" = ? 
				AND "'.$tableName.'Props"."property_uri" = ?';
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlResult = $dbWrapper->execSql($sqlQuery, array (
			$resource->uriResource,
			$property->uriResource
		));
		while (!$sqlResult-> EOF){
			$returnValue[]=$sqlResult->fields['l_language'];
			$sqlResult->MoveNext();
		}
		
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C9 end

        return (array) $returnValue;
    }

    /**
     * Short description of method duplicate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  array excludedProperties
     * @return core_kernel_classes_Resource
     */
    public function duplicate( core_kernel_classes_Resource $resource, $excludedProperties = array())
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012CD begin
		$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		$tableName = $referencer->resourceLocation ($resource);
		if(empty($tableName)){
			return $returnValue;
		}

		//the new Uri
		$newUri = common_Utils::getNewUri();

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();

		//duplicate the row in the main table
		$query = 'SELECT * FROM "'.$tableName.'" WHERE "uri" = ?';
		$result = $dbWrapper->execSql($query, array($resource->uriResource));
		if(!$result->EOF){
				
			//get the columns to duplicate
			$columnProps =array();
			for($i=0; $i < $result->FieldCount(); $i++){
				$column = $result->FetchField($i);

				if(preg_match("/^[0-9]{2,}/", $column->name)){
					$propertyUri = core_kernel_persistence_hardapi_Utils::getLongName($column->name);
					if(!in_array($propertyUri, $excludedProperties)){	//check if the property is excluded
						$columnProps[$propertyUri] = $column->name;
					}
				}
			}
			$instanceId = $result->fields['id'];
				
			//build the insert query
			$insertQuery ='INSERT INTO "'.$tableName.'" ("uri"';
			foreach($columnProps as $column){
				$insertQuery .= ', "'.$column.'"';
			}
			$insertQuery .= ') VALUES (';
			$insertQuery .= "'{$newUri}'";
			foreach($columnProps as $column){
				$insertQuery .= ", '".$result->fields[$column]."'";
			}
			$insertQuery .= ')';
				
			$insertResult = $dbWrapper->execSql($insertQuery);
			if($insertResult !== false  && $instanceId > -1){

				//duplicated data
				$duplicatedResource = new core_kernel_classes_Resource($newUri);
				$referencer->referenceResource($duplicatedResource, $tableName, $resource->getType(), true);

				$duplicateInstanceId = core_kernel_persistence_hardsql_Utils::getInstanceId($duplicatedResource);

				//now we duplciate the rows of the Props table

				//linearize the excluded properties
				$excludedPropertyList = '';
				foreach($excludedProperties as $excludedProperty){
					$excludedPropertyList .= "'{$excludedProperty}',";
				}
				$excludedPropertyList = substr($excludedPropertyList, 0, strlen($excludedPropertyList) -1);

				//query templates of the 3 ways to insert the props rows
				$insertPropValueQuery = 'INSERT INTO "'.$tableName.'Props" ("property_uri", "property_value", "l_language", "instance_id") VALUES (?,?,?,?)';
				$insertPropForeignQuery = 'INSERT INTO "'.$tableName.'Props" ("property_uri", "property_foreign_uri", "l_language", "instance_id") VALUES (?,?,?,?)';
				$insertPropEmptyQuery = 'INSERT INTO "'.$tableName.'Props" ("property_uri", "l_language", "instance_id") VALUES (?,?,?)';

				//get the rows to duplicate
				$propsQuery = 'SELECT * FROM "'.$tableName.'Props" WHERE "instance_id" = ? ';
				$propsQuery .= empty($excludedPropertyList)?'':' AND "property_uri" NOT IN ('.$excludedPropertyList.') ';
				$propsResult = $dbWrapper->execSql($propsQuery, array($instanceId));
				if($dbWrapper->dbConnector->errorNo() !== 0){
					throw new core_kernel_persistence_hardsql_Exception("Unable to duplicate the resource {$resource->uriResource} : " .$dbWrapper->dbConnector->errorMsg());
				}
				
				while(!$propsResult->EOF){
						
					$propUri 		= $propsResult->fields['property_uri'];
					$propValue 		= $propsResult->fields['property_value'];
					$propForeign	= $propsResult->fields['property_foreign_uri'];
					$proplang 		= $propsResult->fields['l_language'];
						
					//insert them regarding the populated columns
					if(!is_null($propValue)  && !empty($propValue)){
						$dbWrapper->execSql($insertPropValueQuery, array($propUri, $propValue, $proplang, $duplicateInstanceId));
					}
					else if(!is_null($propForeign)  && !empty($propForeign)){
						$dbWrapper->execSql($insertPropForeignQuery, array($propUri, $propForeign, $proplang, $duplicateInstanceId));
					}
					else{
						$dbWrapper->execSql($insertPropEmptyQuery, array($propUri, $proplang, $duplicateInstanceId));//costly to insert NULL values
					}
						
					$propsResult->moveNext();
				}

				//return the duplciated resource
				$returnValue = $duplicatedResource;
			}
		}
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012CD end

        return $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  boolean deleteReference
     * @return boolean
     */
    public function delete( core_kernel_classes_Resource $resource, $deleteReference = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012D2 begin

        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $tableName = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->resourceLocation ($resource);
        if(empty($tableName)){
                return $returnValue;
        }

        $uri = $resource->uriResource;

        $resourceId = core_kernel_persistence_hardapi_Utils::getResourceIdByTable($resource, $tableName);
        if($resourceId){
                
                /*
		 * Delete all the references of the resource first, before the resource is delete of course,
		 * if the parameter $deleteReference is true
		 */
		if($deleteReference){
				
			$properties = array();
				
			//get the resource classes (type)
			$types = '';
			foreach($resource->getType() as $type){
				$properties[$type->uriResource] = array();
				$types = "'".$type->uriResource."',";
			}
				
			$types = substr($types, 0, strlen($types) - 1);
				
			if(!empty($types)){

				//get all the properties that have one of the resource class as range
				$sqlQuery = 'SELECT "subject", "object" FROM "statements" WHERE "predicate" = \''.RDFS_RANGE.'\' AND object IN ('.$types.')';
				$result = $dbWrapper->execSql($sqlQuery);

				while(!$result->EOF){
					//fill the properties range: propertyUri => domains:
					$propertyUri = $result->fields['subject'];
					$rangeUri = $result->fields['object'];
					$properties[$rangeUri][$propertyUri] = array();
					$result->moveNext();
						
					//get the domain of the property:
					$property = new core_kernel_classes_Property($propertyUri);
					foreach($property->getDomain()->getIterator() as $domain){
						if($domain instanceof core_kernel_classes_Class){
							$properties[$rangeUri][$propertyUri][] = $domain->uriResource;
						}
					}
				}

				//delete the references
				$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
				foreach($properties as $rangeUri=> $propertyUris){
					foreach($propertyUris as $propertyUri => $domains){
						//property -> column
						$property = new core_kernel_classes_Property($propertyUri);
						$isMulti = ($property->isMultiple() || $property->isLgDependent());
						$columnName = '';
						if(!$isMulti){
							$columnName = core_kernel_persistence_hardapi_Utils::getShortName($property);
							if(empty($columnName)){
								continue;
							}
						}
							
						foreach($domains as $domainUri){
								
							//classLocations -> table
							$classLocations = $referencer->classLocations(new core_kernel_classes_Class($domainUri));
							foreach ($classLocations as $classLocation){

								if($property->isMultiple()){
									//delete the row in the props table
									$query = 'DELETE FROM "'.$classLocation['table'].'Props"
												WHERE "property_uri" = ? 
												AND ("property_value" = ? OR "property_foreign_uri" = ?)';
									$dbWrapper->execSql($query, array(
										$propertyUri,
										$uri,
										$uri
									));
								}
								else {
									//set the col value to NULL
									$query = 'UPDATE "'.$classLocation['table'].'"
												SET "'.$columnName.'" = NULL 
												WHERE "'.$columnName.'" = ?';
									$dbWrapper->execSql($query, array(
										$uri
									));
								}
							}
						}
					}
				}
			}
		}
                
        $queries = array();
		// Delete records in the main table 
		$queries[] = 'DELETE FROM "'.$tableName.'" WHERE "id" = \''.$resourceId.'\'';
		// Delete records in the properties table
        $queries[] = 'DELETE FROM "'.$tableName.'Props" WHERE "instance_id" = \''.$resourceId.'\'';
		
		foreach ($queries as $query) {
			$result = $dbWrapper->execSql($query);
			if($dbWrapper->dbConnector->errorNo() !== 0){
				throw new core_kernel_persistence_hardsql_Exception("Unable to delete resource ({$resource->uriResource}) ;".$dbWrapper->dbConnector->errorMsg());
			}
			if ($result===false){
				$returnValue = false;
				break;
			}else{
				$returnValue = true;
			}
		}

		// Unreference the resource
		core_kernel_persistence_hardapi_ResourceReferencer::singleton()->unReferenceResource($resource);
        }
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012D2 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getLastModificationDate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return core_kernel_persistence_doc_date
     */
    public function getLastModificationDate( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property = null)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012D7 begin
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012D7 end

        return $returnValue;
    }

    /**
     * Short description of method getLastModificationUser
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return string
     */
    public function getLastModificationUser( core_kernel_classes_Resource $resource)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012DC begin
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012DC end

        return (string) $returnValue;
    }

    /**
     * Short description of method getPropertiesValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  array properties
     * @return array
     */
    public function getPropertiesValues( core_kernel_classes_Resource $resource, $properties)
    {
        $returnValue = array();

        // section 127-0-1-1-77557f59:12fa87873f4:-8000:00000000000014D1 begin
        $referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		$table = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->resourceLocation($resource);
		if (empty($table)) {
			return $returnValue;
		}
		$tableProps = $table . 'Props';
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$propertiesMain = '';
		$propertiesProps = '';
		$propertyIndexes = array();
		$propertyIndex = 0;
		foreach ($properties as $property) {

			$propertyAlias = core_kernel_persistence_hardapi_Utils::getShortName($property);
			$propertyLocation = $referencer->propertyLocation($property);

			if (in_array($tableProps, $propertyLocation)
				|| !$referencer->isPropertyReferenced($property)) {
				if (!empty($propertiesProps)) {
					$propertiesProps .= ", ";
				}
				$propertiesProps .= "'" . $property->uriResource . "'";
			} else {
				if (!empty($propertiesMain)) {
					$propertiesMain .= ', ';
				}
				$propertiesMain .= '"' . $propertyAlias . '" as "propertyValue' . $propertyIndex . '"';

				$propertyIndexes[$propertyIndex] = $property;
				$propertyIndex++;
			}
		}

		if (!empty($propertiesProps)) {

			$session = core_kernel_classes_Session::singleton();
			$session = core_kernel_classes_Session::singleton();
			// Define language if required
			$lang = '';
			$defaultLg = '';
			$options = array(); //@TODO: option to be implemented
			if (isset($options['lg'])) {
				$lang = $options['lg'];
			} else {
				($session->getLg() != '') ? $lang = $session->getLg() : $lang = $session->defaultLg;
				$defaultLg = ' OR "l_language" = \'' . $session->defaultLg . '\' ';
			}

			$query = 'SELECT "property_uri", "property_value", "property_foreign_uri"
				FROM "' . $table . '"
				INNER JOIN "' . $tableProps . '" on "' . $table . '"."id" = "' . $tableProps . '"."instance_id"
			   	WHERE "' . $table . '"."uri" = ?
					AND "' . $tableProps . '"."property_uri" IN (' . $propertiesProps . ')
					AND ( "l_language" = ? OR "l_language" = \'\' ' . $defaultLg . ')
				ORDER BY "property_uri"';

			$result = $dbWrapper->execSql($query, array(
				$resource->uriResource
				, $property->uriResource
				, $lang
				));

			if ($dbWrapper->dbConnector->errorNo() !== 0) {
				throw new core_kernel_persistence_hardsql_Exception("Unable to get property (multiple) values for {$resource->uriResource} in {$table} : " . $dbWrapper->dbConnector->errorMsg());
			}
			$currentPredicate = null;
			while (!$result->EOF) {

				if ($currentPredicate != $result->fields['property_uri']) {
					$currentPredicate = $result->fields['property_uri'];
					$returnValue[$currentPredicate] = array();
				}

				$value = $result->fields['property_value'] != null ? $result->fields['property_value'] : $result->fields['property_foreign_uri'];
				$returnValue[$currentPredicate][] = common_Utils::isUri($value) ? new core_kernel_classes_Resource($value) : new core_kernel_classes_Literal($value);

				$result->moveNext();
			}
		}

		if (!empty($propertiesMain)) {
			$query = 'SELECT ' . $propertiesMain . ' FROM "' . $table . '" WHERE "uri" = ?';
			$result = $dbWrapper->execSql($query, array(
				$resource->uriResource
				));

			if ($dbWrapper->dbConnector->errorNo() == 1054) {
				// Column doesn't exists is not an error. Try to get a property which does not exist is allowed
			} else if ($dbWrapper->dbConnector->errorNo() !== 0) {
				throw new core_kernel_persistence_hardsql_Exception("Unable to get property (single) values for {$resource->uriResource} in {$table} : " . $dbWrapper->dbConnector->errorMsg());
			} else {
				while (!$result->EOF) {
					foreach ($propertyIndexes as $propertyIndex => $property) {
						$returnValue[$property->uriResource] = array();
						if ($result->fields['propertyValue' . $propertyIndex] != null) {
							$value = $result->fields['propertyValue' . $propertyIndex];
							$returnValue[$property->uriResource][] = common_Utils::isUri($value) ? new core_kernel_classes_Resource($value) : new core_kernel_classes_Literal($value);
						}
					}
					$result->moveNext();
				}
			}
		}
        // section 127-0-1-1-77557f59:12fa87873f4:-8000:00000000000014D1 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setType
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Class class
     * @return boolean
     */
    public function setType( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:0000000000001548 begin
		
		$classToTableId = core_kernel_persistence_hardsql_Utils::getClassId($class, $resource);
		$resourceLocation = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->resourceLocation($resource);
		
		// If classToTableId is null
		// !!!!!!!!!!!! BE CARREFULL !!!!!!!!!!!!!!
		// We reference it in class_to_table, setType function is used to mark resources with classes (like a tag system)
		// If the class has not been hardified and contains instances, the function will throw an exception
		if ($classToTableId==null){
			
			/*
			 * @todo Write a class hasInstance function
			 */
			$instances = $class->getInstances();
			if (!core_kernel_persistence_hardapi_ResourceReferencer::singleton()->isClassReferenced($class) && !empty($instances)) {
				throw new core_kernel_persistence_hardsql_Exception("Try to set a type ({$class->getLabel()}), which has not been hardified and has instances, to a resource ({$resource->getLabel()})");
			}
			
			$returnValue = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->referenceClass($class, array('table'=>$resourceLocation));//use default top class
			$classToTableId = core_kernel_persistence_hardsql_Utils::getClassId ($class, $resource);
		}

		// Check if the resource is already associated with the class
		if ($resource->hasType($class)){
			return true;
		}

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$resourceToTableId = core_kernel_persistence_hardsql_Utils::getResourceToTableId ($resource);

		// Associate the resource with the class
		$query = 'INSERT INTO "resource_has_class" ("resource_id", "class_id") VALUES (?,?)';
		$result	= $dbWrapper->execSql($query, array(
		$resourceToTableId,
		$classToTableId
		));
		if($dbWrapper->dbConnector->errorNo() !== 0){
			throw new core_kernel_persistence_hardsql_Exception("Unable to associate a class {$class->uriResource} to a resource {$resource->uriResource} : " .$dbWrapper->dbConnector->errorMsg());
		} else {
			$returnValue = true;
		}
		 
		// Check if the association class to table has not yet been referenced
		foreach (core_kernel_persistence_hardapi_ResourceReferencer::singleton()->classLocations ($class) as $classLocation){
			if ($classLocation['uri'] == $class->uriResource && $classLocation['table']==$resourceLocation){
				return true;
			}
		}

		// Associate the class with the table
		$query = 'INSERT INTO "class_to_table" ("uri", "table") VALUES (?,?)';
		$result	= $dbWrapper->execSql($query, array(
		$class->uriResource,
		$resourceLocation
		));
		if($dbWrapper->dbConnector->errorNo() !== 0){
			throw new core_kernel_persistence_hardsql_Exception("Unable to associate a class {$class->uriResource} to a table {$resourceLocation} : " .$dbWrapper->dbConnector->errorMsg());
		} else {
			$returnValue = true;
		}

        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:0000000000001548 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method removeType
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Class class
     * @return boolean
     */
    public function removeType( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:000000000000154C begin

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$instanceId = core_kernel_persistence_hardsql_Utils::getInstanceId ($resource);
		$classId = core_kernel_persistence_hardsql_Utils::getClassId ($class, $resource);

		$query = 'DELETE "resource_has_class".*
        	FROM "resource_has_class" 
        	INNER JOIN "class_to_table" ON "resource_has_class"."class_id" = "class_to_table"."id"
    		INNER JOIN "resource_to_table" ON "resource_to_table"."id" = "resource_has_class"."resource_id"
        	WHERE "resource_to_table"."uri" = ?
        	AND "class_to_table"."uri" = ?';
		$result	= $dbWrapper->execSql($query, array(
		$resource->uriResource,
		$class->uriResource
		));
		if($dbWrapper->dbConnector->errorNo() !== 0){
			throw new core_kernel_persistence_hardsql_Exception("Unable to remove the type {$class->uriResource} for {$resource->uriResource} : " .$dbWrapper->dbConnector->errorMsg());
		} else {
			$returnValue = true;
		}

        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:000000000000154C end

        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000137E begin

		if (core_kernel_persistence_hardsql_Resource::$instance == null){
			core_kernel_persistence_hardsql_Resource::$instance = new core_kernel_persistence_hardsql_Resource();
		}
		$returnValue = core_kernel_persistence_hardsql_Resource::$instance;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000137E end

        return $returnValue;
    }

    /**
     * Short description of method isValidContext
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isValidContext( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F5A begin
			
		if (core_kernel_persistence_hardapi_ResourceReferencer::singleton()->isResourceReferenced ($resource)){
			$returnValue = true;
		}

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F5A end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_persistence_hardsql_Resource */

?>
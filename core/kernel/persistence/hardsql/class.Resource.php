<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 06.05.2011, 16:09:17 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardsql
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_persistence_PersistenceImpl
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/persistence/class.PersistenceImpl.php');

/**
 * include core_kernel_persistence_ResourceInterface
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
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
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
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
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return array
     */
    public function getType( core_kernel_classes_Resource $resource)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001298 begin
		
		// Get the type functions of the table name
		
		$tableName = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->resourceLocation($resource);
		$uri = core_kernel_persistence_hardapi_Utils::getLongName($tableName);
		$returnValue[] = new core_kernel_classes_Resource ($uri);
		
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001298 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getPropertyValues
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  array option
     * @return array
     */
    public function getPropertyValues( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $option = array())
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000129B begin

        $session 	= core_kernel_classes_Session::singleton();
		$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
		
        // Optional params
        $one = isset($options['one']) && $options['one'] == true ? true : false;
        $last = isset($options['last']) && $options['last'] == true ? true : false;
        
    	$table = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->resourceLocation($resource);
		$propertyAlias = core_kernel_persistence_hardapi_Utils::getShortName($property);
        $propertyRange = $property->getRange();
		// Will be used to store the property value or the property foreign id
		$propertyValues = array();
        // Define language if required
        $lang = "";
        if ($property->isLgDependent()){
        	if (isset($option['lg'])){
        		$lang = $option['lg'];
        	} else if ($session->getLg() != ''){
        		$lang = $session->getLg();
        	} else {
        		$lang = $session->defaultLg;
        	}
        }
		// Select in the properties table of the class
		if ($property->isMultiple() || $property->isLgDependent()){
			
			$query = "SELECT property_value, property_foreign_uri 
				FROM {$table} M
				INNER JOIN {$table}Props P on M.id = P.instance_id
			   	WHERE M.uri = ?
				AND P.property_uri = ?
				AND ( l_language = ? OR l_language = '')";
			
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
				throw new core_kernel_persistence_hardapi_Exception("Unable to get property (multiple) values for {$resource->uriResource} in {$table} : " .$dbWrapper->dbConnector->errorMsg());
			}
			while (!$result->EOF){
				$propertyValues[] = $result->fields['property_value'] != null ? $result->fields['property_value'] : $result->fields['property_foreign_uri'];
				if ($propertyValues==null){
					throw new core_kernel_persistence_hardapi_Exception("Unable to get property (multiple) values for {$resource->uriResource} in {$table}, no value defined : " .$dbWrapper->dbConnector->errorMsg());
				}
				$result->moveNext();
			}
		}
		
		// Select in the main table of the class
		else {			
			$query =  "SELECT {$propertyAlias} as propertyValue FROM {$table} WHERE uri = ?";
			$result	= $dbWrapper->execSql($query, array(
				$resource->uriResource
			));
			if($dbWrapper->dbConnector->errorNo() !== 0){
				throw new core_kernel_persistence_hardapi_Exception("Unable to get property (single) values for {$resource->uriResource} in {$table} : " .$dbWrapper->dbConnector->errorMsg());
			}
		
			while (!$result->EOF){
				if ($result->fields['propertyValue']!=null){
					$propertyValues[] = $result->fields['propertyValue'];
				}
				$result->moveNext();
			}
		}
		
		// Format output data
		foreach ($propertyValues as $propertyValue){
			if(!common_Utils::isUri($propertyValue)) {
                $returnValue[] = new core_kernel_classes_Literal($propertyValue);
	         } else {
                $returnValue[] = new core_kernel_classes_Resource($propertyValue);
            }
		}
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000129B end

        return (array) $returnValue;
    }

    /**
     * Short description of method getPropertyValuesCollection
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return core_kernel_classes_ContainerCollection
     */
    public function getPropertyValuesCollection( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000129F begin

		if(DEBUG_MODE){
			$returnValue->debug = __METHOD__;
		}
		
		$returnValue = new core_kernel_classes_ContainerCollection($resource);
		$values = $this->getPropertyValues ($resource, $property);
					
		foreach ($values as $value){
			if(DEBUG_MODE){
            	$value->debug = __METHOD__ .'|' . $property->debug;
            }
            $returnValue->add($value);
        }

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000129F end

        return $returnValue;
    }

    /**
     * Short description of method getOnePropertyValue
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  boolean last
     * @return core_kernel_classes_Container
     */
    public function getOnePropertyValue( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $last = false)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012A3 begin

		$options = array();
		if ($last){
			$options['last'] = true;
		} else {
			$options['one'] = true;
		}
		
		$value = $this->getPropertyValues ($resource, $property, $options);
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
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
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
		$returnValue = $this->getPropertyValues ($resource, $property, $options);
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012A9 end

        return $returnValue;
    }

    /**
     * Short description of method setPropertyValue
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
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
		        
    	$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
        $session 	= core_kernel_classes_Session::singleton();
        $object  = !is_string($object) && $object instanceof core_kernel_classes_Resource ? $object->uriResource : $object;
		$instanceId = null;
        $propertyValue = null;
       	$propertyForeignUri = null;
        $propertyRange = $property->getRange();
        
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
        
        // Get the table name
        $tableName = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->resourceLocation ($resource);
        
        // Get property instance
        $instanceId = core_kernel_persistence_hardsql_Utils::getInstanceId ($resource);

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
        
        if ($property->isMultiple() || $property->isLgDependent()){
        	
        	$query = "INSERT INTO {$tableName}Props 
        		(instance_id, property_uri, property_value, property_foreign_uri, l_language) 
        		VALUES (?, ?, ?, ?, ?)";
	        $result	= $dbWrapper->execSql($query, array(
	        	$instanceId, 
	        	$property->uriResource, 
	        	$propertyValue, 
	        	$propertyForeignUri, 
	        	$lang
	        ));
			if($dbWrapper->dbConnector->errorNo() !== 0){
				throw new core_kernel_persistence_hardapi_Exception("Unable to set property (single) Value for the instance {$resource->uriResource} : " .$dbWrapper->dbConnector->errorMsg());
			}
        } else {
        	
        	$propertyName = core_kernel_persistence_hardapi_Utils::getShortName ($property);
            $query = "UPDATE {$tableName} SET {$propertyName} = ? WHERE id = ?";
	        $result	= $dbWrapper->execSql($query, array(
	        	$propertyValue != null ? $propertyValue : $propertyForeignUri
	        	, $instanceId
	        ));
			if($dbWrapper->dbConnector->errorNo() !== 0){
				throw new core_kernel_persistence_hardapi_Exception("Unable to set property (multiple) Value for the instance {$resource->uriResource} : " .$dbWrapper->dbConnector->errorMsg());
			}
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012AE end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setPropertiesValues
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  array properties
     * @return boolean
     */
    public function setPropertiesValues( core_kernel_classes_Resource $resource, $properties)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012B3 begin
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012B3 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setPropertyValueByLg
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
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
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return boolean
     */
    public function removePropertyValues( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012BD begin

    	$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
        
        // Get the table name
        $tableName = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->resourceLocation ($resource);
        
        if ($property->isMultiple() || $property->isLgDependent()){
        	
        	$query = "DELETE `{$tableName}Props`.* FROM `{$tableName}`, `{$tableName}Props`
        		WHERE `{$tableName}`.uri = '{$resource->uriResource}' 
        		AND `{$tableName}Props`.property_uri = '{$property->uriResource}'
        		AND `{$tableName}`.id = `{$tableName}Props`.instance_id";
	        $result	= $dbWrapper->execSql($query);
			if($dbWrapper->dbConnector->errorNo() !== 0){
				throw new core_kernel_persistence_hardapi_Exception("Unable to delete property values (multiple) for the instance {$resource->uriResource} : " .$dbWrapper->dbConnector->errorMsg());
			} else {
				$returnValue = true;
			}
        } else {
        	
        	$propertyName = core_kernel_persistence_hardapi_Utils::getShortName ($property);
            $query = "UPDATE {$tableName} SET {$propertyName} = NULL WHERE uri = ?";
	        $result	= $dbWrapper->execSql($query, array(
	        	$resource->uriResource
	        ));
			if($dbWrapper->dbConnector->errorNo() !== 0){
				throw new core_kernel_persistence_hardapi_Exception("Unable to delete property values (single) for the instance {$resource->uriResource} : " .$dbWrapper->dbConnector->errorMsg());
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
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  string lg
     * @return boolean
     */
    public function removePropertyValueByLg( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $lg)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C1 begin
        
        $dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
        
        // Get the table name
        $tableName = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->resourceLocation ($resource);
        
        if ($property->isLgDependent()){
        	
        	$query = "DELETE `{$tableName}Props`.* FROM `{$tableName}`, `{$tableName}Props`
        		WHERE `{$tableName}`.uri = '{$resource->uriResource}' 
        		AND `{$tableName}Props`.property_uri = '{$property->uriResource}'
        		AND `{$tableName}`.id = `{$tableName}Props`.instance_id";
	        $result	= $dbWrapper->execSql($query);
			if($dbWrapper->dbConnector->errorNo() !== 0){
				throw new core_kernel_persistence_hardapi_Exception("Unable to delete property values (multiple) for the instance {$resource->uriResource} : " .$dbWrapper->dbConnector->errorMsg());
			} else {
				$returnValue = true;
			}
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C1 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getRdfTriples
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
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
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return array
     */
    public function getUsedLanguages( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C9 begin
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012C9 end

        return (array) $returnValue;
    }

    /**
     * Short description of method duplicate
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  array excludedProperties
     * @return core_kernel_classes_Resource
     */
    public function duplicate( core_kernel_classes_Resource $resource, $excludedProperties = array())
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012CD begin
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012CD end

        return $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
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

        // Delete the records in the main table  and the properties table
		$query = "DELETE {$tableName}.*, {$tableName}Props.* FROM {$tableName} M
			INNER JOIN {$tableName}Props.* P ON M.id = P.instance_id
			WHERE uri = ?";
        $returnValue = $dbWrapper->execSql($query, array($resource->uriResource));
        
        // Unreference the resource
        core_kernel_persistence_hardapi_ResourceReferencer::singleton()->unreferenceResource($resource);
        
//    	if($deleteReference){
//        	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
//        	$sqlQuery = "DELETE FROM statements WHERE object = '".$resource->uriResource."'";
//        	$returnValue = $dbWrapper->execSql($sqlQuery) && $returnValue;
//        }
		
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012D2 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getLastModificationDate
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return core_kernel_persistence_doc_date
     */
    public function getLastModificationDate( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
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
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
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
     * Short description of method getPropertiesValue
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  array properties
     * @param  boolean last
     * @return array
     */
    public function getPropertiesValue( core_kernel_classes_Resource $resource, $properties, $last)
    {
        $returnValue = array();

        // section 127-0-1-1-77557f59:12fa87873f4:-8000:00000000000014D1 begin
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1-77557f59:12fa87873f4:-8000:00000000000014D1 end

        return (array) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
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
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
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
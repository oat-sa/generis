<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 01.05.2011, 00:35:26 with ArgoUML PHP module
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
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
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
	 * @return array
	 */
	public function getPropertyValues( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
	{
		$returnValue = array();

		// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000129B begin
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
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
	public function getPropertyValuesCollection( core_kernel_classes_Resource $resource, core_kernel_classes_Property $property)
	{
		$returnValue = null;

		// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000129F begin

		if(DEBUG_MODE){
			$returnValue->debug = __METHOD__;
		}

		$returnValue = new core_kernel_classes_ContainerCollection($resource);
		$table = core_kernel_persistence_hardapi_TableManager::whereIsTheResource($resource);

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		 
		/*
		 *
		 * @todo manage the isMultiple case
		 *  || $property->isMultiple ()
		 *
		 */
		 
		 
		if ($property->isLgDependent()){
			// Attak the props table
			var_dump("isLgDepedent");
		}
		else {
			$propertyAlias = core_kernel_persistence_hardapi_Utils::getShortName($property);
			var_dump(core_kernel_persistence_hardapi_Utils::getShortName($property));
			$query =  "SELECT {$propertyAlias} FROM {$table}
			    		WHERE uri = ?";
			$result	= $dbWrapper->execSql($query, array(
				$resource->uriResource
			));
			if($dbWrapper->dbConnector->errorNo() !== 0){
				throw new core_kernel_persistence_hardapi_Exception("Unable to get Property Values Collection : " .$dbWrapper->dbConnector->errorMsg());
			}

			$propertiesValues = array();
			if (!$result->EOF){
				while ($row = $result->FetchRow()) {
					$propertiesValues[] = $row[$propertyAlias];
				}
			}
			
			foreach ($propertiesValues as $value){
	            if(!common_Utils::isUri($value)) {
	                $container = new core_kernel_classes_Literal($value);
	            }
	            else {
	                $container = new core_kernel_classes_Resource($value);
	            }
	
	            if(DEBUG_MODE){
	            	$container->debug = __METHOD__ .'|' . $property->debug;
	            }
	            $returnValue->add($container);
	        }
			
		}

		//        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");

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
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
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
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
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
	 * @return boolean
	 */
	public function setPropertyValue( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $object)
	{
		$returnValue = (bool) false;

		// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012AE begin
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
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
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
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
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
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
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
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
		throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
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
		 
		if (core_kernel_persistence_hardapi_TableManager::instanceExists($resource)){
			//var_dump("instance has bee, hardified {$resource->uriResource}");
			$returnValue = true;
		}

		//    	$dbWrapper = core_kernel_classes_DbWrapper::singleton(DATABASE_NAME);
		//        $hardSqlTable = null;
		//
		//        /****
		//         *
		//         * BE CAREFULL:
		//         * the database name is hard coded (mytao)
		//         *
		//         */
		//
		//        // Check if the hard sql tables exist
		//		$hardSqlTablesExistSql = "SELECT count(*) FROM information_schema.TABLES WHERE Table_Name='resource_to_table' and TABLE_SCHEMA='mytao'";
		//		$resulthardSqlTablesExist = $dbWrapper->execSql($hardSqlTablesExistSql);
		//
		//		if ($resulthardSqlTablesExist && $resulthardSqlTablesExist->fields[0]){
		//
		//			// Check if the resource has been hard sqled
		//			$isHardSqlResourceSql = "SELECT `table` FROM `resource_to_table` WHERE `uri`='{$resource->uriResource}'";
		//			$isHardSqlResourceResult = $dbWrapper->execSql($isHardSqlResourceSql);
		//			if ($isHardSqlResourceResult && !$isHardSqlResourceResult->EOF){
		//				$hardSqlTable = $isHardSqlResourceResult->fields['table'];
		//			}
		//
		//		}
		//
		//		if ($hardSqlTable){
		//			$returnValue = true;
		//		}

			// section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F5A end

			$returnValue = false;
			return (bool) $returnValue;
		}

} /* end of class core_kernel_persistence_hardsql_Resource */

?>
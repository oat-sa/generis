<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/persistence/class.ResourceProxy.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 19.04.2011, 15:13:18 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_persistence_PersistenceProxy
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/persistence/class.PersistenceProxy.php');

/**
 * include core_kernel_persistence_hard_sql_Resource
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/persistence/hard_sql/class.Resource.php');

/**
 * include core_kernel_persistence_ResourceInterface
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/persistence/interface.ResourceInterface.php');

/**
 * include core_kernel_persistence_smooth_sql_Resource
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/persistence/smooth_sql/class.Resource.php');

/* user defined includes */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012E3-includes begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012E3-includes end

/* user defined constants */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012E3-constants begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012E3-constants end

/**
 * Short description of class core_kernel_persistence_ResourceProxy
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence
 */
class core_kernel_persistence_ResourceProxy
    extends core_kernel_persistence_PersistenceProxy
        implements core_kernel_persistence_ResourceInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

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
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->getType ($resource);
        
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
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->getPropertyValues ($resource, $property);
        
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
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->getPropertyValuesCollection ($resource, $property);
        
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
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->getOnePropertyValue ($resource, $property, $last);
        
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
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->getPropertyValuesByLg ($resource, $property, $lg);
        
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
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->setPropertyValue ($resource, $property, $object);
        
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
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->setPropertiesValues ($resource, $properties);
        
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
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->setPropertyValueByLg ($resource, $property, $value, $lg);
        
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
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->removePropertyValues ($resource, $property);
        
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
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->removePropertyValueByLg ($resource, $property, $lg);
        
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
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->getRdfTriples ($resource);
        
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
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->getUsedLanguages ($resource, $property);
        
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
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->duplicate ($resource, $excludedProperties);
        
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
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->delete ($resource, $deleteReference);
        
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
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->getLastModificationDate ($resource, $property);
        
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
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->getLastModificationUser ($resource);
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012DC end

        return (string) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_persistence_PersistanceProxy
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000138E begin
        
        if (core_kernel_persistence_ResourceProxy::$instance == null){
        	core_kernel_persistence_ResourceProxy::$instance = new core_kernel_persistence_ResourceProxy();
        }
        $returnValue = core_kernel_persistence_ResourceProxy::$instance;
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000138E end

        return $returnValue;
    }

    /**
     * Short description of method isHardSql
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isHardSql( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001390 begin
        
        $dbWrapper = core_kernel_classes_DbWrapper::singleton(DATABASE_NAME);
        $hardSqlTable = null;
		
        // Check if the hard sql tables exist
		$hardSqlTablesExistSql = "SELECT count(*) FROM information_schema.TABLES WHERE Table_Name='resource_to_table' and TABLE_SCHEMA='mytao'";
		$resulthardSqlTablesExist = $dbWrapper->execSql($hardSqlTablesExistSql);
		
		if ($resulthardSqlTablesExist && $resulthardSqlTablesExist->fields[0]){

			// Check if the resource has been hard sqled
			$isHardSqlResourceSql = "SELECT `table` FROM `resource_to_table` WHERE `uri`='{$resource->uriResource}'";
			$isHardSqlResourceResult = $dbWrapper->execSql($isHardSqlResourceSql);
			if ($isHardSqlResourceResult && !$isHardSqlResourceResult->EOF){
				$hardSqlTable = $isHardSqlResourceResult->fields['table'];
			}
			
		}
		
		if ($hardSqlTable){
			$returnValue = true;
		}
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001390 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isSmoothSql
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isSmoothSql( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001393 begin
        
        $returnValue = true;
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001393 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isVirtuozo
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isVirtuozo( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001396 begin
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001396 end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_persistence_ResourceProxy */

?>
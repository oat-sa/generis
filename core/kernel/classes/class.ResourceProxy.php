<?php

error_reporting(E_ALL);

/**
 * TAO - core/kernel/classes/class.ResourceProxy.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 19.04.2011, 13:55:12 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_classes_PersistanceProxy
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/classes/class.PersistanceProxy.php');

/**
 * include core_kernel_classes_ResourceHardSql
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/classes/class.ResourceHardSql.php');

/**
 * include core_kernel_classes_ResourceSmoothSql
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/classes/class.ResourceSmoothSql.php');

/**
 * include core_kernel_classes_ResourceInterface
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/classes/interface.ResourceInterface.php');

/* user defined includes */
// section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D85-includes begin
// section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D85-includes end

/* user defined constants */
// section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D85-constants begin
// section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D85-constants end

/**
 * Short description of class core_kernel_classes_ResourceProxy
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 */
class core_kernel_classes_ResourceProxy
    extends core_kernel_classes_PersistanceProxy
        implements core_kernel_classes_ResourceInterface
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

        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D8E begin
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->getType ($resource);
        
        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D8E end

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

        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D90 begin
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->getPropertyValues ($resource, $property);
        
        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D90 end

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

        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D95 begin
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->getPropertyValuesCollection ($resource, $property);
        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D95 end

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

        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D97 begin
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->getOnePropertyValue ($resource, $property, $last);
        
        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D97 end

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

        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D9C begin
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->getPropertyValuesByLg ($resource, $property, $lg);
        
        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D9C end

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

        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002DA0 begin
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->setPropertyValue ($resource, $property, $object);
        
        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002DA0 end

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

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DA0 begin
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->setPropertiesValues ($resource, $properties);
        
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DA0 end

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

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DA3 begin
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->setPropertyValueByLg ($resource, $property, $value, $lg);
        
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DA3 end

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

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DA7 begin
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->removePropertyValues ($resource, $property);
        
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DA7 end

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

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DAA begin
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->removePropertyValueByLg ($resource, $property, $lg);
        
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DAA end

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

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DAE begin
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->getRdfTriples ($resource);
        
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DAE end

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

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DB0 begin
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->getUsedLanguages ($resource, $property);
        
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DB0 end

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

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DB3 begin
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->duplicate ($resource, $excludedProperties);
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DB3 end

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

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DB6 begin
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->delete ($resource, $deleteReference);
        
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DB6 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getLastModificationDate
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return doc_date
     */
    public function getLastModificationDate( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = null;

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DB9 begin
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->getLastModificationDate ($resource, $property);
        
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DB9 end

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

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DBD begin
        
        $delegate = $this->getClassToDelegateTo ($resource);
        $returnValue = $delegate->getLastModificationUser ($resource);
        
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DBD end

        return (string) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_classes_RessourceProxy
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--9398bc2:12f6a3d8694:-8000:0000000000002E5F begin
        
        if (core_kernel_classes_ResourceProxy::$instance == null){
        	core_kernel_classes_ResourceProxy::$instance = new core_kernel_classes_ResourceProxy();
        }
        $returnValue = core_kernel_classes_ResourceProxy::$instance;
        
        // section 127-0-1-1--9398bc2:12f6a3d8694:-8000:0000000000002E5F end

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

        // section 127-0-1-1-a9158b5:12f6c77139f:-8000:0000000000002E2F begin

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
        
        // section 127-0-1-1-a9158b5:12f6c77139f:-8000:0000000000002E2F end

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

        // section 127-0-1-1-a9158b5:12f6c77139f:-8000:0000000000002E32 begin
        
        $returnValue = true;
        
        // section 127-0-1-1-a9158b5:12f6c77139f:-8000:0000000000002E32 end

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

        // section 127-0-1-1-a9158b5:12f6c77139f:-8000:0000000000002E35 begin
        // section 127-0-1-1-a9158b5:12f6c77139f:-8000:0000000000002E35 end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_classes_ResourceProxy */

?>
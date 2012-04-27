<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/persistence/hardsql/class.Utils.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 27.04.2012, 08:21:21 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardsql
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--172a02e6:12fc17de14c:-8000:000000000000152F-includes begin
// section 127-0-1-1--172a02e6:12fc17de14c:-8000:000000000000152F-includes end

/* user defined constants */
// section 127-0-1-1--172a02e6:12fc17de14c:-8000:000000000000152F-constants begin
// section 127-0-1-1--172a02e6:12fc17de14c:-8000:000000000000152F-constants end

/**
 * Short description of class core_kernel_persistence_hardsql_Utils
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardsql
 */
class core_kernel_persistence_hardsql_Utils
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getInstanceId
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return string
     */
    public static function getInstanceId( core_kernel_classes_Resource $resource)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-53ffc1dd:131463d99b5:-8000:000000000000160E begin
        
    	$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
    	$table = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->resourceLocation ($resource);
    	$query = 'SELECT "id" FROM "'.$table.'" WHERE uri= ? LIMIT 1';
    	$result = $dbWrapper->execSql($query, array ($resource->uriResource));
    	if($dbWrapper->dbConnector->errorNo() !== 0){
			throw new core_kernel_persistence_hardsql_Exception("Unable to find the resource {$resource->uriResource} in {$table} : " .$dbWrapper->dbConnector->errorMsg());
		}
    	if(!$result->EOF){
    		$returnValue = $result->fields['id'];
    	}
        
        // section 127-0-1-1-53ffc1dd:131463d99b5:-8000:000000000000160E end

        return (string) $returnValue;
    }

    /**
     * Short description of method getResourceToTableId
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return string
     */
    public static function getResourceToTableId( core_kernel_classes_Resource $resource)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-53ffc1dd:131463d99b5:-8000:0000000000001611 begin
        
    	$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
    	$query = 'SELECT "id" FROM "resource_to_table" WHERE "uri"=?';
    	$result = $dbWrapper->execSql($query, array ($resource->uriResource));
    	
    	if($dbWrapper->dbConnector->errorNo() !== 0){
			throw new core_kernel_persistence_hardsql_Exception("Unable to find the class {$resource->uriResource} in resource_to_table : " .$dbWrapper->dbConnector->errorMsg());
		}
    	if (!$result->EOF){
    		$returnValue = $result->fields['id'];
    	}
    
        // section 127-0-1-1-53ffc1dd:131463d99b5:-8000:0000000000001611 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getClassId
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class class
     * @param  Resource resource
     * @return string
     */
    public static function getClassId( core_kernel_classes_Class $class,  core_kernel_classes_Resource $resource)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-53ffc1dd:131463d99b5:-8000:0000000000001614 begin
        
        $dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
    	$query = 'SELECT "id" FROM "class_to_table" WHERE "uri"=? AND "table"=?';
    	$result = $dbWrapper->execSql($query, array (
    		$class->uriResource
    		, core_kernel_persistence_hardapi_ResourceReferencer::singleton()->resourceLocation ($resource)
    	));
    	
    	if($dbWrapper->dbConnector->errorNo() !== 0){
			throw new core_kernel_persistence_hardsql_Exception("Unable to find the class {$class->uriResource} in class_to_table : " .$dbWrapper->dbConnector->errorMsg());
		}
    	
    	if (!$result->EOF){
    		$returnValue = $result->fields['id'];
    	}
        
        // section 127-0-1-1-53ffc1dd:131463d99b5:-8000:0000000000001614 end

        return (string) $returnValue;
    }

} /* end of class core_kernel_persistence_hardsql_Utils */

?>
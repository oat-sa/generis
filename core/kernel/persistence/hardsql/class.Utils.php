<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/persistence/hardsql/class.Utils.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 05.05.2011, 20:51:56 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
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
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardsql
 */
class core_kernel_persistence_hardsql_Utils
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    public function getInstanceId ($resource){
    	$returnValue = null;
    	
    	$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
    	$table = core_kernel_persistence_hardapi_ResourceReferencer::singleton()->resourceLocation ($resource);
    	$query = "SELECT id from {$table} WHERE uri=?";
    	$result = $dbWrapper->execSql($query, array ($resource->uriResource));
    	if($dbWrapper->dbConnector->errorNo() !== 0){
			throw new core_kernel_persistence_hardapi_Exception("Unable to find the resource {$resource->uriResource} in {$table} : " .$dbWrapper->dbConnector->errorMsg());
		}
    	if (!$result->EOF){
    		$returnValue = $result->fields['id'];
    	}
    	
    	return $returnValue;
    }
	
} /* end of class core_kernel_persistence_hardsql_Utils */

?>
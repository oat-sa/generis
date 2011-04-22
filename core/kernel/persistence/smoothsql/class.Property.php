<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 21.04.2011, 13:09:33 with ArgoUML PHP module
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_smoothsql
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
 * include core_kernel_persistence_PropertyInterface
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/persistence/interface.PropertyInterface.php');

/* user defined includes */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139A-includes begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139A-includes end

/* user defined constants */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139A-constants begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139A-constants end

/**
 * Short description of class core_kernel_persistence_smoothsql_Property
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_smoothsql
 */
class core_kernel_persistence_smoothsql_Property
extends core_kernel_persistence_PersistenceImpl
implements core_kernel_persistence_PropertyInterface
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
	 * Short description of method getSubProperties
	 *
	 * @access public
	 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
	 * @param  Resource resource
	 * @param  boolean recursive
	 * @return array
	 */
	public function getSubProperties( core_kernel_classes_Resource $resource, $recursive = false)
	{
		$returnValue = array();

		// section 127-0-1-1-563beb61:12f77be445a:-8000:000000000000144D begin
		
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlQuery = "select subject from statements where predicate = 'http://www.w3.org/2000/01/rdf-schema#subPropertyOf' and object = '".$resource->uriResource."'";
		$returnValue = array();
		$sqlResult = $dbWrapper->execSql($sqlQuery);
		while (!$sqlResult-> EOF){
			$property = new core_kernel_classes_Property($sqlResult->fields['subject']);
			$returnValue[$property->uriResource] = $property;

			if($recursive == true) {
				$returnValue = array_merge($returnValue,$property->getSubProperties(true));
			}
			$sqlResult->MoveNext();
		}

		// section 127-0-1-1-563beb61:12f77be445a:-8000:000000000000144D end

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

		// section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001497 begin

		if (core_kernel_persistence_PropertyProxy::$instance == null){
			core_kernel_persistence_PropertyProxy::$instance = new core_kernel_persistence_PropertyProxy();
		}
		$returnValue = core_kernel_persistence_PropertyProxy::$instance;

		// section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001497 end

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

		// section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F51 begin

		$returnValue = true;

		// section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F51 end

		return (bool) $returnValue;
	}

} /* end of class core_kernel_persistence_smoothsql_Property */

?>
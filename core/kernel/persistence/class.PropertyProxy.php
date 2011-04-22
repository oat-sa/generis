<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/persistence/class.PropertyProxy.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 21.04.2011, 13:09:34 with ArgoUML PHP module 
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
 * include core_kernel_persistence_hardsql_Property
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/persistence/hardsql/class.Property.php');

/**
 * include core_kernel_persistence_PropertyInterface
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/persistence/interface.PropertyInterface.php');

/**
 * include core_kernel_persistence_smoothsql_Property
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/persistence/smoothsql/class.Property.php');

/**
 * include core_kernel_persistence_subscription_Property
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/persistence/subscription/class.Property.php');

/* user defined includes */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139D-includes begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139D-includes end

/* user defined constants */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139D-constants begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139D-constants end

/**
 * Short description of class core_kernel_persistence_PropertyProxy
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence
 */
class core_kernel_persistence_PropertyProxy
    extends core_kernel_persistence_PersistenceProxy
        implements core_kernel_persistence_PropertyInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access public
     * @var PersistanceProxy
     */
    public static $instance = null;

    /**
     * Short description of attribute ressourcesDelegatedTo
     *
     * @access public
     * @var array
     */
    public static $ressourcesDelegatedTo = array();

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
        // section 127-0-1-1-563beb61:12f77be445a:-8000:000000000000144D end

        return (array) $returnValue;
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

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001401 begin
        
        if (core_kernel_persistence_PropertyProxy::$instance == null){
        	core_kernel_persistence_PropertyProxy::$instance = new core_kernel_persistence_PropertyProxy();
        }
        $returnValue = core_kernel_persistence_PropertyProxy::$instance;
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001401 end

        return $returnValue;
    }

    /**
     * Short description of method getImpToDelegateTo
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  array params
     * @return core_kernel_persistence_ResourceInterface
     */
    public function getImpToDelegateTo( core_kernel_classes_Resource $resource, $params = array())
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F63 begin
        
        if (!isset(core_kernel_persistence_PropertyProxy::$ressourcesDelegatedTo[$resource->uriResource])) {
        	
        	$delegate = null;
        
            if ($this->isValidContext ('subscription', $resource)) {
	        	$delegate = core_kernel_persistence_subscription_Property::singleton();
	        }
            else if ($this->isValidContext ('hardsql', $resource)) {
	        	$delegate = core_kernel_persistence_hardsql_Property::singleton();
	        }
            else if ($this->isValidContext ('virtuozo', $resource)) {
	        	$delegate = core_kernel_persistence_virtuozo_Property::singleton();
	        }
            else if ($this->isValidContext ('smoothsql', $resource)) {
	        	$delegate = core_kernel_persistence_smoothsql_Property::singleton();
	        }
	        
	        core_kernel_persistence_PropertyProxy::$ressourcesDelegatedTo[$resource->uriResource] = $delegate;
        }
        
        $returnValue = core_kernel_persistence_PropertyProxy::$ressourcesDelegatedTo[$resource->uriResource];
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F63 end

        return $returnValue;
    }

    /**
     * Short description of method isValidContext
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string context
     * @param  Resource resource
     * @return boolean
     */
    public function isValidContext($context,  core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000155E begin
        
        $impls = $this->getAvailableImpl ();
    	$className = "core_kernel_persistence_".$context."_Property";
    	
    	if (isset($impls["$context"])
        && $impls["$context"] 
        && $className::singleton()->isValidContext($resource))
        {
    		$returnValue = true;
    	}
        
        // section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000155E end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_persistence_PropertyProxy */

?>
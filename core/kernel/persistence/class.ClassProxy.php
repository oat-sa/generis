<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/persistence/class.ClassProxy.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 20.04.2011, 17:29:04 with ArgoUML PHP module 
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
 * include core_kernel_persistence_hardsql_Class
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/persistence/hardsql/class.Class.php');

/**
 * include core_kernel_persistence_ClassInterface
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/persistence/interface.ClassInterface.php');

/**
 * include core_kernel_persistence_smoothsql_Class
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/persistence/smoothsql/class.Class.php');

/**
 * include core_kernel_persistence_subscription_Class
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/persistence/subscription/class.Class.php');

/* user defined includes */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139B-includes begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139B-includes end

/* user defined constants */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139B-constants begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000139B-constants end

/**
 * Short description of class core_kernel_persistence_ClassProxy
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence
 */
class core_kernel_persistence_ClassProxy
    extends core_kernel_persistence_PersistenceProxy
        implements core_kernel_persistence_ClassInterface
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
     * Short description of method getSubClasses
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getSubClasses( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014EB begin
        
        $delegate = $this->getImpToDelegateTo ($resource);
        $returnValue = $delegate->getSubClasses ($resource, $recursive);
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014EB end

        return (array) $returnValue;
    }

    /**
     * Short description of method isSubClassOf
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Class parentClass
     * @return boolean
     */
    public function isSubClassOf( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $parentClass)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F0 begin
        
        $delegate = $this->getImpToDelegateTo ($resource);
        $returnValue = $delegate->isSubClassOf ($resource, $parentClass);
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F0 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getParentClasses
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getParentClasses( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F5 begin
        
        $delegate = $this->getImpToDelegateTo ($resource);
        $returnValue = $delegate->getParentClasses ($resource, $recursive);
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F5 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getProperties
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getProperties( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014FA begin
        
        $delegate = $this->getImpToDelegateTo ($resource);
        $returnValue = $delegate->getProperties ($resource, $recursive);
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014FA end

        return (array) $returnValue;
    }

    /**
     * Short description of method getInstances
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getInstances( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001500 begin
        
        $delegate = $this->getImpToDelegateTo ($resource);
        $returnValue = $delegate->getInstances ($resource, $recursive);
    
        if ($this->isValidContext ('subscription', $resource)){
        	$delegate = core_kernel_persistence_subscription_Class::singleton();
        	$subscriptionValue = $delegate->getInstances ($resource, $recursive);
        	$returnValue = array_merge ($returnValue, $subscriptionValue);
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001500 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setInstance
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Resource instance
     * @return core_kernel_classes_Resource
     */
    public function setInstance( core_kernel_classes_Resource $resource,  core_kernel_classes_Resource $instance)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001506 begin
        
        $delegate = $this->getImpToDelegateTo ($resource);
        $returnValue = $delegate->setInstance ($resource, $instance);
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001506 end

        return $returnValue;
    }

    /**
     * Short description of method setSubClassOf
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Class iClass
     * @return boolean
     */
    public function setSubClassOf( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $iClass)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000150F begin
        
        $delegate = $this->getImpToDelegateTo ($resource);
        $returnValue = $delegate->setSubClassOf ($resource, $iClass);
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000150F end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setProperty
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return boolean
     */
    public function setProperty( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001512 begin
        
        $delegate = $this->getImpToDelegateTo ($resource);
        $returnValue = $delegate->setProperty ($resource, $property);
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001512 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method createInstance
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
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
        
        $delegate = $this->getImpToDelegateTo ($resource);
        $returnValue = $delegate->createInstance ($resource, $label, $comment, $uri);
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F27 end

        return $returnValue;
    }

    /**
     * Short description of method createSubClass
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @return core_kernel_classes_Class
     */
    public function createSubClass( core_kernel_classes_Resource $resource, $label = '', $comment = '')
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F32 begin
        
        $delegate = $this->getImpToDelegateTo ($resource);
        $returnValue = $delegate->createSubClass ($resource, $label, $comment);
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F32 end

        return $returnValue;
    }

    /**
     * Short description of method createProperty
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
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
        
        $delegate = $this->getImpToDelegateTo ($resource);
        $returnValue = $delegate->createProperty ($resource, $label, $comment, $isLgDependent);
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F3C end

        return $returnValue;
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

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013FF begin
        
        if (core_kernel_persistence_ClassProxy::$instance == null){
        	core_kernel_persistence_ClassProxy::$instance = new core_kernel_persistence_ClassProxy();
        }
        $returnValue = core_kernel_persistence_ClassProxy::$instance;
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013FF end

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

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F60 begin

        $impls = $this->getAvailableImpl ($params);
        
        // First access to the resource
        
        if (!isset(core_kernel_persistence_ClassProxy::$ressourcesDelegatedTo[$resource->uriResource])) {
        	
        	$delegate = null;
        
            if ($this->isValidContext ('subscription', $resource)) {
	        	$delegate = core_kernel_persistence_subscription_Class::singleton();
	        }
            else if ($this->isValidContext ('hardsql', $resource)) {
	        	$delegate = core_kernel_persistence_hardsql_Class::singleton();
	        }
            else if ($this->isValidContext ('virtuozo', $resource)) {
	        	$delegate = core_kernel_persistence_virtuozo_Class::singleton();
	        }
            else if ($this->isValidContext ('smoothsql', $resource)) {
	        	$delegate = core_kernel_persistence_smoothsql_Class::singleton();
	        }
	        
	        core_kernel_persistence_ClassProxy::$ressourcesDelegatedTo[$resource->uriResource] = $delegate;
        }
        
        $returnValue = core_kernel_persistence_ClassProxy::$ressourcesDelegatedTo[$resource->uriResource];
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F60 end

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

        // section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000155B begin
        
        $impls = $this->getAvailableImpl ();

    	if (isset($impls["$context"]) && $impls["$context"])
		{
			switch ($context){
				case 'subscription':
					if (core_kernel_persistence_subscription_Class::singleton()->isValidContext($resource)){
						$returnValue = true;
					}
					break;
				case 'hardsql':
					if (core_kernel_persistence_hardsql_Class::singleton()->isValidContext($resource)){
						$returnValue = true;
					}
					break;
				case 'virtuozo':
					if (core_kernel_persistence_virtuozo_Class::singleton()->isValidContext($resource)){
						$returnValue = true;
					}
					break;
				case 'smoothsql':
					if (core_kernel_persistence_smoothsql_Class::singleton()->isValidContext($resource)){
						$returnValue = true;
					}
					break;
			}
		}
        
        // section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000155B end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_persistence_ClassProxy */

?>
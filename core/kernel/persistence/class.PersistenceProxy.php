<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 19.04.2011, 15:05:02 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012E4-includes begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012E4-includes end

/* user defined constants */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012E4-constants begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000012E4-constants end

/**
 * Short description of class core_kernel_persistence_PersistenceProxy
 *
 * @abstract
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence
 */
abstract class core_kernel_persistence_PersistenceProxy
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute ressourcesDelegatedTo
     *
     * @access public
     * @var array
     */
    public static $ressourcesDelegatedTo = array();

    /**
     * Short description of attribute instance
     *
     * @access public
     * @var PersistanceProxy
     */
    public static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method getClassToDelegateTo
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return core_kernel_persistence_ResourceInterface
     */
    public function getClassToDelegateTo( core_kernel_classes_Resource $resource)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001304 begin
        
        // First access to the resource
        if (!isset(core_kernel_persistence_PersistenceProxy::$ressourcesDelegatedTo[$resource->uriResource])) {
        	$delegate = null;
            if ($this->isHardSql($resource)){
	        	$delegate = core_kernel_persistence_hard_sql_Resource::singleton();
	        } 
	        else if ($this->isVirtuozo($resource)){
	        	$delegate = core_kernel_persistence_virtuozo_Resource::singleton();
	        }
	        else if ($this->isSmoothSql($resource)){
	        	$delegate = core_kernel_persistence_smooth_sql_Resource::singleton();
	        }
	        core_kernel_persistence_PersistenceProxy::$ressourcesDelegatedTo[$resource->uriResource] = $delegate;
        }
        
        $returnValue = core_kernel_persistence_PersistenceProxy::$ressourcesDelegatedTo[$resource->uriResource];
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001304 end

        return $returnValue;
    }

    /**
     * Short description of method setClassToDelegateTo
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource ressource
     * @param  ResourceInterface classToDelegateTo
     * @return mixed
     */
    public function setClassToDelegateTo( core_kernel_classes_Resource $ressource,  core_kernel_persistence_ResourceInterface $classToDelegateTo)
    {
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001307 begin
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001307 end
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

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000130B begin
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000130B end

        return $returnValue;
    }

    /**
     * Short description of method isHardSql
     *
     * @abstract
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public abstract function isHardSql( core_kernel_classes_Resource $resource);

    /**
     * Short description of method isSmoothSql
     *
     * @abstract
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public abstract function isSmoothSql( core_kernel_classes_Resource $resource);

    /**
     * Short description of method isVirtuozo
     *
     * @abstract
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public abstract function isVirtuozo( core_kernel_classes_Resource $resource);

} /* end of abstract class core_kernel_persistence_PersistenceProxy */

?>
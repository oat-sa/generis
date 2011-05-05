<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 05.05.2011, 12:50:11 with ArgoUML PHP module 
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
     * Short description of attribute impls
     *
     * @access public
     * @var array
     */
    public $impls = array();

    /**
     * Short description of attribute mode
     *
     * @access public
     * @var string
     */
    public static $mode = '';

    // --- OPERATIONS ---

    /**
     * Short description of method getImpToDelegateTo
     *
     * @abstract
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  array params
     * @return core_kernel_persistence_ResourceInterface
     */
    public abstract function getImpToDelegateTo( core_kernel_classes_Resource $resource, $params = array());

    /**
     * Short description of method singleton
     *
     * @abstract
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_persistence_PersistanceProxy
     */
    public static abstract function singleton();

    /**
     * Short description of method getAvailableImpl
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array params
     * @return array
     */
    public function getAvailableImpl($params = array())
    {
        $returnValue = array();

        // section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000147C begin
        
        $returnValue = array(
        	PERSISTENCE_HARD => true, 
        	PERSISTENCE_SMOOTH => true, 
        	PERSISTENCE_VIRTUOZO => false, 
        	PERSISTENCE_SUBSCRIPTION => false
       	);
        
        if (self::isForcedMode()){
        	
        	$returnValue = array (
        		self::$mode => true
        	);
        } else if (count ($params)){
        	
        	$returnValue = array_merge ($returnValue, $params);
        }
        // section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000147C end

        return (array) $returnValue;
    }

    /**
     * Short description of method isValidContext
     *
     * @abstract
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string context
     * @param  Resource resource
     * @return boolean
     */
    public abstract function isValidContext($context,  core_kernel_classes_Resource $resource);

    /**
     * Short description of method setMode
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string mode
     * @return mixed
     */
    public static function setMode($mode)
    {
        // section 127-0-1-1-7a0c731b:12fbfab7535:-8000:000000000000153C begin
        
    	if (isset($mode) && !empty($mode)){
    		self::$mode = $mode;
    	}
    	
        // section 127-0-1-1-7a0c731b:12fbfab7535:-8000:000000000000153C end
    }

    /**
     * Short description of method isForcedMode
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    public static function isForcedMode()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a0c731b:12fbfab7535:-8000:000000000000153F begin
        
        if (!empty(self::$mode)){
        	$returnValue = true;
        }
        
        // section 127-0-1-1-7a0c731b:12fbfab7535:-8000:000000000000153F end

        return (bool) $returnValue;
    }

    /**
     * Short description of method resetMode
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return mixed
     */
    public function resetMode()
    {
        // section 127-0-1-1-7a0c731b:12fbfab7535:-8000:0000000000001545 begin
   	
   		self::$mode = "";
    		
        // section 127-0-1-1-7a0c731b:12fbfab7535:-8000:0000000000001545 end
    }

} /* end of abstract class core_kernel_persistence_PersistenceProxy */

?>
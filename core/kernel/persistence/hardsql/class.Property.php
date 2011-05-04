<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 03.05.2011, 17:03:36 with ArgoUML PHP module 
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
 * include core_kernel_persistence_PropertyInterface
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/persistence/interface.PropertyInterface.php');

/* user defined includes */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A8-includes begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A8-includes end

/* user defined constants */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A8-constants begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A8-constants end

/**
 * Short description of class core_kernel_persistence_hardsql_Property
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardsql
 */
class core_kernel_persistence_hardsql_Property
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

        // section 127-0-1-1-7b8668ff:12f77d22c39:-8000:000000000000144D begin
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1-7b8668ff:12f77d22c39:-8000:000000000000144D end

        return (array) $returnValue;
    }

    /**
     * Short description of method isLgDependent
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isLgDependent( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--bedeb7e:12fb15494a5:-8000:00000000000014DB begin
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1--bedeb7e:12fb15494a5:-8000:00000000000014DB end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isMultiple
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isMultiple( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--bedeb7e:12fb15494a5:-8000:00000000000014DD begin
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1--bedeb7e:12fb15494a5:-8000:00000000000014DD end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getRange
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return core_kernel_classes_ContainerCollection
     */
    public function getRange( core_kernel_classes_Resource $resource)
    {
        $returnValue = null;

        // section 127-0-1-1-69f26b71:12fb6440cb7:-8000:00000000000014E9 begin
        throw new core_kernel_persistence_ProhibitedFunctionException("not implemented => The function (".__METHOD__.") is not available in this persistence implementation (".__CLASS__.")");
        // section 127-0-1-1-69f26b71:12fb6440cb7:-8000:00000000000014E9 end

        return $returnValue;
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

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001499 begin
        
        if (core_kernel_persistence_hardsql_Property::$instance == null){
        	core_kernel_persistence_hardsql_Property::$instance = new core_kernel_persistence_hardsql_Property();
        }
        $returnValue = core_kernel_persistence_hardsql_Property::$instance;
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001499 end

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

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F54 begin
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F54 end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_persistence_hardsql_Property */

?>
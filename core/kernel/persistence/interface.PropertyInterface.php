<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 05.05.2011, 12:38:01 with ArgoUML PHP module 
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
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A0-includes begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A0-includes end

/* user defined constants */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A0-constants begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000013A0-constants end

/**
 * Short description of class core_kernel_persistence_PropertyInterface
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence
 */
interface core_kernel_persistence_PropertyInterface
{


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
    public function getSubProperties( core_kernel_classes_Resource $resource, $recursive = false);

    /**
     * Short description of method isLgDependent
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isLgDependent( core_kernel_classes_Resource $resource);

    /**
     * Short description of method isMultiple
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isMultiple( core_kernel_classes_Resource $resource);

    /**
     * Short description of method getRange
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return core_kernel_classes_Class
     */
    public function getRange( core_kernel_classes_Resource $resource);

} /* end of interface core_kernel_persistence_PropertyInterface */

?>
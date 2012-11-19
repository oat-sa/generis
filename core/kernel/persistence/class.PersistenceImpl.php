<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 19.11.2012, 16:36:31 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_persistence
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001403-includes begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001403-includes end

/* user defined constants */
// section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001403-constants begin
// section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001403-constants end

/**
 * Short description of class core_kernel_persistence_PersistenceImpl
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_persistence
 */
abstract class core_kernel_persistence_PersistenceImpl
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000140F begin
        throw new Exception('Must be implemented by subclasses.');
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000140F end

        return $returnValue;
    }

    /**
     * Short description of method isValidContext
     *
     * @abstract
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public abstract function isValidContext( core_kernel_classes_Resource $resource);

} /* end of abstract class core_kernel_persistence_PersistenceImpl */

?>
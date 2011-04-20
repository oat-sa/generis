<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 20.04.2011, 14:33:25 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_subscription
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
// section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000140C-includes begin
// section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000140C-includes end

/* user defined constants */
// section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000140C-constants begin
// section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000140C-constants end

/**
 * Short description of class core_kernel_persistence_subscription_Property
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_subscription
 */
class core_kernel_persistence_subscription_Property
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
     * Short description of method singleton
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000142B begin
        
        if (core_kernel_persistence_subscription_Property::$instance == null){
        	core_kernel_persistence_subscription_Property::$instance = new core_kernel_persistence_subscription_Property();
        }
        $returnValue = core_kernel_persistence_subscription_Property::$instance;
        
        // section 127-0-1-1--499759bc:12f72c12020:-8000:000000000000142B end

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

        // section 127-0-1-1--499759bc:12f72c12020:-8000:0000000000001431 begin
        // section 127-0-1-1--499759bc:12f72c12020:-8000:0000000000001431 end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_persistence_subscription_Property */

?>
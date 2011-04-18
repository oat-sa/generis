<?php

error_reporting(E_ALL);

/**
 * TAO - core/kernel/classes/class.PersistanceProxy.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 18.04.2011, 22:47:33 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002E6F-includes begin
// section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002E6F-includes end

/* user defined constants */
// section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002E6F-constants begin
// section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002E6F-constants end

/**
 * Short description of class core_kernel_classes_PersistanceProxy
 *
 * @abstract
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 */
abstract class core_kernel_classes_PersistanceProxy
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
     * @return core_kernel_classes_ResourceInterface
     */
    public function getClassToDelegateTo( core_kernel_classes_Resource $resource)
    {
        $returnValue = null;

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002E7F begin
        
//        if (isset($this->ressourcesDelegatedTo[$resource->uriResource])){
//        	$returnValue = $this->ressourcesDelegatedTo[$resource->uriResource];
//        } else {
//        	
//        }
        
        $returnValue = core_kernel_classes_ResourceHardSql::singleton();
        
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002E7F end

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
    public function setClassToDelegateTo( core_kernel_classes_Resource $ressource,  core_kernel_classes_ResourceInterface $classToDelegateTo)
    {
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002E8D begin
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002E8D end
    }

    /**
     * Short description of method singleton
     *
     * @abstract
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_classes_PersistanceProxy
     */
    public static abstract function singleton();

} /* end of abstract class core_kernel_classes_PersistanceProxy */

?>
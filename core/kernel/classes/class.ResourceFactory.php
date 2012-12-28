<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\classes\class.ResourceFactory.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 28.12.2012, 09:40:41 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1--99--2e5efe17:11fffe7b282:-8000:0000000000001518-includes begin
// section 10-13-1--99--2e5efe17:11fffe7b282:-8000:0000000000001518-includes end

/* user defined constants */
// section 10-13-1--99--2e5efe17:11fffe7b282:-8000:0000000000001518-constants begin
// section 10-13-1--99--2e5efe17:11fffe7b282:-8000:0000000000001518-constants end

/**
 * Short description of class core_kernel_classes_ResourceFactory
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 */
class core_kernel_classes_ResourceFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method create
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Class type
     * @param  string label
     * @param  string comment
     * @return core_kernel_classes_Resource
     */
    public static function create( core_kernel_classes_Class $type, $label = '', $comment = '')
    {
        $returnValue = null;

        // section 10-13-1--99--2e5efe17:11fffe7b282:-8000:0000000000001519 begin
        $propertiesValues = array();
        
        if (!empty($label)){
        	$propertiesValues[RDFS_LABEL] = $label;
        }
        
        if (!empty($comment)){
        	$propertiesValues[RDFS_COMMENT] = $comment;
        }
        
		$returnValue = $type->createInstanceWithProperties($propertiesValues);
        // section 10-13-1--99--2e5efe17:11fffe7b282:-8000:0000000000001519 end

        return $returnValue;
    }

} /* end of class core_kernel_classes_ResourceFactory */

?>
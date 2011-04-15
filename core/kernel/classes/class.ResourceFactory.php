<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\classes\class.ResourceFactory.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 24.03.2010, 14:36:14 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
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
 * @author firstname and lastname of author, <author@example.org>
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
     * @author firstname and lastname of author, <author@example.org>
     * @param  Class type
     * @param  string label
     * @param  string comment
     * @return core_kernel_classes_Resource
     */
    public static function create( core_kernel_classes_Class $type, $label = 'Resource Default Label', $comment = 'Resource Default Comment')
    {
        $returnValue = null;

        // section 10-13-1--99--2e5efe17:11fffe7b282:-8000:0000000000001519 begin
        $returnValue = $type->createInstance($label,$comment);
        // section 10-13-1--99--2e5efe17:11fffe7b282:-8000:0000000000001519 end

        return $returnValue;
    }

} /* end of class core_kernel_classes_ResourceFactory */

?>
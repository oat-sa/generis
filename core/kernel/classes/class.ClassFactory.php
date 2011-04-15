<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/classes/class.ClassFactory.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 08.11.2010, 19:02:43 with ArgoUML PHP module
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-3c0ae01:12c2c9debde:-8000:000000000000138C-includes begin
// section 127-0-1-1-3c0ae01:12c2c9debde:-8000:000000000000138C-includes end

/* user defined constants */
// section 127-0-1-1-3c0ae01:12c2c9debde:-8000:000000000000138C-constants begin
// section 127-0-1-1-3c0ae01:12c2c9debde:-8000:000000000000138C-constants end

/**
 * Short description of class core_kernel_classes_ClassFactory
 *
 * @access public
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 */
class core_kernel_classes_ClassFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method createInstance
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Class clazz
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Resource
     */
    public static function createInstance( core_kernel_classes_Class $clazz, $label = '', $comment = '' , $uri = '')
    {
        $returnValue = null;

        // section 127-0-1-1-3c0ae01:12c2c9debde:-8000:000000000000138D begin
        if($uri != '') {
            core_kernel_impl_ApiModelOO::singleton()->setStatement($uri,RDF_TYPE,$clazz->uriResource,'', '');
            $returnValue = new core_kernel_classes_Resource($uri);
            if ($label != '') {
                $returnValue->setLabel($label);
            }
            if( $comment != '') {
                $returnValue->setComment($comment);
            }
        }
        // section 127-0-1-1-3c0ae01:12c2c9debde:-8000:000000000000138D end

        return $returnValue;
    }

    /**
     * Short description of method createProperty
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Class clazz
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Property
     */
    public static function createProperty( core_kernel_classes_Class $clazz, $label = '', $comment = '' , $isLgDependent = false, $uri = '')
    {
        $returnValue = null;

        // section 127-0-1-1-3c0ae01:12c2c9debde:-8000:0000000000001393 begin
        if($uri != ''){
            $property = new core_kernel_classes_Class(RDF_PROPERTY);
            $propertyInstance = self::createInstance($property,$label,$comment,$uri);
            $returnValue = new core_kernel_classes_Property($propertyInstance->uriResource);
            if(!$clazz->setProperty($returnValue)){
                throw new common_Exception('proplem creating property');
            }
        }
        // section 127-0-1-1-3c0ae01:12c2c9debde:-8000:0000000000001393 end

        return $returnValue;
    }

    /**
     * Short description of method createSubClass
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Class clazz
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public static function createSubClass( core_kernel_classes_Class $clazz, $label = '', $comment = '' , $uri = '')
    {
        $returnValue = null;

        // section 127-0-1-1-3c0ae01:12c2c9debde:-8000:00000000000013A2 begin
        if($uri != ''){
            $class = new core_kernel_classes_Class(RDF_CLASS);
            $intance =  self::createInstance($class,$label,$comment,$uri);
            $returnValue = new core_kernel_classes_Class($intance->uriResource);
            $returnValue->setSubClassOf($clazz);
        }
        // section 127-0-1-1-3c0ae01:12c2c9debde:-8000:00000000000013A2 end

        return $returnValue;
    }

} /* end of class core_kernel_classes_ClassFactory */

?>
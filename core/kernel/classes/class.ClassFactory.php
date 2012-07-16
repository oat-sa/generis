<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/classes/class.ClassFactory.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 16.07.2012, 18:31:46 with ArgoUML PHP module 
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
    public static function createInstance( core_kernel_classes_Class $clazz, $label, $comment, $uri)
    {
        $returnValue = null;

        // section 127-0-1-1-3c0ae01:12c2c9debde:-8000:000000000000138D begin
		$newUri = self::checkProvidedUri($uri);
		$newResource = new core_kernel_classes_Class($newUri);
		$propertiesValues = array(RDF_TYPE => $clazz->uriResource);
		if ($label != '') {
			$propertiesValues[RDFS_LABEL] = $label;
		}
		if( $comment != '') {
			$propertiesValues[RDFS_COMMENT] = $comment;
		}
		$check = $newResource->setPropertiesValues($propertiesValues);
		if($check){
			$returnValue =$newResource;
		}
		else{
			common_Logger::e('Fail to create instance of ' . $clazz . ' null returned');
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
     * @param  boolean isLgDependent
     * @param  string uri
     * @return core_kernel_classes_Property
     */
    public static function createProperty( core_kernel_classes_Class $clazz, $label, $comment, $isLgDependent, $uri)
    {
        $returnValue = null;

        // section 127-0-1-1-3c0ae01:12c2c9debde:-8000:0000000000001393 begin
		$newUri = self::checkProvidedUri($uri);
		$property = new core_kernel_classes_Class(RDF_PROPERTY);
		$propertyInstance = self::createInstance($property,$label,$comment,$newUri);
		$returnValue = new core_kernel_classes_Property($propertyInstance->uriResource);
		if(!$clazz->setProperty($returnValue)){
			throw new common_Exception('proplem creating property');
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
    public static function createSubClass( core_kernel_classes_Class $clazz, $label, $comment, $uri)
    {
        $returnValue = null;

        // section 127-0-1-1-3c0ae01:12c2c9debde:-8000:00000000000013A2 begin
		$newUri = self::checkProvidedUri($uri);
		$class = new core_kernel_classes_Class(RDF_CLASS);
		$intance =  self::createInstance($class,$label,$comment,$newUri);
		$returnValue = new core_kernel_classes_Class($intance->uriResource);
		$returnValue->setSubClassOf($clazz);

        // section 127-0-1-1-3c0ae01:12c2c9debde:-8000:00000000000013A2 end

        return $returnValue;
    }

    /**
     * Short description of method checkProvidedUri
     *
     * @access private
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  string uri
     * @return string
     */
    private static function checkProvidedUri($uri)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-706d7d33:138909bcd61:-8000:0000000000001B14 begin
        if($uri != '') {
        	if(common_Utils::isUri($uri)){
        		$returnValue = $uri;
        	}
        	else{
        		throw new common_Exception('Could not creates new Resource, bad uri provided : ' . $uri);
        	}
        }
        else{
        	$returnValue = common_Utils::getNewUri();
        }
        // section 127-0-1-1-706d7d33:138909bcd61:-8000:0000000000001B14 end

        return (string) $returnValue;
    }

} /* end of class core_kernel_classes_ClassFactory */

?>
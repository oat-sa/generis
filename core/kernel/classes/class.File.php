<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/classes/class.File.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 05.11.2010, 14:01:25 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Resource implements rdf:resource container identified by an uri (a string).
 * Methods enable meta data management for this resource
 *
 * @author patrick.plichart@tudor.lu
 * @see http://www.w3.org/RDF/
 * @version v1.0
 */
require_once('core/kernel/classes/class.Resource.php');

/* user defined includes */
// section 127-0-1-1-128d31a3:12bab34f1f7:-8000:0000000000001365-includes begin
// section 127-0-1-1-128d31a3:12bab34f1f7:-8000:0000000000001365-includes end

/* user defined constants */
// section 127-0-1-1-128d31a3:12bab34f1f7:-8000:0000000000001365-constants begin
// section 127-0-1-1-128d31a3:12bab34f1f7:-8000:0000000000001365-constants end

/**
 * Short description of class core_kernel_classes_File
 *
 * @access public
 * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 */
class core_kernel_classes_File
    extends core_kernel_classes_Resource
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getAbsolutePath
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @return string
     */
    public function getAbsolutePath()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:0000000000001367 begin
         $filePathProp = new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH);
	    $fileNameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
	    $filePath = $this->getOnePropertyValue($filePathProp);
	    $fileName = $this->getOnePropertyValue($fileNameProp);
        if($filePath == null){
            $filePath = GENERIS_FILES_PATH; 
        }
        $returnValue = $filePath . $fileName;
        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:0000000000001367 end

        return (string) $returnValue;
    }

    /**
     * Create an instance of class File from filename and filepath (filepath
     * be optionnal)
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  string fileName
     * @param  string filePath
     * @return core_kernel_classes_File
     */
    public static function create($fileName, $filePath = null)
    {
        $returnValue = null;

        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:000000000000136C begin
        if($filePath == null){
            $filePath = GENERIS_FILES_PATH; 
        }
        $clazz = new core_kernel_classes_Class(CLASS_GENERIS_FILE);
	    $instance = $clazz->createInstance('File : ' . $filePath. $fileName,'File : ' . $filePath. $fileName);
	    $filePathProp = new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH);
	    $fileNameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
	    $instance->setPropertyValue($filePathProp,$filePath);
	    $instance->setPropertyValue($fileNameProp,$fileName);
	    $returnValue = new core_kernel_classes_File($instance->uriResource);
        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:000000000000136C end

        return $returnValue;
    }

    /**
     * Short description of method isFile
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public static function isFile( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:0000000000001370 begin
        $resourceType = $resource->getType();
        $returnValue =  array_key_exists(CLASS_GENERIS_FILE,$resourceType);

        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:0000000000001370 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getFileContent
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @return SplFileInfo
     */
    public function getFileContent()
    {
        // section 127-0-1-1--77b1997d:12bf34c2951:-8000:0000000000001386 begin
    	return new SplFileInfo($this->getAbsolutePath());
        // section 127-0-1-1--77b1997d:12bf34c2951:-8000:0000000000001386 end
    }

    /**
     * Short description of method getFile
     *
     * @access public
     * @author Lionel Lecaque, <lionel.lecaque@tudor.lu>
     * @param  Resource resource
     * @return SplFileInfo , false
     */
    public static function getFile( core_kernel_classes_Resource $resource)
    {
        // section 127-0-1-1--77b1997d:12bf34c2951:-8000:0000000000001388 begin
        if(self::isFile($resource)){
            $file = new core_kernel_classes_File($resource->uriResource);
            return $file->getFileContent();
        }
        return false;
        // section 127-0-1-1--77b1997d:12bf34c2951:-8000:0000000000001388 end
    }

} /* end of class core_kernel_classes_File */

?>
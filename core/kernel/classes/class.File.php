<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/classes/class.File.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 12.04.2012, 14:06:10 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return string
     */
    public function getAbsolutePath()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:0000000000001367 begin
        $filePathProp = new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH);
	    $fileNameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
	    $filePath = (string) $this->getOnePropertyValue($filePathProp);
	    $fileName = (string) $this->getOnePropertyValue($fileNameProp);
        
        if($filePath == null){
            $filePath = GENERIS_FILES_PATH; 
        }
        
        //IF the resource is a folder resource, the absolute filepath should respect a specific format without slash as last char
        if(empty($fileName) && substr($filePath, strlen($returnValue)-1, 1) == DIRECTORY_SEPARATOR){
            $filePath = substr($filePath, 0, strlen($filePath)-1);
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
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string fileName
     * @param  string filePath
     * @param  string uri
     * @return core_kernel_classes_File
     */
    public static function create($fileName, $filePath = null, $uri = "")
    {
        $returnValue = null;

        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:000000000000136C begin
        
        // Default file path if not defined
      	if(is_null($filePath)){
            $filePath = GENERIS_FILES_PATH; 
        }
        // If the file does not exist in the file system => create it
       	/*if(!file_exists($filePath.$fileName)){
        	fclose(fopen($filePath.$fileName,"x"));
       	}*/

        $clazz = new core_kernel_classes_Class(CLASS_GENERIS_FILE);
	    $instance = $clazz->createInstance('File : ' . $filePath. $fileName, 'File : ' . $filePath. $fileName, $uri);
	    $filePathProp = new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH);
	    $fileNameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
	    $instance->setPropertyValue($filePathProp, $filePath);
	    $instance->setPropertyValue($fileNameProp, $fileName);
	    
	    $returnValue = new core_kernel_classes_File($instance->uriResource);
        
        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:000000000000136C end

        return $returnValue;
    }

    /**
     * Short description of method isFile
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public static function isFile( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:0000000000001370 begin
        $resourceType = $resource->getTypes();
        $returnValue =  array_key_exists(CLASS_GENERIS_FILE, $resourceType)||array_key_exists(CLASS_GENERIS_VERSIONEDFILE, $resourceType);        
        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:0000000000001370 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getFileContent
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function getFileContent()
    {
        // section 127-0-1-1--77b1997d:12bf34c2951:-8000:0000000000001386 begin
        if (!file_exists($this->getAbsolutePath())){
        	throw new Exception(__('File not found '.$this->getAbsolutePath()));
        }
    	return @file_get_contents($this->getAbsolutePath());
        // section 127-0-1-1--77b1997d:12bf34c2951:-8000:0000000000001386 end
    }

    /**
     * Short description of method getFile
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @return mixed
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

    /**
     * Short description of method delete
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  boolean deleteReference
     * @return boolean
     */
    public function delete($deleteReference = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:0000000000001672 begin
        /*if(file_exists($this->getAbsolutePath())){
        	if (!@unlink($this->getAbsolutePath())){
        		throw new Exception(__('Unable to remove the file '.$this->getAbsolutePath()));
        	}
        }*/
        parent::delete($deleteReference);
        $returnValue = true;
        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:0000000000001672 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method move
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return boolean
     */
    public function move()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:0000000000001674 begin
        throw new Exception(__('The function is not implemented yet'));
        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:0000000000001674 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getFileInfo
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function getFileInfo()
    {
        // section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:0000000000001671 begin
    	return new SplFileInfo($this->getAbsolutePath());
        // section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:0000000000001671 end
    }

    /**
     * Short description of method setContent
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string content
     * @return boolean
     */
    public function setContent($content)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:0000000000001675 begin
        
        $filePathProp = new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH);
        $filePath = $this->getOnePropertyValue($filePathProp);
        $path = explode('/', $filePath);
        $breadCrumb = '';
        foreach($path as $bread){
        	$breadCrumb .= $bread.'/';
        	if(!file_exists($breadCrumb)){
        		mkdir($breadCrumb);
        	}
        }
        
        if(file_put_contents($this->getAbsolutePath(), $content)===false){
            $returnValue = false;
        }else{
            $returnValue = true;
        }
        
        // section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:0000000000001675 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method fileExists
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return boolean
     */
    public function fileExists()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016EB begin
        $returnValue = file_exists($this->getAbsolutePath());        
        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016EB end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_classes_File */

?>
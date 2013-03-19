<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\classes\class.File.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 06.12.2012, 10:36:19 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
     * Short description of method getFileClass
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return core_kernel_classes_Class
     */
    protected static function getFileClass()
    {
        $returnValue = null;

        // section 10-30-1--78--1698032:13afe62e559:-8000:00000000000030B4 begin
        $returnValue = new core_kernel_classes_Class(CLASS_GENERIS_FILE);
        // section 10-30-1--78--1698032:13afe62e559:-8000:00000000000030B4 end

        return $returnValue;
    }

    /**
     * Create an instance of class File from filename and filepath (filepath
     * be optionnal)
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string fileName
     * @param  string filePath
     * @param  string uri
     * @param  array options
     * @return core_kernel_classes_File
     */
    public static function create($fileName, $filePath = null, $uri = "", $options = array())
    {
        $returnValue = null;

        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:000000000000136C begin
        
        // Default file path if not defined
      	if(is_null($filePath)){
            $filePath = GENERIS_FILES_PATH; 
        }

	    $instance = static::getFileClass()->createInstance('File : ' . $fileName, 'File : ' . $filePath. $fileName, $uri);
	    $filePathProp = new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH);
	    $fileNameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
	    $instance->setPropertyValue($filePathProp, $filePath);
	    $instance->setPropertyValue($fileNameProp, $fileName);
	    
	    $returnValue = new core_kernel_classes_File($instance->getUri());
        
        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:000000000000136C end

        return $returnValue;
    }

    /**
     * Short description of method isFile
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public static function isFile( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:0000000000001370 begin
        $resourceType = $resource->getTypes();
        $returnValue =  array_key_exists(CLASS_GENERIS_FILE, $resourceType) || array_key_exists(CLASS_GENERIS_VERSIONEDFILE, $resourceType);        
        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:0000000000001370 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getAbsolutePath
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getAbsolutePath()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:0000000000001367 begin
        $filePathProp = new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH);
	    $fileNameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
	    
	    $props = $this->getPropertiesValues(array(
	    	$filePathProp, $fileNameProp
	    ));
	    if (!isset($props[PROPERTY_FILE_FILEPATH]) || count($props[PROPERTY_FILE_FILEPATH]) == 0) {
	    	throw new common_Exception('filepath missing for file '.$this->getUri());
	    }
	    if (!isset($props[PROPERTY_FILE_FILENAME]) || count($props[PROPERTY_FILE_FILENAME]) == 0) {
	    	throw new common_Exception('filename missing for file '.$this->getUri());
	    }
	    $filePath = (string)current($props[PROPERTY_FILE_FILEPATH]);
	    $fileName = (string)current($props[PROPERTY_FILE_FILENAME]);
        
        if(substr($filePath, strlen($returnValue)-1, 1) == DIRECTORY_SEPARATOR){
            $filePath = substr($filePath, 0, strlen($filePath)-1);
        }
        
        if(empty($fileName)) {
	        //IF the resource is a folder resource, the absolute filepath should respect a specific format without slash as last char
        	$returnValue = $filePath;
        } else {
	        $returnValue = $filePath . DIRECTORY_SEPARATOR . $fileName;
        }
        // section 127-0-1-1-128d31a3:12bab34f1f7:-8000:0000000000001367 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getFileContent
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
     * Short description of method delete
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string content
     * @return boolean
     */
    public function setContent($content)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:0000000000001675 begin
        
        $filePathProp = new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH);
        $filePath = $this->getOnePropertyValue($filePathProp);
        $path = explode(DIRECTORY_SEPARATOR, $filePath);
        $breadCrumb = '';
        foreach($path as $bread){
        	$breadCrumb .= $bread.DIRECTORY_SEPARATOR;
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
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - helpers/class.File.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 04.04.2012, 16:58:18 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package helpers
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-3aa96a80:134c2ca4f13:-8000:00000000000018E5-includes begin
// section 127-0-1-1-3aa96a80:134c2ca4f13:-8000:00000000000018E5-includes end

/* user defined constants */
// section 127-0-1-1-3aa96a80:134c2ca4f13:-8000:00000000000018E5-constants begin
// section 127-0-1-1-3aa96a80:134c2ca4f13:-8000:00000000000018E5-constants end

/**
 * Short description of class helpers_File
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package helpers
 */
class helpers_File
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method resourceExists
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string path
     * @return boolean
     */
    public static function resourceExists($path = "")
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-3aa96a80:134c2ca4f13:-8000:00000000000018E9 begin
        
		$instances = self::searchResourcesFromPath($path);
        if(!empty($instances)){
            $returnValue = true;
        }
		
        // section 127-0-1-1-3aa96a80:134c2ca4f13:-8000:00000000000018E9 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getResource
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string path
     * @return core_kernel_classes_File
     */
    public static function getResource($path = "")
    {
        $returnValue = null;

        // section 127-0-1-1-3aa96a80:134c2ca4f13:-8000:00000000000018ED begin
        
        $instances = self::searchResourcesFromPath($path);
        if(!empty($instances)){
            $returnValue = current($instances);
        }
        
        // section 127-0-1-1-3aa96a80:134c2ca4f13:-8000:00000000000018ED end

        return $returnValue;
    }

    /**
     * Short description of method searchResourcesFromPath
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  string path
     * @return array
     */
    public static function searchResourcesFromPath($path = "")
    {
        $returnValue = array();

        // section 127-0-1-1-6a191a88:1367c838c77:-8000:0000000000002A0F begin
		$lastDirSep = strrpos($path, DIRECTORY_SEPARATOR);
        $filePath = substr($path, 0, $lastDirSep+1);
        $fileName = substr($path, $lastDirSep+1);
        
        $clazz = new core_kernel_classes_Class(CLASS_GENERIS_FILE);
        $propertyFilters = array(
            PROPERTY_FILE_FILEPATH      =>$filePath
            , PROPERTY_FILE_FILENAME    =>$fileName
        );
        $resources = $clazz->searchInstances($propertyFilters, array('recursive'=>true, 'like'=>false));
		foreach($resources as $resource){
			if (core_kernel_versioning_File::isVersionedFile($resource)) {
				$returnValue[$resource->uriResource] = new core_kernel_versioning_File($resource->uriResource);
			} else if (core_kernel_versioning_File::isFile($resource)) {
				$returnValue[$resource->uriResource] = new core_kernel_classes_File($resource->uriResource);
			}
		}
		
        // section 127-0-1-1-6a191a88:1367c838c77:-8000:0000000000002A0F end

        return (array) $returnValue;
    }

} /* end of class helpers_File */

?>
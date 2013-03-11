<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - helpers/class.File.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 04.03.2013, 15:58:40 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel@taotesting.com>
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
 * @author Joel Bout, <joel@taotesting.com>
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
     * @author Joel Bout, <joel@taotesting.com>
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
     * @author Joel Bout, <joel@taotesting.com>
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
     * @author Joel Bout, <joel@taotesting.com>
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
        $fileNameStr = $fileName != false ? $fileName : '';
        $clazz = new core_kernel_classes_Class(CLASS_GENERIS_FILE);
        $propertyFilters = array(
            PROPERTY_FILE_FILEPATH      =>$filePath
            , PROPERTY_FILE_FILENAME    =>$fileNameStr
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

    /**
     * returns the relative path from the file/directory 'from'
     * to the file/directory 'to'
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string from
     * @param  string to
     * @return string
     */
    public static function getRelPath($from, $to)
    {
        $returnValue = (string) '';

        // section 10-30-1--78-45a9d031:13c193b4a22:-8000:00000000000052F9 begin
        $from = is_dir($from) ? $from : dirname($from); 
		$arrFrom = explode(DIRECTORY_SEPARATOR, rtrim($from, DIRECTORY_SEPARATOR));
  		$arrTo = explode(DIRECTORY_SEPARATOR, rtrim($to, DIRECTORY_SEPARATOR));
  		
		while(count($arrFrom) && count($arrTo) && ($arrFrom[0] == $arrTo[0])) {
			array_shift($arrFrom);
			array_shift($arrTo);
		}
		foreach ($arrFrom as $dir) {
			$returnValue .= '..'.DIRECTORY_SEPARATOR;
		}
		$returnValue .= implode(DIRECTORY_SEPARATOR, $arrTo);
        // section 10-30-1--78-45a9d031:13c193b4a22:-8000:00000000000052F9 end

        return (string) $returnValue;
    }

    /**
     * deletes a file or a directory recursively
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string path
     * @return boolean
     */
    public static function remove($path)
    {
        $returnValue = (bool) false;

        // section 10-30-1--78--3660a937:13d35e0b7d2:-8000:0000000000001FFC begin
		if (is_file($path)) {
        	$returnValue = unlink($path);
        } elseif (is_dir($path)) {
			$iterator = new DirectoryIterator($path);
			foreach ($iterator as $fileinfo) {
				if (!$fileinfo->isDot()) {
					self::remove($fileinfo->getPathname());
				}
			}
			$returnValue = rmdir($path);
        } else {
        	throw new common_exception_Error('"'.$path.'" cannot be removed since it\'s neither a file nor directory');
        }
        // section 10-30-1--78--3660a937:13d35e0b7d2:-8000:0000000000001FFC end

        return (bool) $returnValue;
    }

} /* end of class helpers_File */

?>
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

/**
 * FileSystem helper functions
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package helpers
 */
class core_kernel_fileSystem_Cache
{
	const SERIAL = 'fileSystemMap'; 
	
    /**
     * Get an associativ array of enabled FileSystems with the form:
     * 
     * "FileSystem URI" => "FileSystem absolute path"
     * 
     * @return array
     */
    private static function getFileSystemMap()
    {
    	try {
    		$returnValue = common_cache_FileCache::singleton()->get(self::SERIAL);
    	} catch (common_cache_NotFoundException $e) {
    		$classRepository = new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDREPOSITORY);
			$resources = $classRepository->searchInstances(array(
				PROPERTY_GENERIS_VERSIONEDREPOSITORY_ENABLED => GENERIS_TRUE
			), array(
				'like' => false
			));
    		$returnValue = array();
	    	foreach ($resources as $resource) {
	    		$props = $resource->getPropertiesValues(array(
	    			PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH, PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE
	    		)); 
				$returnValue[$resource->getUri()] = array(
					PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH => current($props[PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH]),
					PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE => current($props[PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE])
				);
			}
			common_cache_FileCache::singleton()->put($returnValue, self::SERIAL);
    	}
    	return $returnValue;
    }
    
    private static function getFSInfo(core_kernel_versioning_Repository $fileSystem) {
    	$map = self::getFileSystemMap();
    	return isset ($map[$fileSystem->getUri()]) ? $map[$fileSystem->getUri()] : null;
    }
    
    public static function getEnabledFileSystems() {
    	$returnValue = array();
    	foreach (self::getFileSystemMap() as $uri => $path) {
    		$returnValue[] = new core_kernel_fileSystem_FileSystem($uri);
    	}
    	return $returnValue;
    }
    
    public static function getFileSystemPath(core_kernel_versioning_Repository $fileSystem) {
    	$info = self::getFSInfo($fileSystem);
    	return is_null($info) ? null : $info[PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH];
    }
    
    public static function getFileSystemType(core_kernel_versioning_Repository $fileSystem) {
    	$info = self::getFSInfo($fileSystem);
    	return is_null($info) ? null : $info[PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE];
    }
    /**
     * returns the FileSystem that contains the specified absoulte path
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param path
     * @return core_kernel_fileSystem_FileSystem
     */
    public static function getRelatedFileSystem($path)
    {
        $returnValue = null;

        foreach (self::getFileSystemMap() as $fsUri => $fsInfo) {
        	$fsPath = $fsInfo[PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH];
        	if (substr($path, 0, strlen($fsPath)) == $fsPath) {
        		$returnValue = new core_kernel_fileSystem_FileSystem($fsUri);
        		break;
        	}
        }

        return $returnValue;
    }
    
    public static function flushCache() {
    	common_cache_FileCache::singleton()->remove(self::SERIAL);
    }

}

?>
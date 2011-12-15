<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 15.12.2011, 11:55:25 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning_subversionWindows
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-a831e14:134415460c1:-8000:000000000000189C-includes begin
// section 127-0-1-1-a831e14:134415460c1:-8000:000000000000189C-includes end

/* user defined constants */
// section 127-0-1-1-a831e14:134415460c1:-8000:000000000000189C-constants begin
// section 127-0-1-1-a831e14:134415460c1:-8000:000000000000189C-constants end

/**
 * Short description of class core_kernel_versioning_subversionWindows_Utils
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning_subversionWindows
 */
class core_kernel_versioning_subversionWindows_Utils
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method exec
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  string command
     * @return string
     */
    public static function exec( core_kernel_classes_Resource $resource, $command)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-a831e14:134415460c1:-8000:000000000000189D begin
        $username = "";
        $password = "";
        $repository = null;
        
        try{
        	if(empty($command)){
        		throw new Exception(__CLASS__ . ' -> ' . __FUNCTION__ . '() : $command_ must be specified');
        	}
        	
			//get context variables
			if($resource instanceof core_kernel_versioning_File){
				$repository = $resource->getRepository();
			}else if($resource instanceof core_kernel_versioning_Repository){
				$repository = $resource;
			}else{
				throw new Exception('The first parameter (resource) should be a File or a Repository');
			}
        	
			if(is_null($repository)){
				throw new Exception('Unable to find the repository to work with for the reference resource ('.$resource->uriResource.')');
			}
			
			$username = $repository->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN));
			$password = $repository->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PASSWORD));
			
        	$returnValue = shell_exec('svn --username ' . $username . ' --password ' . $password . ' ' . $command);
        }
        catch (Exception $e){
        	die('Error code `svn_error_command` in ' . $e->getMessage());
        }
        // section 127-0-1-1-a831e14:134415460c1:-8000:000000000000189D end

        return (string) $returnValue;
    }

} /* end of class core_kernel_versioning_subversionWindows_Utils */

?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 07.12.2011, 16:42:06 with ArgoUML PHP module 
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
// section 127-0-1-1--6f35df64:13418ffe5d0:-8000:00000000000018D2-includes begin
// section 127-0-1-1--6f35df64:13418ffe5d0:-8000:00000000000018D2-includes end

/* user defined constants */
// section 127-0-1-1--6f35df64:13418ffe5d0:-8000:00000000000018D2-constants begin
// section 127-0-1-1--6f35df64:13418ffe5d0:-8000:00000000000018D2-constants end

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
     * @param  array resource
     * @param  string command
     * @return string
     */
    public static function exec($resource, $command)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--6f35df64:13418ffe5d0:-8000:00000000000018D3 begin

        $username = "";
        $password = "";
        $repository = null;
         
        try{
        	if(empty($command_)){
        		throw new Exception(__CLASS__ . ' -> ' . __FUNCTION__ . '() : $command_ must be specified');
        	}
        	
			//get context variables
			if($resource instanceof core_kernel_classes_File){
				$repository = getRepository();
			}else if($resource instanceof core_kernel_classes_File){
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
        
        // section 127-0-1-1--6f35df64:13418ffe5d0:-8000:00000000000018D3 end

        return (string) $returnValue;
    }

} /* end of class core_kernel_versioning_subversionWindows_Utils */

?>
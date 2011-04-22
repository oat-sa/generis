<?php

error_reporting(E_ALL);

/**
 * Utility class that provides transversal methods 
 * to manage  the hard api
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardapi
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_persistence_Switcher
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/persistence/class.Switcher.php');

/* user defined includes */
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:0000000000001596-includes begin
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:0000000000001596-includes end

/* user defined constants */
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:0000000000001596-constants begin
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:0000000000001596-constants end

/**
 * Utility class that provides transversal methods 
 * to manage  the hard api
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_hardapi
 */
class core_kernel_persistence_hardapi_Utils
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute namespaceIds
     *
     * @access private
     * @var array
     */
    private static $namespaceIds = array();

    // --- OPERATIONS ---

    /**
     * Get the namespace identifier of an URI,
     * using the modelID/baseUri mapping
     *
     * @access private
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string namespaceUri
     * @return string
     */
    private static function getNamespaceId($namespaceUri)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--5a63b0fb:12f72879be9:-8000:000000000000159A begin
        
		if(count(self::$namespaceIds) == 0){
			$namespaces = common_ext_NamespaceManager::singleton()->getAllNamespaces();
			foreach($namespaces as $namespace){
				if( ((int)$namespace->getModelId()) < 10){
					self::$namespaceIds[$namespace->getUri()] = '0' . $namespace->getModelId();
				}
				else{
					self::$namespaceIds[$namespace->getUri()] = (string)$namespace->getModelId();
				}
			}
		}
		if(isset(self::$namespaceIds[$namespaceUri])){
			$returnValue = self::$namespaceIds[$namespaceUri];
		}
        
        // section 127-0-1-1--5a63b0fb:12f72879be9:-8000:000000000000159A end

        return (string) $returnValue;
    }

    /**
     * Get the shortname of a resource.
     * It helps you for the tables and columns names
     * that cannot be longer than 64 characters
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return string
     */
    public static function getShortName( core_kernel_classes_Resource $resource)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--5a63b0fb:12f72879be9:-8000:000000000000159D begin
        
    	if(!is_null($resource)){
			$nsUri = substr($resource->uriResource, 0, strpos($resource->uriResource, '#')+1);
			$returnValue = str_replace($nsUri, self::getNamespaceId($nsUri), $resource->uriResource);
		}
        
        // section 127-0-1-1--5a63b0fb:12f72879be9:-8000:000000000000159D end

        return (string) $returnValue;
    }

    /**
     * Short description of method getLongName
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string shortName
     * @return string
     */
    public static function getLongName($shortName)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--151fe597:12f7c91b993:-8000:00000000000014C7 begin
        
        if (!empty($shortName) && strlen($shortName)>2){
        	$modelID = intval (substr($shortName, 0, 2));
        	
        	if ($modelID != null && $modelID >0){
	        	$nsUri = common_ext_NamespaceManager::singleton()->getNamespace ($modelID);
	         	$returnValue = $nsUri . substr($shortName, 2);
        	}
        }       
        
        // section 127-0-1-1--151fe597:12f7c91b993:-8000:00000000000014C7 end

        return (string) $returnValue;
    }

} /* end of class core_kernel_persistence_hardapi_Utils */

?>
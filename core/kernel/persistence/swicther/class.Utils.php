<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 20.04.2011, 13:05:54 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_swicther
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_persistence_Switcher
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('core/kernel/persistence/class.Switcher.php');

/* user defined includes */
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:0000000000001596-includes begin
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:0000000000001596-includes end

/* user defined constants */
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:0000000000001596-constants begin
// section 127-0-1-1--5a63b0fb:12f72879be9:-8000:0000000000001596-constants end

/**
 * Short description of class core_kernel_persistence_swicther_Utils
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_swicther
 */
class core_kernel_persistence_swicther_Utils
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
     * Short description of method getNamespaceId
     *
     * @access private
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
		if(isset(self::$namespaceIds[$nsUri])){
			$returnValue = self::$namespaceIds[$nsUri];
		}
        
        // section 127-0-1-1--5a63b0fb:12f72879be9:-8000:000000000000159A end

        return (string) $returnValue;
    }

    /**
     * Short description of method getShortName
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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

} /* end of class core_kernel_persistence_swicther_Utils */

?>
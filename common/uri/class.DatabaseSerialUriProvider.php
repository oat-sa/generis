<?php

error_reporting(E_ALL);

/**
 * UriProvider implementation based on a serial stored in the database.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage uri
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Any implementation of the AbstractUriProvider class aims at providing unique
 * to client code. It should take into account the state of the Knowledge Base
 * avoid collisions. The AbstractUriProvider::provide method must be implemented
 * subclasses to return a valid URI.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/uri/class.AbstractUriProvider.php');

/* user defined includes */
// section 10-13-1-85--341437fc:13634d84b3e:-8000:00000000000019A2-includes begin
// section 10-13-1-85--341437fc:13634d84b3e:-8000:00000000000019A2-includes end

/* user defined constants */
// section 10-13-1-85--341437fc:13634d84b3e:-8000:00000000000019A2-constants begin
// section 10-13-1-85--341437fc:13634d84b3e:-8000:00000000000019A2-constants end

/**
 * UriProvider implementation based on a serial stored in the database.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage uri
 */
class common_uri_DatabaseSerialUriProvider
    extends common_uri_AbstractUriProvider
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Generates a URI based on a serial stored in the database.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     * @throws common_UriProviderException
     */
    public function provide()
    {
        $returnValue = (string) '';

        // section 10-13-1-85--341437fc:13634d84b3e:-8000:00000000000019A5 begin
        $driver = $this->getDriver();
        switch ($driver){
        	case 'mysql':
        		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        		$modelUri = core_kernel_classes_Session::singleton()->getNameSpace() . '#';
        		
        		if (($result = $dbWrapper->execSql("SELECT generis_sequence_uri_provider(?)", array($modelUri))) !== false){
        			$returnValue = $result->Fields(0);
        		}
        		else{
        			throw new common_uri_UriProviderException("An error occured while calling the stored procedure for mysql.");	
        		}
        	break;
            case 'postgres8':
                $dbWrapper = core_kernel_classes_DbWrapper::singleton();
                $modelUri = core_kernel_classes_Session::singleton()->getNameSpace() . '#';
                
        		if (($result = $dbWrapper->execSql("SELECT * FROM generis_sequence_uri_provider(?)", array($modelUri))) !== false){
        			$returnValue = $result->Fields(0);
        		}
        		else{
        			throw new common_uri_UriProviderException("An error occured while calling the stored procedure for postgresql.");	
        		}
                
            break;
        	default:
        		throw new common_uri_UriProviderException("Unknown database driver.");
        	break;
        }
        // section 10-13-1-85--341437fc:13634d84b3e:-8000:00000000000019A5 end

        return (string) $returnValue;
    }

} /* end of class common_uri_DatabaseSerialUriProvider */

?>
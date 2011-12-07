<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 07.12.2011, 16:39:56 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning_subversionWindows
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_versioning_RepositoryInterface
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/versioning/interface.RepositoryInterface.php');

/* user defined includes */
// section 127-0-1-1--6f35df64:13418ffe5d0:-8000:00000000000018B9-includes begin
// section 127-0-1-1--6f35df64:13418ffe5d0:-8000:00000000000018B9-includes end

/* user defined constants */
// section 127-0-1-1--6f35df64:13418ffe5d0:-8000:00000000000018B9-constants begin
// section 127-0-1-1--6f35df64:13418ffe5d0:-8000:00000000000018B9-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning_subversionWindows
 */
class core_kernel_versioning_subversionWindows_Repository
        implements core_kernel_versioning_RepositoryInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access public
     * @var Repository
     */
    public static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method checkout
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Repository vcs
     * @param  string url
     * @param  string path
     * @param  int revision
     * @return boolean
     */
    public function checkout( core_kernel_versioning_subversion_Repository $vcs, $url, $path, $revision = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--548d6005:132d344931b:-8000:0000000000002503 begin
        
        try {
        	
        	$url = $vcs->getOnePropertyValue(PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL);
        	$path = $vcs->getOnePropertyValue(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH);
        	
        	if (empty($url)){
        		throw new Exception(__CLASS__ . ' -> ' . __FUNCTION__ . '() : the url must be specified');
        	}
        	if (empty($path)){
        		throw new Exception(__CLASS__ . ' -> ' . __FUNCTION__ . '() : the path must be specified');
        	}

        	$returnValue = core_kernel_versioning_subversionWindows_Utils::exec($vcs, 'checkout ' . $url . ' ' . $path);
        }
        catch (Exception $e) {
        	die('Error code `svn_error_checkout` in ' . $e->getMessage());
        }

        // section 127-0-1-1--548d6005:132d344931b:-8000:0000000000002503 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method authenticate
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Repository vcs
     * @param  string login
     * @param  string password
     * @return boolean
     */
    public function authenticate( core_kernel_versioning_Repository $vcs, $login, $password)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016E6 begin
        throw new core_kernel_versioning_subversionWindows_Repository("The function (".__METHOD__.") is not available in this versioning implementation (".__CLASS__.")");
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016E6 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_versioning_subversion_Repository
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--6f35df64:13418ffe5d0:-8000:00000000000018C6 begin
        
        if (core_kernel_versioning_subversionWindows_Repository::$instance == null){
			core_kernel_versioning_subversionWindows_Repository::$instance = new core_kernel_versioning_subversionWindows_Repository();
		}
		$returnValue = core_kernel_versioning_subversionWindows_Repository::$instance;
        
		// section 127-0-1-1--6f35df64:13418ffe5d0:-8000:00000000000018C6 end

        return $returnValue;
    }

} /* end of class core_kernel_versioning_subversionWindows_Repository */

?>
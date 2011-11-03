<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 03.11.2011, 12:30:28 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning_subversion
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
// section 127-0-1-1--548d6005:132d344931b:-8000:0000000000002500-includes begin
// section 127-0-1-1--548d6005:132d344931b:-8000:0000000000002500-includes end

/* user defined constants */
// section 127-0-1-1--548d6005:132d344931b:-8000:0000000000002500-constants begin
// section 127-0-1-1--548d6005:132d344931b:-8000:0000000000002500-constants end

/**
 * Short description of class core_kernel_versioning_subversion_Repository
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning_subversion
 */
class core_kernel_versioning_subversion_Repository
        implements core_kernel_versioning_RepositoryInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var Repository
     */
    private static $instance = null;

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
    public function checkout( core_kernel_versioning_Repository $vcs, $url, $path, $revision = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--548d6005:132d344931b:-8000:0000000000002503 begin
        
        $returnValue = svn_checkout($url, $path, $revision);
        
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
        
		svn_auth_set_parameter(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true); // <--- Important for certificate issues!
		svn_auth_set_parameter(SVN_AUTH_PARAM_NON_INTERACTIVE, true);
		svn_auth_set_parameter(SVN_AUTH_PARAM_NO_AUTH_CACHE, true);
		svn_auth_set_parameter(SVN_AUTH_PARAM_DONT_STORE_PASSWORDS, true);
		
        svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, $login);
        svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $password);
        
        if(@svn_info((string)$vcs->getOnePropertyValue(new core_kernel_classes_property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL))) !== false){
        	$returnValue = true;
        }
        
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

        // section 127-0-1-1--548d6005:132d344931b:-8000:000000000000250B begin
        
        if(is_null(self::$instance)){
			self::$instance = new core_kernel_versioning_subversion_Repository();
		}
		$returnValue = self::$instance;
        
        // section 127-0-1-1--548d6005:132d344931b:-8000:000000000000250B end

        return $returnValue;
    }

} /* end of class core_kernel_versioning_subversion_Repository */

?>
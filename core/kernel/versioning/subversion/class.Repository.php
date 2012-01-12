<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 11.01.2012, 12:05:46 with ArgoUML PHP module 
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
        
        if($vcs->authenticate()){
            $returnValue = svn_checkout($url, $path, $revision);
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
     * Short description of method export
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Repository vcs
     * @param  string src
     * @param  string target
     * @param  int revision
     * @return boolean
     */
    public function export( core_kernel_versioning_Repository $vcs, $src, $target = null, $revision = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:000000000000290C begin
        
        $revision = is_null($revision) ? -1 : $revision;
        
        if($vcs->authenticate()){
            $returnValue = svn_export($src, $target, true, $revision);
        }

        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:000000000000290C end

        return (bool) $returnValue;
    }

    /**
     * Short description of method import
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Repository vcs
     * @param  string src
     * @param  string target
     * @param  string message
     * @param  array options
     * @return core_kernel_classes_File
     */
    public function import( core_kernel_versioning_Repository $vcs, $src, $target, $message = "", $options = array())
    {
        $returnValue = null;

        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:0000000000002912 begin
        //Does not work in the current version of php (try later) https://bugs.php.net/bug.php?id=60293
        //$returnValue = svn_import($src, $target, true);
        
        $saveResource = isset($options['saveResource']) && $options['saveResource'] ? true : false;
        
        if($vcs->authenticate()){
            $src = realpath($src);
            //extract folder name
            $folderName = basename($src);
            $relativePath = $target.$folderName;
            $absolutePath = $vcs->getPath().$folderName;

            //check if the file exists in the onthology
            if(helpers_File::resourceExists($absolutePath)){
                throw new core_kernel_versioning_ResourceAlreadyExistsException('The folder ('.$absolutePath.') already exists in the repository ('.$vcs->getPath().')');
            }else if(file_exists($absolutePath)){
                throw new common_exception_fileAlreadyExists($absolutePath);
            }

            //Copy the src folder to the target destination
            tao_helpers_File::copy($src, $vcs->getPath());
            //Create the resource (it should be an option, deleted if not required)
            $folder = core_kernel_versioning_File::create('', $relativePath, $vcs);
            //Add & commit
            if($folder->add(true) && $folder->commit($message, true)){
                if($saveResource){
                    $returnValue = $folder;
                }else{
                    $resourceToDelete = new core_kernel_classes_Resource($folder->uriResource);
                    $resourceToDelete->delete();
                }
            }
            else{
                throw new core_kernel_versioning_Exception('unable to add & commit the folder ('.$src.') to the destination ('.$target.')');
            }
        }
        
        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:0000000000002912 end

        return $returnValue;
    }

    /**
     * Short description of method listContent
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Repository vcs
     * @param  string path
     * @param  int revision
     * @return array
     */
    public function listContent( core_kernel_versioning_Repository $vcs, $path, $revision = null)
    {
        $returnValue = array();

        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:0000000000002916 begin
        
        if($vcs->authenticate()){
            $svnList = svn_ls($path, $revision);
            foreach($svnList as $svnEntry){
                $returnValue[] = array(
                     'name'         => $svnEntry['name']
                     , 'type'       => $svnEntry['type']
                     , 'revision'   => $svnEntry['created_rev']
                     , 'author'     => $svnEntry['last_author']
                     , 'time'       => $svnEntry['time_t']
                );
            }
        }
        
        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:0000000000002916 end

        return (array) $returnValue;
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
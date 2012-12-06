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

    /**
     * Authenticated server
     * @var type array
     */
    private static $authenticatedRepositories = array();
    
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
        
        $startTime = helpers_Time::getMicroTime();
        if($vcs->authenticate()){
            $returnValue = svn_checkout($url, $path, $revision);
        }
        $endTime = helpers_Time::getMicroTime();
        common_Logger::i("svn_checkout (".$url.' -> '.$path.') -> '.($endTime-$startTime).'s');
        
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
        
        //if the system has already do its authentication to the repository, return the negociation result
        if(isset(self::$authenticatedRepositories[$vcs->uriResource])){
            $returnValue = self::$authenticatedRepositories[$vcs->uriResource];
        }
        //authenticate the system to the repository
        else{
            svn_auth_set_parameter(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true); // <--- Important for certificate issues!
            svn_auth_set_parameter(SVN_AUTH_PARAM_NON_INTERACTIVE, true);
            svn_auth_set_parameter(SVN_AUTH_PARAM_NO_AUTH_CACHE, true);
            svn_auth_set_parameter(SVN_AUTH_PARAM_DONT_STORE_PASSWORDS, true);

            svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, $login);
            svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $password);

            if(@svn_info((string) $vcs->getOnePropertyValue(new core_kernel_classes_property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL)), false) !== false){
                $returnValue = true;
            }
        }
        self::$authenticatedRepositories[$vcs->uriResource] = $returnValue;
        
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
        
        $startTime = helpers_Time::getMicroTime();
        $revision = is_null($revision) ? -1 : $revision;
        
        if($vcs->authenticate()){
            $returnValue = svn_export($src, $target, true, $revision);
        }
        $endTime = helpers_Time::getMicroTime();
        common_Logger::i("svn_export (".$src.' -> '.$target.') -> '.($endTime-$startTime).'s');

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
        
        $startTime = helpers_Time::getMicroTime();
        $saveResource = isset($options['saveResource']) && $options['saveResource'] ? true : false;
        
        if($vcs->authenticate()){
            $importFolderAlreadyExists = false;
            $repositoryUrl = $vcs->getUrl();
            $relativePath = substr($target, strlen($repositoryUrl));
            $absolutePath = $vcs->getPath().$relativePath;

            /*
            // The resource could already exist, this is not a problem
            //check if the resource already exist
            if(helpers_File::resourceExists($absolutePath)){
                throw new core_kernel_versioning_exception_ResourceAlreadyExistsException('The folder ('.$absolutePath.') already exists in the repository ('.$vcs->getPath().')');
            }
            // Same thing here
            //check if the file already exist
            else if(file_exists($absolutePath)){
                throw new common_exception_fileAlreadyExists($absolutePath);
            }
            */

            //Copy the src folder to the target destination
            tao_helpers_File::copy($src, $absolutePath);
            
            //Get the resource folder if it already exists in the onthology     
            $importFolder = helpers_File::getResource($absolutePath);
            if(is_null($importFolder)){
                //else create it
                $importFolder = core_kernel_versioning_File::createVersioned('', $relativePath, $vcs);
                }else{
                $importFolderAlreadyExists = true;
                }
            
//            //Get status of the imported folder
//            $importFolderStatus = $importFolder->getStatus(array('SHOW_UPDATES'=>false));
//            $importFolderYetCommited = true;
//            if($importFolderStatus == VERSIONING_FILE_STATUS_ADDED || $importFolderStatus == VERSIONING_FILE_STATUS_UNVERSIONED){
//                $importFolderYetCommited = false;
//            }
//            
//            //If the import folder has been yet commited, commit its content
//            if($importFolderYetCommited){
//                $filesToCommit = tao_helpers_File::scandir($importFolder->getAbsolutePath());
//                $pathsFilesToCommit = array();
//                foreach($filesToCommit as $fileToCommit){
//                    $pathFileToCommit = $fileToCommit->getAbsolutePath();
//                    $pathsFilesToCommit[] = $pathFileToCommit;
//                    //Add content of the folder if it is not versioned or partially not versioned
//                    if(!core_kernel_versioning_FileProxy::add($importFolder, $pathFileToCommit, true, true)){
//                        throw new core_kernel_versioning_exception_Exception('unable to import the folder ('.$src.') to the destination ('.$target.'). The add step encountered a problem');
//                    }
//                }
//                //Commit all the files in one commit operation
//                if(!core_kernel_versioning_FileProxy::commit($VersionedUnitFolderInstance, $pathsFilesToCommit)){
//                    throw new core_kernel_versioning_exception_Exception('unable to import the folder ('.$src.') to the destination ('.$target.'). The commit step encountered a problem');
//                }
//            }
//            //Else commit itself
//            else{
//                //Add the folder
//                if(!$importFolder->add(true, true)){
//                    throw new core_kernel_versioning_exception_Exception('unable to import the folder ('.$src.') to the destination ('.$target.'). The add step encountered a problem');
//                }
//                //And commit it
//                if(!$importFolder->commit($message, true)){
//                    throw new core_kernel_versioning_exception_Exception('unable to import the folder ('.$src.') to the destination ('.$target.'). The commit step encountered a problem');
//                }
//            }
            
            //Add the folder
            if(!$importFolder->add(true, true)){
                throw new core_kernel_versioning_exception_Exception('unable to import the folder ('.$src.') to the destination ('.$target.'). The add step encountered a problem');
            }
            //And commit it
            if(!$importFolder->commit($message, true)){
                throw new core_kernel_versioning_exception_Exception('unable to import the folder ('.$src.') to the destination ('.$target.'). The commit step encountered a problem');
            }
            
            //Delete the resource if the developer does not want to keep a reference in the onthology
            if($saveResource){
                $returnValue = $importFolder;
            }
            else{
                $resourceToDelete = new core_kernel_classes_Resource($importFolder->uriResource);
                $resourceToDelete->delete();
            }
        }
        
        $endTime = helpers_Time::getMicroTime();
        common_Logger::i("svn_import (".$src.' -> '.$target.') -> '.($endTime-$startTime).'s');
        
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
        
        $startTime = helpers_Time::getMicroTime();
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
        $endTime = helpers_Time::getMicroTime();
        common_Logger::i("svn_listContent (".$path.') -> '.($endTime-$startTime).'s');
        
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
<?php

error_reporting(E_ALL);

/**
 * Manage your versioned files as resources in TAO
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_classes_File
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/classes/class.File.php');

/**
 * include core_kernel_versioning_FileProxy
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/versioning/class.FileProxy.php');

/* user defined includes */
// section 127-0-1-1-6b8f17d3:132493e0488:-8000:0000000000001668-includes begin
// section 127-0-1-1-6b8f17d3:132493e0488:-8000:0000000000001668-includes end

/* user defined constants */
// section 127-0-1-1-6b8f17d3:132493e0488:-8000:0000000000001668-constants begin

const VERSIONING_FILE_STATUS_UNVERSIONED        = 2;
const VERSIONING_FILE_STATUS_NORMAL             = 3;
const VERSIONING_FILE_STATUS_ADDED              = 4;
const VERSIONING_FILE_STATUS_MISSING            = 5;
const VERSIONING_FILE_STATUS_DELETED            = 6;
const VERSIONING_FILE_STATUS_REPLACED           = 7;
const VERSIONING_FILE_STATUS_MODIFIED           = 8;
const VERSIONING_FILE_STATUS_CONFLICTED         = 10;
const VERSIONING_FILE_STATUS_REMOTELY_MODIFIED  = 15;
const VERSIONING_FILE_STATUS_REMOTELY_LOCKED    = 16;
const VERSIONING_FILE_STATUS_REMOTELY_DELETED   = 17;

const VERSIONING_FILE_VERSION_MINE              = 'mine-full';
const VERSIONING_FILE_VERSION_THEIRS            = 'theirs-full';
const VERSIONING_FILE_VERSION_WORKING           = 'working';
const VERSIONING_FILE_VERSION_BASE              = 'base';

// section 127-0-1-1-6b8f17d3:132493e0488:-8000:0000000000001668-constants end

/**
 * Manage your versioned files as resources in TAO
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning
 */
class core_kernel_versioning_File
    extends core_kernel_classes_File
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Resources factory
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string fileName
     * @param  string relativeFilePath
     * @param  Resource repository
     * @param  string uri
     * @param  array options
     * @return core_kernel_classes_File
     */
    public static function create($fileName, $relativeFilePath = null,  core_kernel_classes_Resource $repository = null, $uri = "", $options = array())
    {
        $returnValue = null;

        // section 127-0-1-1--a63bd74:132c9c69076:-8000:000000000000249D begin
        
        $repositoryPath = $repository->getPath();
        //add a slash at the end of the repository path if it does not exist
        $repositoryPath = substr($repositoryPath,strlen($repositoryPath)-1,1)==DIRECTORY_SEPARATOR ? $repositoryPath : $repositoryPath.DIRECTORY_SEPARATOR;
        //remove the first slash of the relative path if it exists
        $relativeFilePath = count($relativeFilePath) && $relativeFilePath[0]==DIRECTORY_SEPARATOR ? substr($relativeFilePath,1) : $relativeFilePath;
        //if the relative file path exists format the string
        $relativeFilePath = file_exists($relativeFilePath) ? realpath($relativeFilePath) : $relativeFilePath;
        //add a slash at the end of the relative file path unless the relative file path is empty
        //$relativeFilePath = empty($relativeFilePath) || substr($relativeFilePath,strlen($relativeFilePath)-1,1)==DIRECTORY_SEPARATOR ? $relativeFilePath : $relativeFilePath.DIRECTORY_SEPARATOR;
        
        //build the file path
        $filePath = $repositoryPath.$relativeFilePath;
        
        //Quick hack
        //@todo document and make the change clear
        $filePath = file_exists($filePath) ? realpath($filePath) : $filePath;
        //add directory separator at the end of the file path
        $filePath = substr($filePath,strlen($filePath)-1,1)==DIRECTORY_SEPARATOR ? $filePath : $filePath.DIRECTORY_SEPARATOR;
        
        //check if a resource with the same path exists yet in the repository
        $clazz = new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDFILE);
        $options = array('like' => false, 'recursive' => true);
		$propertyFilter = array(
			PROPERTY_FILE_FILENAME => $fileName,
			PROPERTY_VERSIONEDFILE_FILEPATH => $filePath,
			PROPERTY_VERSIONEDFILE_REPOSITORY => $repository->uriResource
		);
        $sameNameFiles = $clazz->searchInstances($propertyFilter, $options);
        if(!empty($sameNameFiles)){
        	throw new core_kernel_versioning_exception_Exception(__('A file with the name "'.$fileName.'" already exists at the location '.$repositoryPath.$filePath));
        }
        
        // If the file does not exist, create it
        /*if(!file_exists($repositoryPath.$filePath.DIRECTORY_SEPARATOR.$fileName)){
        	$create = true;
    	}*/
        $instance = parent::create($fileName, $filePath, $uri);
        $returnValue = new core_kernel_versioning_File($instance->uriResource);
        
        // Add versioned file path, path of the file in the repository
	    $versionedFilePathProp = new core_kernel_classes_Property(PROPERTY_VERSIONEDFILE_FILEPATH);
	    $instance->setPropertyValue($versionedFilePathProp, $relativeFilePath);
	    
	    // Add repository
	    $versionedFileRepositoryProp = new core_kernel_classes_Property(PROPERTY_VERSIONEDFILE_REPOSITORY);
	    $instance->setPropertyValue($versionedFileRepositoryProp, $repository);
        
        // section 127-0-1-1--a63bd74:132c9c69076:-8000:000000000000249D end

        return $returnValue;
    }

    /**
     * Check if a resource is a versioned file resource
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public static function isVersionedFile( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000024B1 begin
        
        $returnValue = $resource->hasType(new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDFILE));
        
        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000024B1 end

        return (bool) $returnValue;
    }

    /**
     * Commit changes to the remote repository
     *
     * Throw a core_kernel_versioning_exception_VersioningDisabledException 
     * if the constant GENERIS_VERSIONING_ENABLED is set to false
     *
     * Throw a core_kernel_versioning_exception_FileRemainsInConflictException 
     * if  the local working copy of the resource remains in conflict
     *
     * Throw a core_kernel_versioning_exception_OutOfDateException 
     * if the local working copy of the resource is out of date (and 
     * requires an update)
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string message
     * @param  boolean recursive
     * @return boolean
     */
    public function commit($message = "", $recursive = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000032F5 begin
    
        if(!GENERIS_VERSIONING_ENABLED){
        	throw new core_kernel_versioning_exception_VersioningDisabledException();
        }
        
        $status = $this->getStatus();
        
        //check that the file does not remain in conflict
        if($status == VERSIONING_FILE_STATUS_UNVERSIONED){
            throw new core_kernel_versioning_exception_FileUnversionedException();
        }
        
        //check that the file does not remain in conflict
        if($status == VERSIONING_FILE_STATUS_CONFLICTED){
            throw new core_kernel_versioning_exception_FileRemainsInConflictException();
        }
        
        //check that the file does not remain in conflict
        if($status == VERSIONING_FILE_STATUS_REMOTELY_MODIFIED){
            throw new core_kernel_versioning_exception_OutOfDateException();
        }
        
        $returnValue = core_kernel_versioning_FileProxy::singleton()->commit($this, $message, $this->getAbsolutePath(), $recursive);
        
        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000032F5 end

        return (bool) $returnValue;
    }

    /**
     * Update changes from the remote repository
     * Throw a core_kernel_versioning_exception_VersioningDisabledException 
     * if the constant GENERIS_VERSIONING_ENABLED is set to false
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  int revision
     * @return boolean
     */
    public function update($revision = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000032F7 begin
        
        if(!GENERIS_VERSIONING_ENABLED){
        	throw new core_kernel_versioning_exception_VersioningDisabledException();
        }
        
        $status = $this->getStatus();
        
        //if a revision has been given
        //or the remote version has been modified 
        //or the local working copy does not exist 
        //or the target is a directory
        if( !is_null($revision)
            || is_dir($this->getAbsolutePath()) || (
            $status == VERSIONING_FILE_STATUS_REMOTELY_MODIFIED 
            && $this->fileExists()
        )){
            $returnValue = core_kernel_versioning_FileProxy::singleton()->update($this, $this->getAbsolutePath(), $revision);
        }
        //the file does not require an update, return true
        else{
            $returnValue = true;
        }
        
        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000032F7 end

        return (bool) $returnValue;
    }

    /**
     * Revert changes
     * Throw a core_kernel_versioning_exception_VersioningDisabledException 
     * if the constant GENERIS_VERSIONING_ENABLED is set to false
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  int revision If a revision is given revert changes from this revision. Else revert local changes.
     * @param  string msg
     * @return boolean
     */
    public function revert($revision = null, $msg = "")
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000032F9 begin
        
        if(!GENERIS_VERSIONING_ENABLED){
        	throw new core_kernel_versioning_exception_VersioningDisabledException();
        }
        
        if($this->fileExists()){
        	if($this->isVersioned()){
        		$returnValue = core_kernel_versioning_FileProxy::singleton()->revert($this, $revision, $msg);
        	}
        }
        
        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000032F9 end

        return (bool) $returnValue;
    }

    /**
     * Delete the resource from the ontology.
     * Be carrefull, the function does not delete the file in the file 
     * system.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    public function delete()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000032FC begin
        
        if($this->fileExists() && GENERIS_VERSIONING_ENABLED){
        	$filePath = $this->getAbsolutePath();
            //check if the file is up to date
            
            /**
             * @todo this code won't work in shell implentation
             */
            //If the file has yet been deleted remotly => udpate it
            if($this->getStatus() == VERSIONING_FILE_STATUS_REMOTELY_DELETED){
                $returnValue = $this->update();
                /**
                 * @todo check the file is now well deleted locally
                 */
            }
            //else delete it
            else{
                //check if the resource is versioned before the delete
                $isVersioned = $this->isVersioned();
                //if in conflict solve before the problem by using our version of the file
                if($this->isInConflict()){
                    $this->resolve(VERSIONING_FILE_VERSION_MINE);
                }
                //delete the svn resource
                $returnValue = core_kernel_versioning_FileProxy::singleton()->delete($this, $filePath, true);
                //commit the svn delete
                if($returnValue && $isVersioned){
                    //delete the svn resource
                    $returnValue = $this->commit(__('delete the file').' '.$filePath, is_dir($filePath));
                }
            }
            
        }
        else{
            $returnValue = true;
        }
	    
        //delete the tao resource
        $returnValue &= parent::delete();
        
        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000032FC end

        return (bool) $returnValue;
    }

    /**
     * Get the repository which is associated to the resource
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_versioning_subversion_Repository
     */
    public function getRepository()
    {
        $returnValue = null;

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016DB begin
        
        $repository = $this->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_VERSIONEDFILE_REPOSITORY));
        if(!is_null($repository)){
        	$returnValue = new core_kernel_versioning_Repository($repository->uriResource);
        }
        
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016DB end

        return $returnValue;
    }

    /**
     * Add the resource to the remote repository
     * Throw a core_kernel_versioning_exception_VersioningDisabledException 
     * if the constant GENERIS_VERSIONING_ENABLED is set to false
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  boolean recursive
     * @param  boolean force
     * @return boolean
     */
    public function add($recursive = false, $force = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F5 begin
        
        if (!GENERIS_VERSIONING_ENABLED){
            throw new core_kernel_versioning_exception_VersioningDisabledException();
        }

        //Check if the path is versioned
        $relativePath = (string) $this->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_VERSIONEDFILE_FILEPATH));
        $fileName = (string) $this->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_FILE_FILENAME));
        $filePath = $this->getRepository()->getPath() . DIRECTORY_SEPARATOR . $relativePath;
        $relativeFilePathExploded = explode(DIRECTORY_SEPARATOR, $relativePath);
        $breadCrumb = realpath($this->getRepository()->getPath());
        
        foreach ($relativeFilePathExploded as $bread) {
            $breadCrumb = realpath($breadCrumb . DIRECTORY_SEPARATOR . $bread);
            if (empty($bread)) {
                continue;
            } 
            //if the resource resource to add is a folder, do not add and commit the resource at this moment
            else if ($breadCrumb == realpath($filePath.DIRECTORY_SEPARATOR.$fileName)) {
                continue;
            }

            if(core_kernel_versioning_FileProxy::singleton()->getStatus($this, $breadCrumb, array('SHOW_UPDATES'=>false)) == VERSIONING_FILE_STATUS_UNVERSIONED){
                core_kernel_versioning_FileProxy::singleton()->add($this, $breadCrumb, null, true);
            }
            core_kernel_versioning_FileProxy::singleton()->commit($this, "[sys] Add directory to the repository", $breadCrumb);
        }

        //the file was already versioned -> EXCEPTION
        /*if($this->isVersioned()){
            throw new core_kernel_versioning_exception_Exception(__('the resource has already been versioned : ' . $filePath));
        }*/

        //the file does not exist -> EXCEPTION
        if (!$this->fileExists()){
            throw new core_kernel_versioning_exception_Exception(__('Unable to add a file (' . $this->getAbsolutePath() . '). The file does not exist.'));
        }
        
        $returnValue = core_kernel_versioning_FileProxy::singleton()->add($this, $this->getAbsolutePath(), $recursive, $force);
        
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F5 end

        return (bool) $returnValue;
    }

    /**
     * Check if the resource is versioned
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    public function isVersioned()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F8 begin
	    
        if(!GENERIS_VERSIONING_ENABLED){
        	$returnValue = false;
        }
        
        $status = $this->getStatus(array('SHOW_UPDATES'=>false));
        if($status      != VERSIONING_FILE_STATUS_UNVERSIONED
           && $status   != VERSIONING_FILE_STATUS_ADDED)
        {
            $returnValue = true;
        }
        
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F8 end

        return (bool) $returnValue;
    }

    /**
     * Return the history of the resource as an associative array
     * Throw a core_kernel_versioning_exception_VersioningDisabledException 
     * if the constant GENERIS_VERSIONING_ENABLED is set to false
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array
     */
    public function getHistory()
    {
        $returnValue = array();

        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016F9 begin
    
        if(!GENERIS_VERSIONING_ENABLED){
        	throw new core_kernel_versioning_exception_VersioningDisabledException();
        }
        
        if(!is_null($this->getRepository())){
        	$returnValue = core_kernel_versioning_FileProxy::singleton()->gethistory($this, $this->getAbsolutePath());
        }
        
        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016F9 end

        return (array) $returnValue;
    }

    /**
     * Get the relative path of the resource in the repository
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function getPath()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:0000000000001708 begin
        
       	$versionedFilePathProp = new core_kernel_classes_Property(PROPERTY_VERSIONEDFILE_FILEPATH);
	    $returnValue = $this->getOnePropertyValue($versionedFilePathProp);
        
        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:0000000000001708 end

        return (string) $returnValue;
    }

    /**
     * Check if the content of the local version is different
     * from the remote version of the file.
     * Throw a core_kernel_versioning_exception_VersioningDisabledException 
     * if the constant GENERIS_VERSIONING_ENABLED is set to false
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    public function hasLocalChanges()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-50a804cb:13317e3246f:-8000:0000000000001712 begin
    
        $returnValue = $this->getStatus() == VERSIONING_FILE_STATUS_MODIFIED;
        
        // section 127-0-1-1-50a804cb:13317e3246f:-8000:0000000000001712 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getVersion
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return int
     */
    public function getVersion()
    {
        $returnValue = (int) 0;

        // section 127-0-1-1-750fdd52:133644e7bdd:-8000:0000000000001740 begin
        
        $history = $this->getHistory();
        $returnValue = count($history);
        
        // section 127-0-1-1-750fdd52:133644e7bdd:-8000:0000000000001740 end

        return (int) $returnValue;
    }

    /**
     * Short description of method getStatus
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array options
     * @return int
     */
    public function getStatus($options = array())
    {
        $returnValue = (int) 0;

        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001900 begin
    
        if(!GENERIS_VERSIONING_ENABLED){
        	throw new core_kernel_versioning_exception_VersioningDisabledException();
        }
        
        $svnStatusOptions = array();
        $defaultSvnStatusOptions = array('SHOW_UPDATES' => true);
        $svnStatusOptions = array_merge($defaultSvnStatusOptions, $options);
        
        $returnValue = core_kernel_versioning_FileProxy::singleton()->getStatus($this, $this->getAbsolutePath(), $svnStatusOptions);
        
        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001900 end

        return (int) $returnValue;
    }

    /**
     * Short description of method resolve
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string version
     * @return boolean
     */
    public function resolve($version)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001926 begin
        
        if(!GENERIS_VERSIONING_ENABLED){
        	throw new core_kernel_versioning_exception_VersioningDisabledException();
        }
        
        switch($version){
            case VERSIONING_FILE_VERSION_MINE:
            case VERSIONING_FILE_VERSION_THEIRS:
            case VERSIONING_FILE_VERSION_WORKING:
            case VERSIONING_FILE_VERSION_BASE:
                break;
            default:
                throw new common_Exception('Invalid Argument');
        }
        
        $returnValue = core_kernel_versioning_FileProxy::singleton()->resolve($this, $this->getAbsolutePath(), $version);
        
        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001926 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isInConflict
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    public function isInConflict()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001929 begin
        
        $returnValue = $this->getStatus()==VERSIONING_FILE_STATUS_CONFLICTED;
        
        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001929 end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_versioning_File */

?>
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
        
        $add = isset($option['add']) ? $option['add'] : false;
        $commit = isset($option['commit']) ? $option['commit'] : false;
        //$create = isset($option['create']) ? $option['create'] : false;
        
        $repositoryPath = $repository->getPath();
        //add a slash at the end of the repository path if it does not exist
        $repositoryPath = substr($repositoryPath,strlen($repositoryPath)-1,1)=='/' ? $repositoryPath : $repositoryPath.'/';
        //remove the slash of the file relative path if it exists
        $relativeFilePath = count($relativeFilePath)&&$relativeFilePath[0]=='/' ? substr($relativeFilePath,1) : $relativeFilePath;
        //build the file path
        $filePath = $repositoryPath.$relativeFilePath;
        
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
        	throw new core_kernel_versioning_Exception(__('A file with the name "'.$fileName.'" already exists at the location '.$repositoryPath.$filePath));
        }   
        
        // If the file does not exist, create it
        /*if(!file_exists($repositoryPath.$filePath.'/'.$fileName)){
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
        
        // Auto-commit the file ?
        if($add){
        	$returnValue->add();
        }
        if($commit){
        	$returnValue->commit();
        }
        
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
     * Throw a core_kernel_versioning_VersioningDisabledException 
     * if the constant GENERIS_VERSIONING_ENABLED is set to false
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
        	throw new core_kernel_versioning_VersioningDisabledException();
        }
        
        $returnValue = core_kernel_versioning_FileProxy::singleton()->commit($this, $message, $this->getAbsolutePath(), $recursive);
        
        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000032F5 end

        return (bool) $returnValue;
    }

    /**
     * Update changes from the remote repository
     * Throw a core_kernel_versioning_VersioningDisabledException 
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
        	throw new core_kernel_versioning_VersioningDisabledException();
        }
        
        $returnValue = core_kernel_versioning_FileProxy::singleton()->update($this, $this->getAbsolutePath(), $revision);
        
        // section 127-0-1-1--a63bd74:132c9c69076:-8000:00000000000032F7 end

        return (bool) $returnValue;
    }

    /**
     * Revert changes
     * Throw a core_kernel_versioning_VersioningDisabledException 
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
        	throw new core_kernel_versioning_VersioningDisabledException();
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
        
        if(GENERIS_VERSIONING_ENABLED && $this->fileExists()){
        	$filePath = $this->getAbsolutePath();
        	$returnValue = core_kernel_versioning_FileProxy::singleton()->delete($this, $filePath);
        	if($this->isVersioned()){
		        $this->commit(__('delete the file').' '.$filePath, false);
        	}else{
                //throw an exception, the file is not versioned, it is impossible actually ...
            }
        }
	    
        parent::delete();
        
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
     * Throw a core_kernel_versioning_VersioningDisabledException 
     * if the constant GENERIS_VERSIONING_ENABLED is set to false
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  boolean recursive
     * @return boolean
     */
    public function add($recursive = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F5 begin
        
        if(!GENERIS_VERSIONING_ENABLED){
        	throw new core_kernel_versioning_VersioningDisabledException();
        }
        
        //Check if the path is versioned
        $relativePath = $this->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_VERSIONEDFILE_FILEPATH));
        $filePath = realpath($this->getRepository()->getPath().'/'.$relativePath);
        $relativeFilePathExploded = explode('/', $relativePath);
        $breadCrumb = realpath($this->getRepository()->getPath());
        foreach ($relativeFilePathExploded as $bread){
            
        	$breadCrumb = realpath($breadCrumb.'/'.$bread);
        	if(empty($bread)){
                continue;
            }else if ($breadCrumb == $filePath){
                continue;
            }
            
            $isVersioned = core_kernel_versioning_FileProxy::singleton()->isVersioned($this, $breadCrumb)?'true':'false';
        	if(!core_kernel_versioning_FileProxy::singleton()->isVersioned($this, $breadCrumb)){
        		core_kernel_versioning_FileProxy::singleton()->add($this, $breadCrumb);
        		core_kernel_versioning_FileProxy::singleton()->commit($this, "[sys] Add directory to the repository", $breadCrumb);
        	}
        }
        
        //the file was already versioned -> EXCEPTION
        if($this->isVersioned()){
        	throw new core_kernel_versioning_Exception(__('the resource has already been versioned : '.$filePath));
        }
        
        //the file does not exist -> EXCEPTION
        if($this->fileExists()){
	        $returnValue = core_kernel_versioning_FileProxy::singleton()->add($this, $this->getAbsolutePath(), $recursive);
        }
        else{
	        throw new core_kernel_versioning_Exception(__('Unable to add a file ('.$this->getAbsolutePath().'). The file does not exist.'));
        }
        
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
        else if(is_null($this->getRepository())){
            $returnValue = false;
        }
        else{
        	$returnValue = core_kernel_versioning_FileProxy::singleton()->isVersioned($this, $this->getAbsolutePath());
        }
        
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F8 end

        return (bool) $returnValue;
    }

    /**
     * Return the history of the resource as an associative array
     * Throw a core_kernel_versioning_VersioningDisabledException 
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
        	throw new core_kernel_versioning_VersioningDisabledException();
        }
        else if(!is_null($this->getRepository()) && $this->getRepository()->authenticate()){
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
     * Throw a core_kernel_versioning_VersioningDisabledException 
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
    
        if(!GENERIS_VERSIONING_ENABLED){
        	throw new core_kernel_versioning_VersioningDisabledException();
        }
        
        $returnValue = core_kernel_versioning_FileProxy::singleton()->hasLocalChanges($this, $this->getAbsolutePath());
        
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

} /* end of class core_kernel_versioning_File */

?>
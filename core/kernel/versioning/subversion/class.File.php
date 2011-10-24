<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 21.10.2011, 16:23:23 with ArgoUML PHP module 
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
 * include core_kernel_versioning_FileInterface
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/versioning/interface.FileInterface.php');

/* user defined includes */
// section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:00000000000024D0-includes begin
// section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:00000000000024D0-includes end

/* user defined constants */
// section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:00000000000024D0-constants begin
// section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:00000000000024D0-constants end

/**
 * Short description of class core_kernel_versioning_subversion_File
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning_subversion
 */
class core_kernel_versioning_subversion_File
        implements core_kernel_versioning_FileInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var File
     */
    private static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method commit
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string message
     * @param  string path
     * @return boolean
     */
    public function commit( core_kernel_classes_File $resource, $message, $path)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165A begin
        
        $returnValue = svn_commit($message, array($path))===false ? false : true;

        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165A end

        return (bool) $returnValue;
    }

    /**
     * Short description of method update
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  int revision
     * @return boolean
     */
    public function update( core_kernel_classes_File $resource, $path, $revision = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165C begin
        
        $returnValue = svn_update($path, $revision)===false ? false : true;
        
        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165C end

        return (bool) $returnValue;
    }

    /**
     * Short description of method revert
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  int revision
     * @param  string msg
     * @return boolean
     */
    public function revert( core_kernel_classes_File $resource, $revision = null, $msg = "")
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165E begin
        
        //no revision, revert local change
        if (is_null($revision)){
        	
        	$returnValue = svn_revert($resource->getAbsolutePath());
        }
        else{
        	
        	$path = realpath($resource->getAbsolutePath());
        	
        	//destroy the existing version
        	unlink($path);
        	//replace with the target revision
        	$resource->update($revision);
        	//get old content
        	$content = $resource->getFileContent();
        	//update to the current version
        	$resource->update();
        	//set the new content
        	$resource->setContent($content);
        	//commit the change
        	$resource->commit($msg);
        	
        	/*
        	$repository = $resource->getRepository();
        	$repositoryUrl = $repository->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL));
        	$repositoryLogin = $repository->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN));
        	$repositoryPassword = $repository->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PASSWORD));
        	$defaultRespositoryPath = GENERIS_FILES_PATH.'/versioning/TMP_REVERT_REPOSITORY';
        	
        	$fileName = $resource->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_FILE_FILENAME));
        	$filePath = (string) $resource->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_VERSIONEDFILE_FILEPATH));
        	
        	$tmpRepository = core_kernel_versioning_Repository::create(
				new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAOItem.rdf#VersioningRepositoryTypeSubversion'),
				$repositoryUrl.$filePath.$fileName,
				$repositoryLogin,
				$repositoryPassword,
				$defaultRespositoryPath,
				'TMP Revert Repository',
				'TMP Revert Repository'
			);
			$tmpRepository->checkout($revision);
			
			$instance = core_kernel_versioning_File::create($fileName, $resource->getPath(), $tmpRepository);
		    $content = $instance->getFileContent();
			
		    $resource->setContent($content);
		    $resource->commit($msg);
			
		    $instance->delete();
			//$tmpRepository->delete();  */
        }
        
        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165E end

        return (bool) $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return boolean
     */
    public function delete( core_kernel_classes_File $resource, $path)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:0000000000001678 begin
        
        $returnValue = svn_delete($path, true);
        
        // section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:0000000000001678 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getVersion
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @return string
     */
    public function getVersion( core_kernel_versioning_File $resource)
    {
        $returnValue = (string) '';

        // section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:00000000000024D2 begin
        // section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:00000000000024D2 end

        return (string) $returnValue;
    }

    /**
     * Short description of method add
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return boolean
     */
    public function add( core_kernel_classes_File $resource, $path)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F1 begin
        
        $returnValue = svn_add($path, false);
        
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F1 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isVersioned
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return boolean
     */
    public function isVersioned( core_kernel_classes_File $resource, $path)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016FA begin
        
        $status = svn_status($path);
        // If the file has a status, check the status is not unversioned or added
        if(!empty($status)){
        	
        	$text_status = $status[0]['text_status'];
        	if($text_status		!= SVN_WC_STATUS_UNVERSIONED	// 2. FILE UNVERSIONED
        		&& $text_status	!= SVN_WC_STATUS_ADDED			// 4. JUST ADDED FILE
        	){
        		// 6. SVN_WC_STATUS_DELETED
        		// 7. SVN_WC_STATUS_REPLACED
        		// 8. SVN_WC_STATUS_MODIFIED
        		$returnValue = true;
        	}
        } 
        else {
        	if (file_exists($path)){
        		$returnValue = true;
        	}
        }
        
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016FA end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isUnversioned
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return boolean
     */
    public function isUnversioned( core_kernel_classes_File $resource, $path)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016F0 begin
        
        $status = svn_status($path);
        if(is_array($status) && isset($status['text_status']) && $status['text_status']=='SVN_WC_STATUS_UNVERSIONED'){
        	$returnValue = true;
        }
        
        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016F0 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getHistory
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return array
     */
    public function getHistory( core_kernel_classes_File $resource, $path)
    {
        $returnValue = array();

        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016FB begin
        
        return svn_log($path);
        
        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016FB end

        return (array) $returnValue;
    }

    /**
     * Short description of method hasLocalChanges
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return boolean
     */
    public function hasLocalChanges( core_kernel_classes_File $resource, $path)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--485428cc:133267d2802:-8000:0000000000001732 begin
    
        $status = svn_status($path);
        // If the file has a status, check the status is not unversioned or added
        if(!empty($status)){
        	
        	$text_status = $status[0]['text_status'];
        	if($text_status		== SVN_WC_STATUS_MODIFIED){	// 8. SVN_WC_STATUS_MODIFIED
        		$returnValue = true;
        	}
        }
        
        // section 127-0-1-1--485428cc:133267d2802:-8000:0000000000001732 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_classes_File
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--548d6005:132d344931b:-8000:00000000000016A6 begin
        
        if(is_null(self::$instance)){
			self::$instance = new core_kernel_versioning_subversion_File();
		}
		$returnValue = self::$instance;
		
        // section 127-0-1-1--548d6005:132d344931b:-8000:00000000000016A6 end

        return $returnValue;
    }

} /* end of class core_kernel_versioning_subversion_File */

?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/versioning/class.Repository.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 12.11.2012, 15:46:08 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_versioning
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Resource implements rdf:resource container identified by an uri (a string).
 * Methods enable meta data management for this resource
 *
 * @author patrick.plichart@tudor.lu
 * @see http://www.w3.org/RDF/
 * @version v1.0
 */
require_once('core/kernel/classes/class.Resource.php');

/**
 * include core_kernel_versioning_RepositoryProxy
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('core/kernel/versioning/class.RepositoryProxy.php');

/* user defined includes */
// section 127-0-1-1--548d6005:132d344931b:-8000:0000000000002519-includes begin
// section 127-0-1-1--548d6005:132d344931b:-8000:0000000000002519-includes end

/* user defined constants */
// section 127-0-1-1--548d6005:132d344931b:-8000:0000000000002519-constants begin
// section 127-0-1-1--548d6005:132d344931b:-8000:0000000000002519-constants end

/**
 * Short description of class core_kernel_versioning_Repository
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package core
 * @subpackage kernel_versioning
 */
class core_kernel_versioning_Repository
    extends core_kernel_classes_Resource
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute authenticated
     *
     * @access private
     * @var boolean
     */
    private $authenticated = false;

    // --- OPERATIONS ---

    /**
     * Repository factory
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource type
     * @param  string url
     * @param  string login
     * @param  string password
     * @param  string path
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_versioning_subversion_Repository
     */
    public static function create( core_kernel_classes_Resource $type, $url, $login, $password, $path, $label, $comment, $uri = '')
    {
        $returnValue = null;

        // section 127-0-1-1--548d6005:132d344931b:-8000:000000000000251D begin
        
        //add directory separator at the end of the repository path
        
        $path = rtrim($path, "\\/") . DIRECTORY_SEPARATOR;
        
        $versioningRepositoryClass = new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDREPOSITORY);
        
        $repository = $versioningRepositoryClass->createInstance($label, $comment, $uri);
		
        $versioningRepositoryUrlProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL);
		$repository->setPropertyValue($versioningRepositoryUrlProp, $url);
		
		$versioningRepositoryPathProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH);
		$repository->setPropertyValue($versioningRepositoryPathProp, $path);
		
		$versioningRepositoryTypeProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE);
		$repository->setPropertyValue($versioningRepositoryTypeProp, $type);
		
		$versioningRepositoryLoginProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN);
		$repository->setPropertyValue($versioningRepositoryLoginProp, $login);
		
		$versioningRepositoryPasswordProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PASSWORD);
		$repository->setPropertyValue($versioningRepositoryPasswordProp, $password);
		
		$returnValue = new core_kernel_versioning_Repository($repository->uriResource);
		
        // section 127-0-1-1--548d6005:132d344931b:-8000:000000000000251D end

        return $returnValue;
    }

    /**
     * Checkout the remote repository to a local directory
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  int revision
     * @return boolean
     */
    public function checkout($revision = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--548d6005:132d344931b:-8000:000000000000251A begin
        $VersioningRepositoryUrlProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL);
		$url = (string)$this->getOnePropertyValue($VersioningRepositoryUrlProp);
		
		$VersioningRepositoryPathProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH);
		$path = (string)$this->getOnePropertyValue($VersioningRepositoryPathProp);
		
//        if ($this->authenticate()){
        	$returnValue = core_kernel_versioning_RepositoryProxy::singleton()->checkout($this, $url, $path, $revision);
//        }
        
        // section 127-0-1-1--548d6005:132d344931b:-8000:000000000000251A end

        return (bool) $returnValue;
    }

    /**
     * Get the repository type
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public function getVCSType()
    {
        $returnValue = null;

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016D7 begin
        $returnValue = $this->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE));
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016D7 end

        return $returnValue;
    }

    /**
     * Get path of the local repository
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getPath()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016D9 begin
        
        $returnValue = (string) $this->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH));
        
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016D9 end

        return (string) $returnValue;
    }

    /**
     * Get authenticated with the remote repository
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return boolean
     */
    public function authenticate()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016EB begin
          
        if($this->authenticated){
        	
        	$returnValue = true;
        } else {
        	
        	
	        $VersioningRepositoryLoginProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN);
			$login = (string) $this->getOnePropertyValue($VersioningRepositoryLoginProp);
			
			$VersioningRepositoryPasswordProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PASSWORD);
			$password = (string) $this->getOnePropertyValue($VersioningRepositoryPasswordProp); 
			
			$returnValue = $this->authenticated = core_kernel_versioning_RepositoryProxy::singleton()->authenticate($this, $login, $password);

        }
		
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016EB end

        return (bool) $returnValue;
    }

    /**
     * Delete the repository.
     * Be carrefull, the function does not delete the directory in the file 
     * system.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  boolean deleteReference
     * @return boolean
     */
    public function delete($deleteReference = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016F7 begin
        
        $path = $this->getPath();
        /* remove the resource implies other consequence, do not remove 
        if(is_dir($path)){
        	// Remove the local copy
        	tao_helpers_File::remove($path, true);
        }*/
        
        $returnValue = parent::delete();
        
        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016F7 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method export
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string src
     * @param  string target
     * @param  int revision
     * @return boolean
     */
    public function export($src, $target, $revision = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:00000000000028FD begin
        $returnValue = core_kernel_versioning_RepositoryProxy::singleton()->export($this, $src, $target, $revision);
        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:00000000000028FD end

        return (bool) $returnValue;
    }

    /**
     * @exception core_kernel_versioning_ResourceAlreadyExistsException
     * @exception common_exception_fileAlreadyExists
     * @param options.saveResource {boolean} Save the resource in the onthology
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string src
     * @param  string target
     * @param  string message
     * @param  array options
     * @return core_kernel_versioning_File
     */
    public function import($src, $target, $message = "", $options = array())
    {
        $returnValue = null;

        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:0000000000002904 begin
        //the src has to be a folder for the moment
        if(!is_dir($src)){
            throw new core_kernel_versioning_exception_Exception('The first parameter has to be a valid folder');
        }
        
        $repositoryUrl = $this->getUrl();
        if(strstr($target, $repositoryUrl) === false){
            throw new core_kernel_versioning_exception_Exception('The parameter target ('.$target.') does not match the repository url ('.$repositoryUrl.')');
        }
        
        $returnValue = core_kernel_versioning_RepositoryProxy::singleton()->import($this, $src, $target, $message, $options);
        
        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:0000000000002904 end

        return $returnValue;
    }

    /**
     * Short description of method listContent
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string path
     * @param  int revision
     * @return array
     */
    public function listContent($path, $revision = null)
    {
        $returnValue = array();

        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:0000000000002908 begin
        $returnValue = core_kernel_versioning_RepositoryProxy::singleton()->listContent($this, $path, $revision);
        // section 127-0-1-1--7db71b94:134477a2b9c:-8000:0000000000002908 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getUrl
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getUrl()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-6006a946:134f026c0e2:-8000:00000000000018FF begin
        
        $returnValue = $this->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL));
        
        // section 127-0-1-1-6006a946:134f026c0e2:-8000:00000000000018FF end

        return (string) $returnValue;
    }

    /**
     * Short description of method enable
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return boolean
     */
    public function enable()
    {
        $returnValue = (bool) false;

        // section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000004F63 begin
        if ($this->authenticate()) {
        	if($this->checkout()){
        		$this->editPropertyValues(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_ENABLED), GENERIS_TRUE);
				common_Logger::i("The remote versioning repository ".$this->getUri()." is bound to TAO.");
        		$returnValue = true;
        	}
        }
        // section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000004F63 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method disable
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return boolean
     */
    public function disable()
    {
        $returnValue = (bool) false;

        // section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000004F65 begin
        $classVersionedFiles = new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDFILE);
        $count = $classVersionedFiles->countInstances(array(
        	PROPERTY_VERSIONEDFILE_REPOSITORY => $this
        ), array('like' => false));
        if ($count == 0) {
			$this->editPropertyValues(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_ENABLED), GENERIS_FALSE);
			common_Logger::i("The remote versioning repository ".$this->getUri()." has been disabled");
			$returnValue = true;
        } else {
			common_Logger::w("The remote versioning repository ".$this->getUri()." could not be disabled, because it is in use");
        }
        // section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000004F65 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method addFile
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  File file
     * @param  string remotePath
     * @return core_kernel_versioning_File
     */
    public function addFile( core_kernel_classes_File $file, $remotePath = '')
    {
        $returnValue = null;

        // section 10-30-1--78-e79fa48:13af3e783af:-8000:0000000000005035 begin
		
		$destination = $this->getPath().$remotePath;
		$source = $file->getAbsolutePath();
		if(tao_helpers_File::move($source, $destination)){
			$returnValue = core_kernel_versioning_File::create('', $remotePath, $this);
			if(!is_null($returnValue)){
				$file->delete();
			}
		}
		
        // section 10-30-1--78-e79fa48:13af3e783af:-8000:0000000000005035 end

        return $returnValue;
    }

} /* end of class core_kernel_versioning_Repository */

?>

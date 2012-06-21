<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/ext/class.SimpleExtension.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 15.06.2012, 17:34:52 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 * @subpackage ext
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017AF-includes begin
// section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017AF-includes end

/* user defined constants */
// section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017AF-constants begin
// section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017AF-constants end

/**
 * Short description of class common_ext_SimpleExtension
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 * @subpackage ext
 */
class common_ext_SimpleExtension
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute manifest
     *
     * @access public
     * @var string
     */
    public $manifest = '';

    /**
     * Short description of attribute author
     *
     * @access public
     * @var string
     */
    public $author = '';

    /**
     * Short description of attribute name
     *
     * @access public
     * @var string
     */
    public $name = '';

    /**
     * Short description of attribute version
     *
     * @access public
     * @var string
     */
    public $version = '';

    /**
     * Short description of attribute requiredExtensionsList
     *
     * @access public
     * @var array
     */
    public $requiredExtensionsList = array();

    /**
     * Short description of attribute registerToClassLoader
     *
     * @access public
     * @var boolean
     */
    public $registerToClassLoader = false;

    /**
     * Short description of attribute classLoaderPackages
     *
     * @access public
     * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
     * @var array
     */
    public $classLoaderPackages = array();

    /**
     * Short description of attribute configFile
     *
     * @access public
     * @var Integer
     */
    public $configFile = null;

    /**
     * Short description of attribute classLoaderFiles
     *
     * @access public
     * @var array
     */
    public $classLoaderFiles = array();

    /**
     * Short description of attribute installFiles
     *
     * @access public
     * @var array
     */
    public $installFiles = array();

    /**
     * Short description of attribute id
     *
     * @access public
     * @var string
     */
    public $id = '';

    /**
     * Short description of attribute configuration
     *
     * @access public
     * @var ExtensionConfiguration
     */
    public $configuration = null;

    /**
     * Short description of attribute parentID
     *
     * @access protected
     * @var string
     */
    protected $parentID = '';

    /**
     * Short description of attribute installed
     *
     * @access public
     * @var boolean
     */
    public $installed = false;

    // --- OPERATIONS ---

    /**
     * Should not be called directly, please use ExtensionsManager
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string id
     * @param  boolean installed
     * @return mixed
     */
    public function __construct($id, $installed = false)
    {
        // section -87--2--3--76--148ee98a:12452773959:-8000:0000000000002320 begin
    	$this->manifest = EXTENSION_PATH.'/'.$id.'/'.MANIFEST_NAME;
		$this->id = $id;
		$this->installed = $installed;
		if(is_file($this->manifest)){
			$manifestArray = require $this->manifest;
			$this->author = $manifestArray['additional']['author'];
			$this->name = $manifestArray['name'];
			$this->version = $manifestArray['additional']['version'];
			
			if(isset($manifestArray['additional']['install']) && is_array($manifestArray['additional']['install'])) {
				$this->installFiles = $manifestArray['additional']['install'];
			}
			if(isset($manifestArray['additional']['registerToClassLoader'])){
				$this->registerToClassLoader =$manifestArray['additional']['registerToClassLoader'];
			}
			$list = $manifestArray['additional']['dependances'];
			if(!is_array($list) && !empty($list)){
				$list = array($list);
			}
			foreach ($list as $ext) {
				$this->requiredExtensionsList[] = $ext;
			}
			if(isset($manifestArray['additional']['extends'])) {
				$this->parentID = $manifestArray['additional']['extends'];
				if (!in_array($this->parentID, $this->requiredExtensionsList)) {
	        		$this->requiredExtensionsList[] = $this->parentID;
				} 
	        }
			
			if(isset($manifestArray['additional']['classLoaderPackages'])) {
				$this->classLoaderPackages = $manifestArray['additional']['classLoaderPackages'];	
			}
	    	if(isset($manifestArray['additional']['configFile'])) {
				$this->configFile = $manifestArray['additional']['configFile'];	
			}
			if(isset($manifestArray['additional']['models'])) {
				if(!is_array($manifestArray['additional']['models']) && !empty($manifestArray['additional']['models'])){
					$this->model = array($manifestArray['additional']['models']);
				}
				else{
					$this->model = $manifestArray['additional']['models'];	
				}
			}
			if(isset($manifestArray['additional']['modelsRight']) && !empty ($manifestArray['additional']['modelsRight'])) {
				$this->modelsRight = $manifestArray['additional']['modelsRight'];
			}
			if(isset($manifestArray['additional']['install']) && is_array($manifestArray['additional']['install'])) {
				$this->installFiles = $manifestArray['additional']['install'];
			}
		}
		else {
			//Here the extension is set unvalided to not be displayed by the view
			throw new common_ext_ManifestNotFoundException("Extension Manifest not found for extension '${id}'.", $id);
		}

        // section -87--2--3--76--148ee98a:12452773959:-8000:0000000000002320 end
    }

    /**
     * Short description of method getConfiguration
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return common_ext_ExtensionConfiguration
     */
    public function getConfiguration()
    {
        $returnValue = null;

        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:00000000000023A0 begin
        if($this->configuration == null) {
        	$db = core_kernel_classes_DbWrapper::singleton();
			$query = "SELECT loaded,\"loadAtStartUp\",ghost FROM extensions WHERE id ='".$this->id."';";
			$result = $db->execSql($query);
			$loaded = $result->fields["loaded"] == 1;
			$loadedAtStartUp = $result->fields["loadAtStartUp"] == 1;
			$ghost = $result->fields["ghost"] == 1;
			$this->configuration = new common_ext_ExtensionConfiguration($loaded,$loadedAtStartUp, $ghost);

        }
        return $this->configuration;
        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:00000000000023A0 end

        return $returnValue;
    }

    /**
     * Short description of method getID
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getID()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A17 begin
        return $this->id;
        // section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A17 end

        return (string) $returnValue;
    }

} /* end of class common_ext_SimpleExtension */

?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/ext/class.ExtensionsManager.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 24.05.2012, 15:25:09 with ArgoUML PHP module 
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
// section -87--2--3--76-e9002fe:123ebbb9fa8:-8000:0000000000001799-includes begin
// section -87--2--3--76-e9002fe:123ebbb9fa8:-8000:0000000000001799-includes end

/* user defined constants */
// section -87--2--3--76-e9002fe:123ebbb9fa8:-8000:0000000000001799-constants begin
// section -87--2--3--76-e9002fe:123ebbb9fa8:-8000:0000000000001799-constants end

/**
 * Short description of class common_ext_ExtensionsManager
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 * @subpackage ext
 */
class common_ext_ExtensionsManager
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute extensions
     *
     * @access private
     * @var array
     */
    private $extensions = array();

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var ExtensionsManager
     */
    private static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return common_ext_ExtensionsManager
     */
    public static function singleton()
    {
        $returnValue = null;

        // section -87--2--3--76--148ee98a:12452773959:-8000:000000000000233B begin
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c();
		}
		$returnValue = self::$instance;
        // section -87--2--3--76--148ee98a:12452773959:-8000:000000000000233B end

        return $returnValue;
    }

    /**
     * Short description of method getInstalledExtensions
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getInstalledExtensions()
    {
        $returnValue = array();

        // section -87--2--3--76-e9002fe:123ebbb9fa8:-8000:000000000000179E begin
		if(empty($this->extensions)){

			$db = core_kernel_classes_DbWrapper::singleton();
			$query = "SELECT * FROM extensions;";
			$result = $db->execSql($query);
			if($db->dbConnector->errorNo() !== 0){
				throw new core_kernel_persistence_hardapi_Exception($db->dbConnector->errorMsg());
			}

			while (!$result-> EOF){
				$id = $result->fields["id"];
				$extension = new common_ext_SimpleExtension($id);

				$extension->configuration = new common_ext_ExtensionConfiguration(
					($result->fields["loaded"] == 1),
					($result->fields["loadAtStartUp"] == 1),
					($result->fields["ghost"] == 1),
					$result->fields["version"]
				);

				$this->extensions[$id] = $extension;
				$result->MoveNext();
			}
		}
		$returnValue = $this->extensions;
        // section -87--2--3--76-e9002fe:123ebbb9fa8:-8000:000000000000179E end

        return (array) $returnValue;
    }

    /**
     * Short description of method isExtensionInstalled
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string extension
     * @return boolean
     */
    public function isExtensionInstalled($extension)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001902 begin
        $extensions = $this->getInstalledExtensions();
        $returnValue = isset($extensions[$extension]);
        // section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001902 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isExtensionEnabled
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string extension
     * @return boolean
     */
    public function isExtensionEnabled($extension)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4e48a7c:136ee1b3246:-8000:00000000000019D8 begin
        $extensions = $this->getInstalledExtensions();
        if (isset($extensions[$extension])) {
        	$conf = $extensions[$extension]->getConfiguration();
        	if (!$conf->ghost) {
        		$returnValue = true;
        	}
        }
        // section 127-0-1-1-4e48a7c:136ee1b3246:-8000:00000000000019D8 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method addExtension
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string id
     * @param  string extensionsZipPath
     * @return mixed
     */
    public function addExtension($id, $extensionsZipPath)
    {
        // section -87--2--3--76-e9002fe:123ebbb9fa8:-8000:00000000000017A0 begin
		$fileUnzip = new fileUnzip($package_zip);
		$fileUnzip->unzipAll(EXTENSION_PATH);
		$newExt = new common_ext_SimpleExtension($id);
		$extInstaller = new common_ext_ExtensionInstaller($newExt);
		$extInstaller->install();
        // section -87--2--3--76-e9002fe:123ebbb9fa8:-8000:00000000000017A0 end
    }

    /**
     * remove Extension from the database, filesystem is not change
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  extension
     * @return mixed
     */
    public function removeExtension($extension)
    {
        // section -87--2--3--76-e9002fe:123ebbb9fa8:-8000:00000000000017A3 begin
		foreach($this->getExtensionList() as $ext) {
			$required = $ext->getRequiredExtensions();


		}
        // section -87--2--3--76-e9002fe:123ebbb9fa8:-8000:00000000000017A3 end
    }

    /**
     * Load all extensions that have to be loaded
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function loadExtensions()
    {
        // section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017B4 begin

		if(count($this->extensions) == 0){
			$this->getInstalledExtensions(); //init at first load;
		}
		foreach($this->extensions as $extension) {
			$extensionLoader = new common_ext_ExtensionLoader($extension);

			//handle dependances requirement
			foreach ($extension->requiredExtensionsList as $ext) {
				if(!array_key_exists($ext, $this->extensions) && $ext != 'generis') {
					throw new common_ext_ExtensionException('Required Extension is Missing : ' . $ext);
				}
			}
			$config = $extension->getConfiguration();
			if($config->loadedAtStartUp){
				$extensionLoader->load();
			}
		}
        // section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017B4 end
    }

    /**
     * Short description of method __construct
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    private function __construct()
    {
        // section -87--2--3--76--148ee98a:12452773959:-8000:000000000000233D begin
        // section -87--2--3--76--148ee98a:12452773959:-8000:000000000000233D end
    }

    /**
     * Call a service to retrieve list of extensions that may be installed
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function getAvailableExtensions()
    {
        // section -87--2--3--76--148ee98a:12452773959:-8000:0000000000002364 begin
		$result = array(
		'testExtension' => array (
				'zip' => dirname(__FILE__).'/test/common/testExtension.zip',
				'author' => 'CRP Henri Tudor',
				'name' => 'testExtensionZip',
				'description' => 'Sample Test Extension to test Ext Mechanism',
				'version' => '0.25'
				)
		);
		$returnValue = array_diff_key($result,$this->getInstalledExtensions());

		return $returnValue;
        // section -87--2--3--76--148ee98a:12452773959:-8000:0000000000002364 end
    }

    /**
     * modify the configuration
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array configurationArray array(extensionid =>configuration)
     * @return mixed
     */
    public function modifyConfigurations($configurationArray)
    {
        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:0000000000002383 begin
		foreach ($configurationArray as $id => $configuration) {
			$ext = new common_ext_SimpleExtension($id);
//			TODO var_dump($ext->requiredExtensionsList);
			foreach ($ext->requiredExtensionsList as $id) {
//				var_dump($configurationArray[$id]);
			}

			//throw new common_ext_ExtensionException(__('Extension '). $ext->id  .__( ' could not be removed :'). $e->getMessage());
			$configuration->save($ext);

		}

        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:0000000000002383 end
    }

    /**
     * Reset the manager in order to take into account current extensions states
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function reset()
    {
        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:000000000000239B begin
		$this->extensions = array();
        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:000000000000239B end
    }

    /**
     * Short description of method getModelsToLoad
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getModelsToLoad()
    {
        $returnValue = array();

        // section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001840 begin
		foreach ($this->getInstalledExtensions() as $ext) {
			if(isset($ext->model) && count($ext->model) > 0){
				$returnValue = array_merge($returnValue, $ext->model);
			}
		}
		$returnValue = array_unique($returnValue);
        // section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001840 end

        return (array) $returnValue;
    }

    /**
     * Get all the extension dependancies for a given extension
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  SimpleExtension extension
     * @return array
     */
    public function getDependancies( common_ext_SimpleExtension $extension)
    {
        $returnValue = array();

        // section 127-0-1-1--34c6d20a:12dcbf5c5e2:-8000:00000000000014AE begin


        if(is_array($extension->requiredExtensionsList)){
	        if(count($this->extensions) == 0){
				$this->getInstalledExtensions(); //init at first load;
			}
        	$returnValue = $extension->requiredExtensionsList;

        	foreach($extension->requiredExtensionsList as $dependance){
        		if(isset($this->extensions[$dependance]) && $this->extensions[$dependance] instanceof common_ext_SimpleExtension){
        			$returnValue = array_merge($returnValue, $this->getDependancies($this->extensions[$dependance]));
        		}
        	}
        }

        // section 127-0-1-1--34c6d20a:12dcbf5c5e2:-8000:00000000000014AE end

        return (array) $returnValue;
    }

    /**
     * Short description of method getUpdatableModels
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getUpdatableModels()
    {
        $returnValue = array();

        // section 127-0-1-1--450598c3:13175ea282e:-8000:0000000000003C45 begin

    	foreach ($this->getInstalledExtensions() as $ext) {
			if(isset ($ext->modelsRight) && count($ext->modelsRight) > 0){
				if (isset($ext->modelsRight)){
					/*
					 *
					 * TODO
					 * We manage update, add read, delete ..
					 * if the variable exist, the model is updatable!
					 * use a code in the next investigation, such as unix right
					 *
					 */
					foreach ($ext->modelsRight as $model=>$right){
						$ns = common_ext_NamespaceManager::singleton()->getNamespace ($model.'#');
						if ($ns == null) {
							throw new common_ext_ExtensionException("Session Expired, could not get namespace");
						}
						$modelId = $ns->getModelId();
						if (!isset($returnValue[$modelId])){
							$returnValue[$modelId] = $model;
						}
					}
				}
			}
		}

        // section 127-0-1-1--450598c3:13175ea282e:-8000:0000000000003C45 end

        return (array) $returnValue;
    }

} /* end of class common_ext_ExtensionsManager */

?>
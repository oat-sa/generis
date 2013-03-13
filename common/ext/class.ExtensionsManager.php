<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/ext/class.ExtensionsManager.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 03.01.2013, 18:20:51 with ArgoUML PHP module 
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
			self::$instance = new self();
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
        foreach ($this->extensions as $ext) {
        	if ($ext->isInstalled()) {
        		$returnValue[$ext->getID()] = $ext;
        	}
        }
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
        throw new Exception('deprecated function '.__FUNCTION__);
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
        throw new Exception('deprecated function '.__FUNCTION__);
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
		$newExt = $this->getExtensionById($id);
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
		foreach($this->extensions as $extension) {
			$extensionLoader = new common_ext_ExtensionLoader($extension);

			//handle dependances requirement
			foreach ($extension->requiredExtensionsList as $ext) {
				if(!array_key_exists($ext, $this->extensions) && $ext != 'generis') {
					throw new common_ext_ExtensionException('Required Extension is Missing : ' . $ext);
				}
			}
			
			$extensionLoader->load();
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
		$this->loadInstalledExtensions();
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
        $returnValue = array();
		$dir = new DirectoryIterator(ROOT_PATH);
		foreach ($dir as $fileinfo) {
			if ($fileinfo->isDir() && !$fileinfo->isDot() && substr($fileinfo->getBasename(), 0, 1) != '.') {
				$extId = $fileinfo->getBasename();
				try {
					$ext = $this->getExtensionById($extId);
					if (!$ext->isInstalled()) {
						$returnValue[] = $ext;
					}
				} catch (common_ext_ExtensionException $exception) {
					common_Logger::d($extId.' is not an extension');
				}
			}
		}
		
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
			$ext = $this->getExtensionById($id);
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
		$this->loadInstalledExtensions();
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
							throw new common_ext_ExtensionException("Session Expired, could not get namespace for model ".$model);
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

    /**
     * Short description of method getExtensionById
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string id
     * @return common_ext_Extension
     */
    public function getExtensionById($id)
    {
        $returnValue = null;

        // section 127-0-1-1-176d7eef:1379cae211f:-8000:0000000000005DC3 begin
        if (empty($id)) {
        	throw new common_ext_ExtensionException('No id specified for getExtensionById()');
        }
        if (!isset($this->extensions[$id])) {
        	$this->extensions[$id] = new common_ext_Extension($id, false);
        	$extensionLoader = new common_ext_ExtensionLoader($this->extensions[$id]);
        	$extensionLoader->load();
        }
        $returnValue = $this->extensions[$id];
        // section 127-0-1-1-176d7eef:1379cae211f:-8000:0000000000005DC3 end

        return $returnValue;
    }

    /**
     * Short description of method loadInstalledExtensions
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    private function loadInstalledExtensions()
    {
        // section 127-0-1-1--1d51cc99:137f05120f0:-8000:0000000000001A59 begin
        $this->extensions = array();
        
    	$db = core_kernel_classes_DbWrapper::singleton();
		$query = 'SELECT * FROM "extensions"';
		$result = $db->query($query);

		while ($row = $result->fetch()){
			$id = $row["id"];
			$extension = new common_ext_Extension($id, true, $row);
/*
			$extension->configuration = new common_ext_ExtensionConfiguration(
				($row['loaded'] == 1),
				($row['loadAtStartUp'] == 1),
				($row['ghost'] == 1),
				$row['version']
			);
*/
			$this->extensions[$id] = $extension;
		}
        // section 127-0-1-1--1d51cc99:137f05120f0:-8000:0000000000001A59 end
    }

} /* end of class common_ext_ExtensionsManager */

?>
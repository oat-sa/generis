<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common\ext\class.ExtensionInstaller.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 13.06.2012, 16:51:45 with ArgoUML PHP module 
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

/**
 * EXtension Wrapper
 *
 * @author lionel.lecaque@tudor.lu
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */
require_once('common/ext/class.ExtensionHandler.php');

/* user defined includes */
// section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017B9-includes begin
// section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017B9-includes end

/* user defined constants */
// section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017B9-constants begin
// section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017B9-constants end

/**
 * Short description of class common_ext_ExtensionInstaller
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 * @subpackage ext
 */
class common_ext_ExtensionInstaller
    extends common_ext_ExtensionHandler
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * States if local data must be installed or not.
     *
     * @access private
     * @var boolean
     */
    private $localData = false;

    // --- OPERATIONS ---

    /**
     * install an extension
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    public function install()
    {
        // section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017C4 begin
		
    	common_Logger::i('Installing '.$this->extension->getID(), 'INSTALL');
    	
		try{
			
			//check dependances
			if($this->checkRequiredExtensions()){

				$this->installCustomScript();
				$this->installWriteConfig();
				$this->installOntology();
				$this->installLocalData();
				$this->installWriteConfig();
				$this->installRegisterExt();
			}
				
				
		}catch (common_ext_ExtensionException $e){
			throw new common_ext_ExtensionException(__('Problem installing extension '). $this->extension->id .' : '. $e->getMessage());
		}

        // section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017C4 end
    }

    /**
     * check required extensions are not missing
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    protected function checkRequiredExtensions()
    {
        $returnValue = (bool) false;

        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:00000000000023A2 begin
		$extensionManager = common_ext_ExtensionsManager::singleton();
		$installedExtArray = $extensionManager->getInstalledExtensions();
        foreach ($this->extension->requiredExtensionsList as $requiredExt) {
			if(!array_key_exists($requiredExt,$installedExtArray)){
				throw new common_ext_ExtensionException('Extension '. $requiredExt . ' missing');
			}
		}
		$returnValue = true;
        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:00000000000023A2 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method installLocalData
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    protected function installLocalData()
    {
        // section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A22 begin
		$modelCreator = new tao_install_utils_ModelCreator(LOCAL_NAMESPACE);
    	$localData = $this->extension->getDir().'/models/ontology/local.rdf';
		if(file_exists($localData)){
			common_Logger::d('Inserting localdata for '.$this->extension->getID(), 'INSTALL');
			$modelCreator->insertLocalModelFile($localData);
		}
        // section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A22 end
    }

    /**
     * Short description of method installOntology
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    protected function installOntology()
    {
        // section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A24 begin
        // insert model
		$modelCreator = new tao_install_utils_ModelCreator(LOCAL_NAMESPACE);
    	$models = tao_install_utils_ModelCreator::getModelsFromExtensions(array($this->extension));
		foreach ($models as $ns => $modelFiles){
			foreach ($modelFiles as $file) {
				common_Logger::d('Inserting for NS '.$ns.' model '.basename($file), 'INSTALL');
				$modelCreator->insertModelFile($ns, $file);
			}
		}
		// insert translated models
		$models = tao_install_utils_ModelCreator::getTranslationModelsFromExtension($this->extension);
		foreach($models as $ns => $modelFile){
			foreach($modelFile as $mF) {
				common_Logger::d('Inserting translation of model ' . basename($mF) . ' for extension '.$this->extension->getID(), 'INSTALL');
				$modelCreator->insertModelFile($ns, $mF);
			}
		}
        // section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A24 end
    }

    /**
     * Short description of method installWriteConfig
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    protected function installWriteConfig()
    {
        // section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A26 begin
		$sampleFile	= $this->extension->getDir().'includes/config.php.sample';
		$finalFile	= $this->extension->getDir().'/includes/config.php';
		
    	common_Logger::d('Writing config '.$finalFile.' for '.$this->extension->getID(), 'INSTALL');
		$myConfigWriter = new tao_install_utils_ConfigWriter(
			$sampleFile,
			$finalFile
		);
		$myConfigWriter->createConfig();
		
		// @todo solve this
		if ($this->extension->getID() == 'tao') {
			require_once($finalFile);
		}
        // section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A26 end
    }

    /**
     * Short description of method installRegisterExt
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    protected function installRegisterExt()
    {
        // section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A28 begin
    	common_Logger::d('Registering '.$this->extension->getID(), 'INSTALL');
    	
		//add extension to db
		$db = core_kernel_classes_DbWrapper::singleton();
		$sql = "INSERT INTO extensions (id, name, version, loaded, \"loadAtStartUp\") VALUES ('".$this->extension->id."', '".$this->extension->name."', '".$this->extension->version."', 1, 1);";
		$db->execSql($sql);
		
		//flush Manager
		common_ext_ExtensionsManager::singleton()->reset();
		
        // section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A28 end
    }

    /**
     * Short description of method installCustomScript
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    protected function installCustomScript()
    {
        // section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A2C begin
		//install script
		if(isset($manifestArray['additional']['install']['php'])){
			common_Logger::d('Running custom install script '.$manifestArray['additional']['install']['php'].' for ext '.$this->extension->getID(), 'INSTALL');
			require_once $manifestArray['additional']['install']['php'];
		}
        // section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A2C end
    }

    /**
     * Instantiate a new ExtensionInstaller for a given Extension.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Extension extension The extension to install
     * @param  boolean localData Import local data or not.
     * @return mixed
     */
    public function __construct( common_ext_Extension $extension, $localData = true)
    {
        // section -64--88-56-1--cf3e319:137e64d7097:-8000:0000000000001A2B begin
        parent::__construct($extension);
        $this->setLocalData($localData);
        // section -64--88-56-1--cf3e319:137e64d7097:-8000:0000000000001A2B end
    }

    /**
     * Sets localData field.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  boolean value
     * @return mixed
     */
    public function setLocalData($value)
    {
        // section -64--88-56-1--cf3e319:137e64d7097:-8000:0000000000001A3A begin
        $this->localData = $value;
        // section -64--88-56-1--cf3e319:137e64d7097:-8000:0000000000001A3A end
    }

    /**
     * Retrieve localData field
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function getLocalData()
    {
        $returnValue = (bool) false;

        // section -64--88-56-1--cf3e319:137e64d7097:-8000:0000000000001A3D begin
        $returnValue = $this->localData;
        // section -64--88-56-1--cf3e319:137e64d7097:-8000:0000000000001A3D end

        return (bool) $returnValue;
    }

} /* end of class common_ext_ExtensionInstaller */

?>
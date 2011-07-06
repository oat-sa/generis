<?php

/**
 * default action
 * must be in the actions folder
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package action
 */
class generis_actions_ExtensionsManager extends Module {

	/**
	 * Index page
	 */
	public function index() {
		 
		$extensionManager = common_ext_ExtensionsManager::singleton();
		$extensionManager->reset();
		$installedExtArray = $extensionManager->getInstalledExtensions();
		$availlableExtArray = $extensionManager->getAvailableExtensions();
		$this->setData('installedExtArray',$installedExtArray);
		$this->setData('availlableExtArray',$availlableExtArray);
		$this->setView('view.tpl.php');

	}

	public function add( $id , $package_zip ){

		$extensionManager = common_ext_ExtensionsManager::singleton();
		$fileUnzip = new fileUnzip(urldecode($package_zip));
		$fileUnzip->unzipAll(EXTENSION_PATH);
		$newExt = new common_ext_SimpleExtension($id);
		$extInstaller = new common_ext_ExtensionInstaller($newExt);
		try {
			$extInstaller->install();
			$message =   __('Extension ') . $newExt->name . __(' has been installed');
		}
		catch(common_ext_ExtensionException $e) {
			$message = $e->getMessage();
		}

		$this->setData('message',$message);
		$this->index();

	}
	


	public function modify($loaded,$loadAtStartUp){
                
		$extensionManager = common_ext_ExtensionsManager::singleton();
		$installedExtArray = $extensionManager->getInstalledExtensions();
		$configurationArray = array();
		foreach($installedExtArray as $k=>$ext){
			$configuration = new common_ext_ExtensionConfiguration(isset($loaded[$k]),isset($loadAtStartUp[$k]));
			$configurationArray[$k]=$configuration;
		}
		try {
			$extensionManager->modifyConfigurations($configurationArray);
			$message = __('Extensions\' configurations updated ');
		}
		catch(common_ext_ExtensionException $e) {
			$message = $e->getMessage();
		}
		$this->setData('message', $message);
		$this->index();

	}

}
?>
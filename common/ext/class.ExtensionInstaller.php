<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common\ext\class.ExtensionInstaller.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 24.03.2010, 14:38:36 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
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

    // --- OPERATIONS ---

    /**
     * install an extension
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function install()
    {
        // section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017C4 begin

		try{
			
			$db = core_kernel_classes_DbWrapper::singleton();
			//check dependances
			if($this->checkRequiredExtensions()){

				//additionnal data into db
				$manifestArray = require $this->extension->manifest;
				if(isset($manifestArray['additional']['install']['sql'])){
					common_Utils::loadSqlFile($manifestArray['additional']['install']['sql']);
				}

				//install script
				if(isset($manifestArray['additional']['install']['php'])){
					require_once $manifestArray['additional']['install']['php'];
				}
				//add extension to db
				$sql = "INSERT INTO extensions (id, name, version, loaded, \"loadAtStartUp\") VALUES ('".$this->extension->id."', '".$this->extension->name."', '".$this->extension->version."', '', '');";
				$db->execSql($sql);
			}
				
				
		}catch (common_ext_ExtensionException $e){
			throw new common_ext_ExtensionException(__('Problem installing extension '). $this->extension->id .' : '. $e->getMessage());
		}

        // section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017C4 end
    }

    /**
     * check required extensions are not missing
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return boolean
     */
    public function checkRequiredExtensions()
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

} /* end of class common_ext_ExtensionInstaller */

?>
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
 * Generis Object Oriented API - common/ext/class.ExtensionLoader.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 01.03.2013, 12:18:24 with ArgoUML PHP module 
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
// section -87--2--3--76-e9002fe:123ebbb9fa8:-8000:000000000000179B-includes begin
// section -87--2--3--76-e9002fe:123ebbb9fa8:-8000:000000000000179B-includes end

/* user defined constants */
// section -87--2--3--76-e9002fe:123ebbb9fa8:-8000:000000000000179B-constants begin
// section -87--2--3--76-e9002fe:123ebbb9fa8:-8000:000000000000179B-constants end

/**
 * Short description of class common_ext_ExtensionLoader
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 * @subpackage ext
 */
class common_ext_ExtensionLoader
    extends common_ext_ExtensionHandler
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Already loaded configuration and constants file.
     *
     * @access private
     * @var array
     */
    private $loadedFiles = array();

    // --- OPERATIONS ---

    /**
     * Load the extension.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return mixed
     */
    public function load()
    {
        // section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017AD begin
        common_Logger::t('Loading extension ' . $this->extension->getID());
        
        $this->loadConstants();
        $this->loadClasses();
        // section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017AD end
    }

    /**
     * Initializes the class loaders of the extension
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return void
     */
    protected function loadClasses()
    {
        // section 127-0-1-1-62ede985:13d2586a59c:-8000:0000000000001FE5 begin
    	common_Logger::t('Loading extension ' . $this->extension->getId() . ' class loader packages');
    	 
    	$extensions = $this->extension->getDependencies();
    	array_unshift($extensions, $this->extension->getID());
    	
    	$extManager = common_ext_ExtensionsManager::singleton();
    	
    	foreach ($extensions as $extId){
    		$ext = $extManager->getExtensionById($extId);
    		
    		$classLoader = common_ext_ClassLoader::singleton();
    		if(isset($ext->classLoaderPackages)) {
    			foreach($ext->classLoaderPackages as $package) {
    				$classLoader->addPackage($package);
    			}
    		}
    	}
    	
    	$classLoader->register();
        // section 127-0-1-1-62ede985:13d2586a59c:-8000:0000000000001FE5 end
    }

    /**
     * Load the constant and configuration files.
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return void
     */
    protected function loadConstants()
    {
        // section 127-0-1-1-62ede985:13d2586a59c:-8000:0000000000001FE8 begin
        common_Logger::t('Loading extension ' . $this->extension->getId() . ' constants');
        $ctxPath = ROOT_PATH . '/' . $this->extension->getID();
    	
    	if ($this->extension->getID() != "generis"){
    		$ext = common_ext_ExtensionsManager::singleton()->getExtensionById($this->extension->getID());
    		if (count($ext->getConstants()) > 0) {
    			foreach ($ext->getConstants() as $key => $value) {
    				if(!defined($key) && !is_array($value)){
    					define($key, $value);
    				}
    			}
    		}
    		// backward compatibility
    		if (file_exists($ctxPath . "/includes/config.php")) {
    			require_once $ctxPath . "/includes/config.php";
    		}
    	}
    	// we will load the constant file of the current extension and all it's dependancies
    	
    	// get the dependancies
    	$extensions = $this->extension->getDependencies();
    	
    	// merge them with the additional constants (defined in the options)
    	if(isset($this->options['constants'])){
    		if(is_string($this->options['constants'])){
    			$this->options['constants'] = array($this->options['constants']);
    		}
    		$extensions = array_merge($extensions, $this->options['constants']);
    	}
    	// add the current extension (as well !)
    	$extensions = array_merge(array($this->extension->getID()), $extensions);
    	
    	foreach($extensions as $extension){
    	
    		if($extension == 'generis') {
    			continue; //generis constants are already loaded
    		}
    	
    		//load the config of the extension
    		$this->loadConstantsFile($extension);
    	}
        // section 127-0-1-1-62ede985:13d2586a59c:-8000:0000000000001FE8 end
    }

    /**
     * Load a single constant file that belongs to a given extension
     *
     * @access private
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string extensionId The extension ID.
     * @return void
     */
    private function loadConstantsFile($extensionId)
    {
        // section 127-0-1-1-62ede985:13d2586a59c:-8000:0000000000001FEB begin
    	$constantFile = ROOT_PATH . $extensionId .DIRECTORY_SEPARATOR. 'includes' .DIRECTORY_SEPARATOR. 'constants.php';
    	$loadedFiles = $this->getLoadedFiles();
    	if(file_exists($constantFile) && !in_array($constantFile, $loadedFiles)){
    	
    		//include the constant file
    		include_once $constantFile;
    		
    		//this variable comes from the constant file and contain the const definition
    		if(isset($todefine)){
    			foreach($todefine as $constName => $constValue){
    				if(!defined($constName)){
    					define($constName, $constValue);	//constants are defined there!
    				} else {
    					common_Logger::d('Constant '.$constName.' in '.$extensionId.' has already been defined');
    				}
    			}
    			unset($todefine);
    		}
    	}
        // section 127-0-1-1-62ede985:13d2586a59c:-8000:0000000000001FEB end
    }

    /**
     * Get an array of file paths that represent the already loaded constants
     * configuration files.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return array
     */
    public function getLoadedFiles()
    {
        $returnValue = array();

        // section 127-0-1-1-62ede985:13d2586a59c:-8000:0000000000001FF4 begin
        $returnValue = $this->loadedFiles;
        // section 127-0-1-1-62ede985:13d2586a59c:-8000:0000000000001FF4 end

        return (array) $returnValue;
    }

    /**
     * Add a file to the loaded file list.
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string filePath The path to the file.
     * @return void
     */
    protected function addLoadedFile($filePath)
    {
        // section 127-0-1-1-62ede985:13d2586a59c:-8000:0000000000001FF7 begin
        array_push($this->loadedFiles, $filePath);
        $this->loadedFiles = array_unique($this->loadedFiles);
        // section 127-0-1-1-62ede985:13d2586a59c:-8000:0000000000001FF7 end
    }

} /* end of class common_ext_ExtensionLoader */

?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/ext/class.Extension.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 30.05.2012, 11:59:44 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage ext
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_ext_SimpleExtension
 *
 * @author lionel.lecaque@tudor.lu
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */
require_once('common/ext/class.SimpleExtension.php');

/* user defined includes */
// section 127-0-1-1-176d7eef:1379cae211f:-8000:0000000000005DC5-includes begin
// section 127-0-1-1-176d7eef:1379cae211f:-8000:0000000000005DC5-includes end

/* user defined constants */
// section 127-0-1-1-176d7eef:1379cae211f:-8000:0000000000005DC5-constants begin
// section 127-0-1-1-176d7eef:1379cae211f:-8000:0000000000005DC5-constants end

/**
 * Short description of class common_ext_Extension
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage ext
 */
class common_ext_Extension
    extends common_ext_SimpleExtension
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getAllModules
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getAllModules()
    {
        $returnValue = array();

        // section 127-0-1-1-176d7eef:1379cae211f:-8000:0000000000005DCA begin
        $dir = new DirectoryIterator(ROOT_PATH.$this->id.'/actions');
	    foreach ($dir as $fileinfo) {
			if(preg_match('/^class\.[^.]*\.php$/', $fileinfo->getFilename())) {
				$module = substr($fileinfo->getFilename(), 6, -4);
				$class = $this->id.'_actions_'.$module;
				if (is_subclass_of($class, 'Module')) {
					$returnValue[$module] = $class;
				} else {
					common_Logger::w($fileinfo->getFilename().' does not inherit Module');
				}
			}
		}
		if (!is_null($this->parent)) {
			$returnValue = array_merge($this->parent->getAllModules(), $returnValue);
		}
        // section 127-0-1-1-176d7eef:1379cae211f:-8000:0000000000005DCA end

        return (array) $returnValue;
    }

    /**
     * Short description of method getModule
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string id
     * @return Module
     */
    public function getModule($id)
    {
        $returnValue = null;

        // section 127-0-1-1-176d7eef:1379cae211f:-8000:0000000000005DCC begin
    	$className = $this->id.'_actions_'.$id;
		if(class_exists($className)) {
			$returnValue = new $className;
		} elseif (!is_null($this->parent)) {
			$returnValue = $this->parent->getModule($id);
		}
        // section 127-0-1-1-176d7eef:1379cae211f:-8000:0000000000005DCC end

        return $returnValue;
    }

    /**
     * Short description of method getDependencies
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getDependencies()
    {
        $returnValue = array();

        // section 127-0-1-1-176d7eef:1379cae211f:-8000:0000000000005DCF begin
    	if(is_array($this->requiredExtensionsList)) {
        	$returnValue = $this->requiredExtensionsList;

        	foreach($this->requiredExtensionsList as $id){
        		$dependence = common_ext_ExtensionsManager::singleton()->getExtensionById($id);
        		$returnValue = array_merge($returnValue, $dependence->getDependencies());
        	}
        }
        // section 127-0-1-1-176d7eef:1379cae211f:-8000:0000000000005DCF end

        return (array) $returnValue;
    }

    /**
     * Short description of method isEnabled
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return boolean
     */
    public function isEnabled()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-176d7eef:1379cae211f:-8000:0000000000005DD2 begin
        // section 127-0-1-1-176d7eef:1379cae211f:-8000:0000000000005DD2 end

        return (bool) $returnValue;
    }

} /* end of class common_ext_Extension */

?>
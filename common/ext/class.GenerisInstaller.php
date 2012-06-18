<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/ext/class.GenerisInstaller.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 14.06.2012, 11:18:54 with ArgoUML PHP module 
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
 * include common_ext_ExtensionInstaller
 *
 * @author lionel.lecaque@tudor.lu
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */
require_once('common/ext/class.ExtensionInstaller.php');

/* user defined includes */
// section 127-0-1-1-2805dfc8:137ea47ddc3:-8000:0000000000001A3E-includes begin
// section 127-0-1-1-2805dfc8:137ea47ddc3:-8000:0000000000001A3E-includes end

/* user defined constants */
// section 127-0-1-1-2805dfc8:137ea47ddc3:-8000:0000000000001A3E-constants begin
// section 127-0-1-1-2805dfc8:137ea47ddc3:-8000:0000000000001A3E-constants end

/**
 * Short description of class common_ext_GenerisInstaller
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage ext
 */
class common_ext_GenerisInstaller
    extends common_ext_ExtensionInstaller
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method install
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function install()
    {
        // section 127-0-1-1-2805dfc8:137ea47ddc3:-8000:0000000000001A40 begin
    	if ($this->extension->getID() != 'generis') {
    		throw new common_ext_ExtensionException('Tried to install a non generis extension using the GenerisInstaller');
    	}
        //$this->installCustomScript();
		//$this->installWriteConfig();
		$this->installOntology();
		//$this->installLocalData();
		//$this->installWriteConfig();
		//$this->installModuleModel();
		//$this->installRegisterExt();
        // section 127-0-1-1-2805dfc8:137ea47ddc3:-8000:0000000000001A40 end
    }

} /* end of class common_ext_GenerisInstaller */

?>
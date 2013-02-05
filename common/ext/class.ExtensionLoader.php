<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common\ext\class.ExtensionLoader.php
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

    // --- OPERATIONS ---

    /**
     * load an extension
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function load()
    {
        // section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017AD begin
        common_Logger::t('Loading extension ' . $this->extension->getID());

		$classLoader = common_ext_ClassLoader::singleton();
		if(isset($this->extension->classLoaderPackages)) {
			foreach($this->extension->classLoaderPackages as $package) {
				$classLoader->addPackage($package);
			}
		}

		$classLoader->register();
        // section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017AD end
    }

} /* end of class common_ext_ExtensionLoader */

?>
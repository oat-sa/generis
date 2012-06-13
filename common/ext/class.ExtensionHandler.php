<?php

error_reporting(E_ALL);

/**
 * EXtension Wrapper
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
// section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017BD-includes begin
// section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017BD-includes end

/* user defined constants */
// section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017BD-constants begin
// section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017BD-constants end

/**
 * EXtension Wrapper
 *
 * @abstract
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 * @subpackage ext
 */
abstract class common_ext_ExtensionHandler
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute extension
     *
     * @access public
     * @var Extension
     */
    public $extension = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Extension extension
     * @return mixed
     */
    public function __construct( common_ext_Extension $extension)
    {
        // section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017C6 begin
		$this->extension = $extension;
        // section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017C6 end
    }

} /* end of abstract class common_ext_ExtensionHandler */

?>
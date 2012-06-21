<?php

error_reporting(E_ALL);

/**
 * This exception must be thrown when an error occurs while an extension
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage ext
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Any exception related to extensions should inherit this class.
 *
 * @author lionel.lecaque@tudor.lu
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */
require_once('common/ext/class.ExtensionException.php');

/* user defined includes */
// section -64--88-56-1-1a866557:1380f1ba529:-8000:0000000000001A5D-includes begin
// section -64--88-56-1-1a866557:1380f1ba529:-8000:0000000000001A5D-includes end

/* user defined constants */
// section -64--88-56-1-1a866557:1380f1ba529:-8000:0000000000001A5D-constants begin
// section -64--88-56-1-1a866557:1380f1ba529:-8000:0000000000001A5D-constants end

/**
 * This exception must be thrown when an error occurs while an extension
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage ext
 */
class common_ext_InstallationException
    extends common_ext_ExtensionException
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

} /* end of class common_ext_InstallationException */

?>
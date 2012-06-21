<?php

error_reporting(E_ALL);

/**
 * This exception is thrown when an extension is already installed.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage ext
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This exception must be thrown when an error occurs while an extension
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/ext/class.InstallationException.php');

/* user defined includes */
// section -64--88-56-1--40cf7f1a:1380f5aea49:-8000:0000000000001A75-includes begin
// section -64--88-56-1--40cf7f1a:1380f5aea49:-8000:0000000000001A75-includes end

/* user defined constants */
// section -64--88-56-1--40cf7f1a:1380f5aea49:-8000:0000000000001A75-constants begin
// section -64--88-56-1--40cf7f1a:1380f5aea49:-8000:0000000000001A75-constants end

/**
 * This exception is thrown when an extension is already installed.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage ext
 */
class common_ext_AlreadyInstalledException
    extends common_ext_InstallationException
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

} /* end of class common_ext_AlreadyInstalledException */

?>
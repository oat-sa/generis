<?php

error_reporting(E_ALL);

/**
 * This exception is thrown when a forbidden action is requested at installation
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
// section -64--88-56-1--40cf7f1a:1380f5aea49:-8000:0000000000001A79-includes begin
// section -64--88-56-1--40cf7f1a:1380f5aea49:-8000:0000000000001A79-includes end

/* user defined constants */
// section -64--88-56-1--40cf7f1a:1380f5aea49:-8000:0000000000001A79-constants begin
// section -64--88-56-1--40cf7f1a:1380f5aea49:-8000:0000000000001A79-constants end

/**
 * This exception is thrown when a forbidden action is requested at installation
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage ext
 */
class common_ext_ForbiddenActionException
    extends common_ext_InstallationException
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

} /* end of class common_ext_ForbiddenActionException */

?>
<?php

error_reporting(E_ALL);

/**
 * This exception must be thrown when an Extension Manifest is requested but not
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage ext
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * An exception that occurs in the context of Extension Manifests.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/ext/class.ManifestException.php');

/* user defined includes */
// section -64--88-56-1-1a866557:1380f1ba529:-8000:0000000000001A5B-includes begin
// section -64--88-56-1-1a866557:1380f1ba529:-8000:0000000000001A5B-includes end

/* user defined constants */
// section -64--88-56-1-1a866557:1380f1ba529:-8000:0000000000001A5B-constants begin
// section -64--88-56-1-1a866557:1380f1ba529:-8000:0000000000001A5B-constants end

/**
 * This exception must be thrown when an Extension Manifest is requested but not
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage ext
 */
class common_ext_ManifestNotFoundException
    extends common_ext_ManifestException
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

} /* end of class common_ext_ManifestNotFoundException */

?>
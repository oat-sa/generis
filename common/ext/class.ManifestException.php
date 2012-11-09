<?php

error_reporting(E_ALL);

/**
 * An exception that occurs in the context of Extension Manifests.
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
// section 10-13-1-85-739cd80a:13ae5546680:-8000:0000000000001C6C-includes begin
// section 10-13-1-85-739cd80a:13ae5546680:-8000:0000000000001C6C-includes end

/* user defined constants */
// section 10-13-1-85-739cd80a:13ae5546680:-8000:0000000000001C6C-constants begin
// section 10-13-1-85-739cd80a:13ae5546680:-8000:0000000000001C6C-constants end

/**
 * An exception that occurs in the context of Extension Manifests.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage ext
 */
class common_ext_ManifestException
    extends common_ext_ExtensionException
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

} /* end of class common_ext_ManifestException */

?>
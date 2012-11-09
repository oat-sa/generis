<?php

error_reporting(E_ALL);

/**
 * This exception must be thrown when a manifest is malformed e.g. missing
 * data, syntax, ...
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
// section 10-13-1-85-739cd80a:13ae5546680:-8000:0000000000001C70-includes begin
// section 10-13-1-85-739cd80a:13ae5546680:-8000:0000000000001C70-includes end

/* user defined constants */
// section 10-13-1-85-739cd80a:13ae5546680:-8000:0000000000001C70-constants begin
// section 10-13-1-85-739cd80a:13ae5546680:-8000:0000000000001C70-constants end

/**
 * This exception must be thrown when a manifest is malformed e.g. missing
 * data, syntax, ...
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage ext
 */
class common_ext_MalformedManifestException
    extends common_ext_ManifestException
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

} /* end of class common_ext_MalformedManifestException */

?>
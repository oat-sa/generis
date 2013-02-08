<?php

error_reporting(E_ALL);

/**
 * This exception depicts an error while accessing the FileSystem.
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package common
 * @subpackage exception
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_exception_Error
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 */
require_once('common/exception/class.Error.php');

/* user defined includes */
// section 10-30-1--82--5bc03e16:13cb918086b:-8000:0000000000001FAC-includes begin
// section 10-30-1--82--5bc03e16:13cb918086b:-8000:0000000000001FAC-includes end

/* user defined constants */
// section 10-30-1--82--5bc03e16:13cb918086b:-8000:0000000000001FAC-constants begin
// section 10-30-1--82--5bc03e16:13cb918086b:-8000:0000000000001FAC-constants end

/**
 * This exception depicts an error while accessing the FileSystem.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package common
 * @subpackage exception
 */
class common_exception_FileSystemError
    extends common_exception_Error
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

} /* end of class common_exception_FileSystemError */

?>
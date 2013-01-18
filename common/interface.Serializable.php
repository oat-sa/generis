<?php

error_reporting(E_ALL);

/**
 * Classes that implement this class claims their instances are serializable and
 * be identified by a unique serial string.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_cache_FileCache
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/cache/class.FileCache.php');

/* user defined includes */
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001ECA-includes begin
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001ECA-includes end

/* user defined constants */
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001ECA-constants begin
// section 10-13-1-85--38a3ebee:13c4cf6d12a:-8000:0000000000001ECA-constants end

/**
 * Classes that implement this class claims their instances are serializable and
 * be identified by a unique serial string.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 */
interface common_Serializable
{


    // --- OPERATIONS ---

    /**
     * Obtain a serial for the instance of the class that implements the
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getSerial();

} /* end of interface common_Serializable */

?>
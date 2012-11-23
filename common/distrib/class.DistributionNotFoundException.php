<?php

error_reporting(E_ALL);

/**
 * Such an Exception must be thrown if a distribution that was supposed to exist
 * not.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage distrib
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_Exception
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('common/class.Exception.php');

/* user defined includes */
// section 10-13-1-85--15eb259f:13b2dbd2961:-8000:0000000000001DC1-includes begin
// section 10-13-1-85--15eb259f:13b2dbd2961:-8000:0000000000001DC1-includes end

/* user defined constants */
// section 10-13-1-85--15eb259f:13b2dbd2961:-8000:0000000000001DC1-constants begin
// section 10-13-1-85--15eb259f:13b2dbd2961:-8000:0000000000001DC1-constants end

/**
 * Such an Exception must be thrown if a distribution that was supposed to exist
 * not.
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage distrib
 */
class common_distrib_DistributionNotFoundException
    extends common_Exception
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

} /* end of class common_distrib_DistributionNotFoundException */

?>
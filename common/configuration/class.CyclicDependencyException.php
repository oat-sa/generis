<?php

error_reporting(E_ALL);

/**
 * This exception must be throw when a cyclic dependency between components is
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage configuration
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
// section 10-13-1-85--478659bd:13afeb85455:-8000:0000000000001C8E-includes begin
// section 10-13-1-85--478659bd:13afeb85455:-8000:0000000000001C8E-includes end

/* user defined constants */
// section 10-13-1-85--478659bd:13afeb85455:-8000:0000000000001C8E-constants begin
// section 10-13-1-85--478659bd:13afeb85455:-8000:0000000000001C8E-constants end

/**
 * This exception must be throw when a cyclic dependency between components is
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage configuration
 */
class common_configuration_CyclicDependencyException
    extends common_Exception
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

} /* end of class common_configuration_CyclicDependencyException */

?>
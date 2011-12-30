<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/exception/class.InvalidArgumentType.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 29.12.2011, 12:05:08 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package common
 * @subpackage exception
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_Exception
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('common/class.Exception.php');

/* user defined includes */
// section 127-0-1-1--7d7a54ea:134896cda52:-8000:00000000000044FA-includes begin
// section 127-0-1-1--7d7a54ea:134896cda52:-8000:00000000000044FA-includes end

/* user defined constants */
// section 127-0-1-1--7d7a54ea:134896cda52:-8000:00000000000044FA-constants begin
// section 127-0-1-1--7d7a54ea:134896cda52:-8000:00000000000044FA-constants end

/**
 * Short description of class common_exception_InvalidArgumentType
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package common
 * @subpackage exception
 */
class common_exception_InvalidArgumentType
    extends common_Exception
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string class
     * @param  string function
     * @param  int position
     * @param  string expectedType
     * @param  object
     * @return mixed
     */
    public function __construct($class = null, $function = 0, $position = 0, $expectedType = '', $object = null)
    {
        // section 127-0-1-1--7d7a54ea:134896cda52:-8000:00000000000044FF begin
        
        $message = 'Argument '.$position.' passed to '.$class.'::'.$function.'() must be an '.$expectedType.', '.get_class($object).' given';
        parent::__construct($message);
        
        // section 127-0-1-1--7d7a54ea:134896cda52:-8000:00000000000044FF end
    }

} /* end of class common_exception_InvalidArgumentType */

?>
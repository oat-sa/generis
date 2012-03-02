<?php

error_reporting(E_ALL);

/**
 * This exception allow developers to generate expected
 * errors when clients try to acces to an ajax service
 * through an other way than the ajax mechanism
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
// section 127-0-1-1-641a0ff0:1359a3da29e:-8000:0000000000001B23-includes begin
// section 127-0-1-1-641a0ff0:1359a3da29e:-8000:0000000000001B23-includes end

/* user defined constants */
// section 127-0-1-1-641a0ff0:1359a3da29e:-8000:0000000000001B23-constants begin
// section 127-0-1-1-641a0ff0:1359a3da29e:-8000:0000000000001B23-constants end

/**
 * This exception allow developers to generate expected
 * errors when clients try to acces to an ajax service
 * through an other way than the ajax mechanism
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package common
 * @subpackage exception
 */
class common_exception_IsAjaxAction
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
     * @param  string service
     * @return mixed
     */
    public function __construct($service = "")
    {
        // section 127-0-1-1-641a0ff0:1359a3da29e:-8000:0000000000001B24 begin
        
        $message = 'The following service ('.$path.') is an Ajax service';
        parent::__construct($message);
        
        // section 127-0-1-1-641a0ff0:1359a3da29e:-8000:0000000000001B24 end
    }

} /* end of class common_exception_IsAjaxAction */

?>
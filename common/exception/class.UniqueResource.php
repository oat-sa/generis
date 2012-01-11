<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/exception/class.UniqueResource.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 11.01.2012, 09:53:34 with ArgoUML PHP module 
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
// section 127-0-1-1-602f558a:134cbda6031:-8000:00000000000018F9-includes begin
// section 127-0-1-1-602f558a:134cbda6031:-8000:00000000000018F9-includes end

/* user defined constants */
// section 127-0-1-1-602f558a:134cbda6031:-8000:00000000000018F9-constants begin
// section 127-0-1-1-602f558a:134cbda6031:-8000:00000000000018F9-constants end

/**
 * Short description of class common_exception_UniqueResource
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package common
 * @subpackage exception
 */
class common_exception_UniqueResource
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
     * @param  Resource resource
     * @param  string message
     * @return mixed
     */
    public function __construct( core_kernel_classes_Resource $resource, $message = "")
    {
        // section 127-0-1-1-602f558a:134cbda6031:-8000:00000000000018FA begin
        
        if(empty($message)){
            $message = 'The resource ('.$resource->uriResource.') should be unique';
        }
        parent::__construct($message);
        
        // section 127-0-1-1-602f558a:134cbda6031:-8000:00000000000018FA end
    }

} /* end of class common_exception_UniqueResource */

?>
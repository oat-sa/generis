<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/exception/class.UniqueFileResource.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 11.01.2012, 09:57:39 with ArgoUML PHP module 
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
 * include common_exception_UniqueResource
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('common/exception/class.UniqueResource.php');

/* user defined includes */
// section 127-0-1-1-602f558a:134cbda6031:-8000:00000000000018FF-includes begin
// section 127-0-1-1-602f558a:134cbda6031:-8000:00000000000018FF-includes end

/* user defined constants */
// section 127-0-1-1-602f558a:134cbda6031:-8000:00000000000018FF-constants begin
// section 127-0-1-1-602f558a:134cbda6031:-8000:00000000000018FF-constants end

/**
 * Short description of class common_exception_UniqueFileResource
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package common
 * @subpackage exception
 */
class common_exception_UniqueFileResource
    extends common_exception_UniqueResource
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @return mixed
     */
    public function __construct( core_kernel_classes_File $resource)
    {
        // section 127-0-1-1-602f558a:134cbda6031:-8000:0000000000001900 begin

        $path = $resource->getAbsolutePath();
        $message = 'A resource file ('.$resource->uriResource.') has already been defined for the path ('.$path.')';
        parent::__construct($resource, $message);
        
        // section 127-0-1-1-602f558a:134cbda6031:-8000:0000000000001900 end
    }

} /* end of class common_exception_UniqueFileResource */

?>
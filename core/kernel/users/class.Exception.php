<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\users\class.Exception.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 12.05.2010, 20:21:00 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_users
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001846-includes begin
// section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001846-includes end

/* user defined constants */
// section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001846-constants begin
// section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001846-constants end

/**
 * Short description of class core_kernel_users_Exception
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_users
 */
class core_kernel_users_Exception extends common_Exception
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute BAD_PASSWORD
     *
     * @access public
     * @var int
     */
    const BAD_PASSWORD = 0;

    /**
     * Short description of attribute BAD_LOGIN
     *
     * @access public
     * @var int
     */
    const BAD_LOGIN = 1;
    
    /**
     * Short description of attribute BAD_ROLE
     *
     * @access public
     * @var int
     */
    const BAD_ROLE = 2;
    
    /**
     * Short description of attribute LOGIN_EXITS
     *
     * @access public
     * @var int
     */
	const LOGIN_EXITS = 3;


} /* end of class core_kernel_users_Exception */

?>
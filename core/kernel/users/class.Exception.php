<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/users/class.Exception.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 18.02.2013, 16:43:05 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package core
 * @subpackage kernel_users
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_Exception
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 */
require_once('common/class.Exception.php');

/* user defined includes */
// section 127-0-1-1--55ee3a0d:13cedda118c:-8000:0000000000001FBC-includes begin
// section 127-0-1-1--55ee3a0d:13cedda118c:-8000:0000000000001FBC-includes end

/* user defined constants */
// section 127-0-1-1--55ee3a0d:13cedda118c:-8000:0000000000001FBC-constants begin
// section 127-0-1-1--55ee3a0d:13cedda118c:-8000:0000000000001FBC-constants end

/**
 * Short description of class core_kernel_users_Exception
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package core
 * @subpackage kernel_users
 */
class core_kernel_users_Exception
    extends common_Exception
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

    // --- OPERATIONS ---

} /* end of class core_kernel_users_Exception */

?>
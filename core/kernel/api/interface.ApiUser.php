<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\api\interface.ApiUser.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 23.03.2010, 15:58:21 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_api
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_api_Api
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('core/kernel/api/interface.Api.php');

/* user defined includes */
// section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009BD-includes begin
// section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009BD-includes end

/* user defined constants */
// section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009BD-constants begin
// section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009BD-constants end

/**
 * Short description of class core_kernel_api_ApiUser
 *
 * @abstract
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_api
 */
interface core_kernel_api_ApiUser
    extends core_kernel_api_Api
{


    // --- OPERATIONS ---

    /**
     * Short description of method addUser
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  User user
     * @return boolean
     */
    public function addUser( core_kernel_users_User $user);

    /**
     * Short description of method isAdmin
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return boolean
     */
    public function isAdmin();

    /**
     * Short description of method addGroup
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Group group
     * @return void
     */
    public function addGroup( core_kernel_users_Group $group);

} /* end of interface core_kernel_api_ApiUser */

?>
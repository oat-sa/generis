<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\impl\class.ApiUserI.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 29.03.2010, 14:19:52 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_impl
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_api_ApiUser
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('core/kernel/api/interface.ApiUser.php');

/**
 * session has been set public because when implementing an interface, the son
 * this class may not read this attribute otherwise in php 5.2
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('core/kernel/impl/class.ApiI.php');

/* user defined includes */
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000075A-includes begin
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000075A-includes end

/* user defined constants */
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000075A-constants begin
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000075A-constants end

/**
 * Short description of class core_kernel_impl_ApiUserI
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_impl
 */
class core_kernel_impl_ApiUserI
    extends core_kernel_impl_ApiI
        implements core_kernel_api_ApiUser
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method addUser
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  User user
     * @return boolean
     */
    public function addUser( core_kernel_users_User $user)
    {
        $returnValue = (bool) false;

        // section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009BF begin
        // section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009BF end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isAdmin
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return boolean
     */
    public function isAdmin()
    {
        $returnValue = (bool) false;

        // section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009C1 begin
        // section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009C1 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method addGroup
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Group group
     * @return void
     */
    public function addGroup( core_kernel_users_Group $group)
    {
        // section 10-13-1--31-4ffc4ea9:119c263b344:-8000:0000000000000F0E begin
        // section 10-13-1--31-4ffc4ea9:119c263b344:-8000:0000000000000F0E end
    }

} /* end of class core_kernel_impl_ApiUserI */

?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/users/interface.RolesManagement.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 18.02.2013, 16:33:58 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package core
 * @subpackage kernel_users
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FFD-includes begin
// section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FFD-includes end

/* user defined constants */
// section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FFD-constants begin
// section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FFD-constants end

/**
 * Short description of class core_kernel_users_RolesManagement
 *
 * @access public
 * @author Jerome Bogaerts, <jerome@taotesting.com>
 * @package core
 * @subpackage kernel_users
 */
interface core_kernel_users_RolesManagement
{


    // --- OPERATIONS ---

    /**
     * Add a role in Generis.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  string label The label to apply to the newly created Generis Role.
     * @param  includedRoles The Role(s) to be included in the newly created Generis Role.
Can be either a Resource or an array of Resources.
     * @return core_kernel_classes_Resource
     */
    public function addRole($label, $includedRoles = null);

    /**
     * Remove a Generis role from the persistent memory. User References to this
     * will be removed.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource role The Role to remove.
     * @return boolean
     */
    public function removeRole( core_kernel_classes_Resource $role);

    /**
     * Get an array of the Roles included by a Generis Role.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource role A Generis Role.
     * @return array
     */
    public function getIncludedRoles( core_kernel_classes_Resource $role);

    /**
     * Returns an array of Roles (as Resources) where keys are their URIs. The
     * roles represent which kind of Roles are accepted to be identified against
     * system.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return array
     */
    public function getAllowedRoles();

    /**
     * Returns a Role (as a Resource) which represents the default role of the
     * If a user has to be created but no Role is given to him, it will receive
     * role.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return core_kernel_classes_Resource
     */
    public function getDefaultRole();

    /**
     * Make a Role include another Role.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param  Resource role The role that needs to include another role.
     * @param  Resource roleToInclude The role to be included.
     * @return void
     */
    public function includeRole( core_kernel_classes_Resource $role,  core_kernel_classes_Resource $roleToInclude);

} /* end of interface core_kernel_users_RolesManagement */

?>
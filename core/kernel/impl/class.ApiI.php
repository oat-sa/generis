<?php

error_reporting(E_ALL);

/**
 * session has been set public because when implementing an interface, the son
 * this class may not read this attribute otherwise in php 5.2
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_impl
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
// section 10-13-1--31--64270bf:11918ad765e:-8000:000000000000098C-includes begin
// section 10-13-1--31--64270bf:11918ad765e:-8000:000000000000098C-includes end

/* user defined constants */
// section 10-13-1--31--64270bf:11918ad765e:-8000:000000000000098C-constants begin
// section 10-13-1--31--64270bf:11918ad765e:-8000:000000000000098C-constants end

/**
 * session has been set public because when implementing an interface, the son
 * this class may not read this attribute otherwise in php 5.2
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_impl
 */
class core_kernel_impl_ApiI
        implements core_kernel_api_Api
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute session
     *
     * @access public
     * @var Session
     */
    public $session = null;

    // --- OPERATIONS ---

    /**
     * Short description of method logIn
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string login
     * @param  string password
     * @param  string module
     * @param  boolean role
     * @return boolean
     */
    public function logIn($login, $password, $module, $role)
    {
        $returnValue = (bool) false;

        // section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009AF begin
        if($role === true) {
        	$role = CLASS_ROLE_TAOMANAGER;
        }
       
        core_kernel_users_Service::singleton()->login($login, $password, $role);
		$this->session = core_kernel_classes_Session::singleton();
        // section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009AF end

        return (bool) $returnValue;
    }

    /**
     * Short description of method logOut
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return boolean
     */
    public function logOut()
    {
        $returnValue = (bool) false;

        // section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009B5 begin
        core_kernel_users_Service::singleton()->logout();
        // section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009B5 end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_impl_ApiI */

?>
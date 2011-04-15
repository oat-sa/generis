<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\api\interface.ApiSubscription.php
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
// section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009B7-includes begin
// section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009B7-includes end

/* user defined constants */
// section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009B7-constants begin
// section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009B7-constants end

/**
 * Short description of class core_kernel_api_ApiSubscription
 *
 * @abstract
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_api
 */
interface core_kernel_api_ApiSubscription
    extends core_kernel_api_Api
{


    // --- OPERATIONS ---

    /**
     * Short description of method getSubscribees
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return core_kernel_classes_ContainerCollection
     */
    public function getSubscribees();

    /**
     * Short description of method addSubscribee
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Subscription subscribee
     * @return void
     */
    public function addSubscribee( core_kernel_users_Subscription $subscribee);

    /**
     * Short description of method addSubscriber
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Subscription subscriber
     * @return void
     */
    public function addSubscriber( core_kernel_users_Subscription $subscriber);

} /* end of interface core_kernel_api_ApiSubscription */

?>
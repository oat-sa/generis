<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\impl\class.ApiSubscriptionI.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 29.03.2010, 14:19:52 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author patrick.plichart@tudor.lu
 * @package core
 * @subpackage kernel_impl
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_api_ApiSubscription
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('core/kernel/api/interface.ApiSubscription.php');

/**
 * session has been set public because when implementing an interface, the son
 * this class may not read this attribute otherwise in php 5.2
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('core/kernel/impl/class.ApiI.php');

/* user defined includes */
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000075C-includes begin
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000075C-includes end

/* user defined constants */
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000075C-constants begin
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000075C-constants end

/**
 * Short description of class core_kernel_impl_ApiSubscriptionI
 *
 * @access public
 * @author patrick.plichart@tudor.lu
 * @package core
 * @subpackage kernel_impl
 */
class core_kernel_impl_ApiSubscriptionI
    extends core_kernel_impl_ApiI
        implements core_kernel_api_ApiSubscription
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getSubscribees
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return core_kernel_classes_ContainerCollection
     */
    public function getSubscribees()
    {
        $returnValue = null;

        // section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009B9 begin
        // section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009B9 end

        return $returnValue;
    }

    /**
     * Short description of method addSubscribee
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Subscription subscribee
     * @return void
     */
    public function addSubscribee( core_kernel_users_Subscription $subscribee)
    {
        // section 10-13-1--31-4ffc4ea9:119c263b344:-8000:0000000000000EE2 begin
        // section 10-13-1--31-4ffc4ea9:119c263b344:-8000:0000000000000EE2 end
    }

    /**
     * Short description of method addSubscriber
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Subscription subscriber
     * @return void
     */
    public function addSubscriber( core_kernel_users_Subscription $subscriber)
    {
        // section 10-13-1--31--5a69f10:119c34ee42b:-8000:0000000000000B27 begin
        // section 10-13-1--31--5a69f10:119c34ee42b:-8000:0000000000000B27 end
    }

} /* end of class core_kernel_impl_ApiSubscriptionI */

?>
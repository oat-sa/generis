<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\api\interface.ApiSearch.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 29.03.2010, 14:21:57 with ArgoUML PHP module 
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
// section 10-13-1--99--4fdec042:11a2a3b44dc:-8000:00000000000010AB-includes begin
// section 10-13-1--99--4fdec042:11a2a3b44dc:-8000:00000000000010AB-includes end

/* user defined constants */
// section 10-13-1--99--4fdec042:11a2a3b44dc:-8000:00000000000010AB-constants begin
// section 10-13-1--99--4fdec042:11a2a3b44dc:-8000:00000000000010AB-constants end

/**
 * Short description of class core_kernel_api_ApiSearch
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_api
 */
interface core_kernel_api_ApiSearch
    extends core_kernel_api_Api
{


    // --- OPERATIONS ---

    /**
     * returns all resources as a collection containing for some properties,
     * that contains somehow (subStringOf with wildcards) all of the provided
     * keywords must be an array of strings
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array keyword
     * @return core_kernel_classes_ContainerCollection
     */
    public function fullTextSearch($keyword);

    /**
     * performs a search in the knowledge base for resources having for all
     * the corresponding values
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array criteria
     * @return core_kernel_classes_ContainerCollection
     */
    public function search($criteria);

    /**
     * Short description of method searchInstances
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array propertyFilters
     * @param  Class topClazz
     * @param  array options
     * @return array
     */
    public function searchInstances($propertyFilters = array(),  core_kernel_classes_Class $topClazz = null, $options = array());

} /* end of interface core_kernel_api_ApiSearch */

?>
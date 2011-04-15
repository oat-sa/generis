<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/api/interface.ApiModel.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 15.04.2011, 16:46:04 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package core
 * @subpackage kernel_api
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_api_Api
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('core/kernel/api/interface.Api.php');

/* user defined includes */
// section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009C7-includes begin
// section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009C7-includes end

/* user defined constants */
// section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009C7-constants begin
// section 10-13-1--31--1e8cf08b:11927b92513:-8000:00000000000009C7-constants end

/**
 * Short description of class core_kernel_api_ApiModel
 *
 * @abstract
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package core
 * @subpackage kernel_api
 */
interface core_kernel_api_ApiModel
    extends core_kernel_api_Api
{


    // --- OPERATIONS ---

    /**
     * this suport standard sparql query (string) and return the corresponding
     * of resources as collection. This makes use of pOWl third party library
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string sparqlQuery
     * @return core_kernel_classes_ContainerCollection
     * @see sparql proposed by the w3c consortium
     */
    public function sparqlQuery($sparqlQuery);

    /**
     * This supports rdql queries formulated as strings , this make use of the
     * party library pOwl
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string rdqlQuery
     * @return boolean
     */
    public function rdqlQuery($rdqlQuery);

    /**
     * build xml rdf containing rdf:Description of all meta-data the conected
     * may get
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array sourceNamespaces
     * @return string
     */
    public function exportXmlRdf($sourceNamespaces = array());

    /**
     * import xml rdf files into the knowledge base
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string targetNameSpace
     * @param  string fileLocation
     * @return boolean
     */
    public function importXmlRdf($targetNameSpace, $fileLocation);

    /**
     * connect on the remote module whose id is provided, retrive the knowledge
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  int idSubscription
     * @return boolean
     */
    public function connectOnRemoteModule($idSubscription);

    /**
     * return resource object for the provided uriResource, if the uri does not
     * in the knowledge base, returns false
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  string uriResource
     * @return core_kernel_classes_Resource
     */
    public function getResourceDescription($uriResource);

    /**
     * returns an xml rdf serialization for uriResource with all meta dat found
     * inferenced from te knowlege base about this resource
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string uriResource
     * @return string
     */
    public function getResourceDescriptionXML($uriResource);

    /**
     * returns metaclasses tat are not subclasses of other metaclasses
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @return core_kernel_classes_ContainerCollection
     */
    public function getMetaClasses();

    /**
     * returns classes that are not subclasses of other classes
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @return core_kernel_classes_ContainerCollection
     */
    public function getRootClasses();

    /**
     * Short description of method getAllClasses
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return core_kernel_classes_ContainerCollection
     */
    public function getAllClasses();

    /**
     * add a new statment to the knowledge base
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  string subject
     * @param  string predicate
     * @param  string object
     * @param  string language
     * @return boolean
     */
    public function setStatement($subject, $predicate, $object, $language);

    /**
     * Short description of method removeStatement
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string subject
     * @param  string predicate
     * @param  string object
     * @param  string language
     * @return boolean
     */
    public function removeStatement($subject, $predicate, $object, $language);

    /**
     * Short description of method getSubject
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string predicate
     * @param  string object
     * @return core_kernel_classes_Resource
     */
    public function getSubject($predicate, $object);

    /**
     * Short description of method getObject
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string subject
     * @param  string predicate
     * @return core_kernel_classes_ContainerCollection
     */
    public function getObject($subject, $predicate);

    /**
     * Short description of method getResourceTree
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @deprecated
     * @param  string uriResource
     * @param  int depth
     * @return mixed
     */
    public function getResourceTree($uriResource, $depth);

} /* end of interface core_kernel_api_ApiModel */

?>
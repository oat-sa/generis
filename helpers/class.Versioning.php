<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - helpers/class.Versioning.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 05.11.2012, 14:09:43 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package helpers
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000004F5E-includes begin
// section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000004F5E-includes end

/* user defined constants */
// section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000004F5E-constants begin
// section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000004F5E-constants end

/**
 * Short description of class helpers_Versioning
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package helpers
 */
class helpers_Versioning
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method isEnabled
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return boolean
     */
    public static function isEnabled()
    {
        $returnValue = (bool) false;

        // section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000004F5F begin
        $returnValue = count(self::getAvailableRepositories()) > 0;
        // section 10-30-1--78-1b01f2ef:13ac03fd34f:-8000:0000000000004F5F end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getAvailableRepositories
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public static function getAvailableRepositories()
    {
        $returnValue = array();

        // section 10-30-1--78--774a33b7:13ad0ae6f5f:-8000:0000000000001BB9 begin
        $classRepository = new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDREPOSITORY);
        $returnValue = $classRepository->searchInstances(array(
        	PROPERTY_GENERIS_VERSIONEDREPOSITORY_ENABLED => GENERIS_TRUE
        ), array(
        	'like' => false
        ));
        // section 10-30-1--78--774a33b7:13ad0ae6f5f:-8000:0000000000001BB9 end

        return (array) $returnValue;
    }

} /* end of class helpers_Versioning */

?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - helpers/class.FileSource.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 04.01.2013, 15:34:43 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package helpers
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-30-1--78-400cd9:13c05dfbbe2:-8000:00000000000052D7-includes begin
// section 10-30-1--78-400cd9:13c05dfbbe2:-8000:00000000000052D7-includes end

/* user defined constants */
// section 10-30-1--78-400cd9:13c05dfbbe2:-8000:00000000000052D7-constants begin
// section 10-30-1--78-400cd9:13c05dfbbe2:-8000:00000000000052D7-constants end

/**
 * Short description of class helpers_FileSource
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package helpers
 */
class helpers_FileSource
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * returns a list of active FileSources
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public static function getFileSources()
    {
        $returnValue = array();

        // section 10-30-1--78-400cd9:13c05dfbbe2:-8000:00000000000052DC begin
		$classRepository = new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDREPOSITORY);
		$returnValue = $classRepository->searchInstances(array(
			PROPERTY_GENERIS_VERSIONEDREPOSITORY_ENABLED => GENERIS_TRUE
		), array(
			'like' => false
		));
        // section 10-30-1--78-400cd9:13c05dfbbe2:-8000:00000000000052DC end

        return (array) $returnValue;
    }

} /* end of class helpers_FileSource */

?>
<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
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
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - helpers/class.Time.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 06.02.2012, 11:17:50 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package helpers
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--51e2e300:1355214e1e7:-8000:00000000000047C7-includes begin
// section 127-0-1-1--51e2e300:1355214e1e7:-8000:00000000000047C7-includes end

/* user defined constants */
// section 127-0-1-1--51e2e300:1355214e1e7:-8000:00000000000047C7-constants begin
// section 127-0-1-1--51e2e300:1355214e1e7:-8000:00000000000047C7-constants end

/**
 * Short description of class helpers_Time
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package helpers
 */
class helpers_Time
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getMicroTime
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return helpers_double
     */
    public static function getMicroTime()
    {
        $returnValue = (float) 0.0;

        // section 127-0-1-1--51e2e300:1355214e1e7:-8000:00000000000047C8 begin
        
        list($ms, $s) = explode(" ", microtime());
        $returnValue = $s+$ms;
        
        // section 127-0-1-1--51e2e300:1355214e1e7:-8000:00000000000047C8 end

        return (float) $returnValue;
    }

} /* end of class helpers_Time */

?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/exception/class.Error.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 30.01.2012, 16:44:05 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage exception
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_Exception
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('common/class.Exception.php');

/**
 * include common_log_SeverityLevel
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('common/log/interface.SeverityLevel.php');

/* user defined includes */
// section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001905-includes begin
// section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001905-includes end

/* user defined constants */
// section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001905-constants begin
// section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001905-constants end

/**
 * Short description of class common_exception_Error
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage exception
 */
class common_exception_Error
    extends common_Exception
        implements common_log_SeverityLevel
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method getSeverity
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return int
     */
    public function getSeverity()
    {
        $returnValue = (int) 0;

        // section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001907 begin
        $returnValue = common_Logger::ERROR_LEVEL;
        // section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001907 end

        return (int) $returnValue;
    }

} /* end of class common_exception_Error */

?>
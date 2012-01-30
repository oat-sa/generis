<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/log/interface.SeverityLevel.php
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
 * @subpackage log
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001906-includes begin
// section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001906-includes end

/* user defined constants */
// section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001906-constants begin
// section 127-0-1-1--15445bbd:1352f3a7eb2:-8000:0000000000001906-constants end

/**
 * Short description of class common_log_SeverityLevel
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage log
 */
interface common_log_SeverityLevel
{


    // --- OPERATIONS ---

    /**
     * Short description of method getSeverity
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return int
     */
    public function getSeverity();

} /* end of interface common_log_SeverityLevel */

?>
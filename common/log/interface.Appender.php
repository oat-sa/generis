<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/log/interface.Appender.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 19.06.2012, 10:30:40 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage log
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_log_Dispatcher
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('common/log/class.Dispatcher.php');

/* user defined includes */
// section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000435C-includes begin
// section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000435C-includes end

/* user defined constants */
// section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000435C-constants begin
// section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000435C-constants end

/**
 * Short description of class common_log_Appender
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage log
 */
interface common_log_Appender
{


    // --- OPERATIONS ---

    /**
     * decides whenever the Item should be logged by doLog
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @return mixed
     * @see doLog
     */
    public function log( common_log_Item $item);

    /**
     * Short description of method getLogThreshold
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return int
     */
    public function getLogThreshold();

    /**
     * Short description of method init
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array configuration
     * @return boolean
     */
    public function init($configuration);

} /* end of interface common_log_Appender */

?>
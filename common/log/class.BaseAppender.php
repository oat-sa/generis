<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/log/class.BaseAppender.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 08.12.2011, 14:00:00 with ArgoUML PHP module 
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
 * include common_log_Appender
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('common/log/interface.Appender.php');

/* user defined includes */
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:000000000000185C-includes begin
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:000000000000185C-includes end

/* user defined constants */
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:000000000000185C-constants begin
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:000000000000185C-constants end

/**
 * Short description of class common_log_BaseAppender
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage log
 */
abstract class common_log_BaseAppender
        implements common_log_Appender
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute threshold
     *
     * @access private
     * @var Integer
     */
    private $threshold = null;

    // --- OPERATIONS ---

    /**
     * Short description of method log
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @return mixed
     */
    public function log( common_log_Item $item)
    {
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000435D begin
        if ($item->getSeverity() >= $this->threshold) {
        	$this->doLog($item);
    	}
        // section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000435D end
    }

    /**
     * Short description of method getLogThreshold
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return int
     */
    public function getLogThreshold()
    {
        $returnValue = (int) 0;

        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017C6 begin
        $returnValue = $this->threshold;
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017C6 end

        return (int) $returnValue;
    }

    /**
     * Short description of method init
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array configuration
     * @return mixed
     */
    public function init($configuration)
    {
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:000000000000183B begin
    	if (isset($configuration['threshold']) && is_numeric($configuration['threshold']))
    		$this->threshold = intval($configuration['threshold']);
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:000000000000183B end
    }

    /**
     * Short description of method doLog
     *
     * @abstract
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Item item
     * @return mixed
     */
    public abstract function doLog( common_log_Item $item);

} /* end of abstract class common_log_BaseAppender */

?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/log/class.Dispatcher.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 07.12.2011, 15:51:22 with ArgoUML PHP module 
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
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001808-includes begin
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001808-includes end

/* user defined constants */
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001808-constants begin
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001808-constants end

/**
 * Short description of class common_log_Dispatcher
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage log
 */
class common_log_Dispatcher
        implements common_log_Appender
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute minLevel
     *
     * @access private
     * @var int
     */
    private $minLevel = null;

    /**
     * Short description of attribute appenders
     *
     * @access private
     * @var array
     */
    private $appenders = array();

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var Dispatcher
     */
    private static $instance = null;

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
        foreach ($this->appenders as $appender)
        	$appender->log($item);
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
        $returnValue = $this->minLevel;
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
    	$this->appenders = array();
    	$this->minLevel = null;
    	
    	foreach ($GLOBALS['config_log'] as $appenderConfig) {
    		$classname = null;
    		if (isset($appenderConfig['class'])) {
    			$classname = $appenderConfig['class'];
    			unset($appenderConfig['class']);
    		} elseif (isset($appenderConfig['nom'])) {
    			// backward compatibility
    			$classname = $appenderConfig['nom'];
    			unset($appenderConfig['nom']);
    			if ($classname == 'FileAppender') {
    				$classname = 'common_log_SingleFileAppender';
    			}
    		}
    	
    		if (!is_null($classname) && is_subclass_of($classname, 'common_log_Appender')) {
    			$appender = new $classname();
    			if (!is_null($appender)) {
    				$appender->init($appenderConfig);
    				$this->addAppender($appender);
    			}
    		}
    	}
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:000000000000183B end
    }

    /**
     * Short description of method addAppender
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Appender appender
     * @return mixed
     */
    public function addAppender( common_log_Appender $appender)
    {
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001820 begin
        $this->appenders[] = $appender;
        if (is_null($this->minLevel) || $this->minLevel > $appender->getLogThreshold())
        	$this->minLevel = $appender->getLogThreshold();
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001820 end
    }

    /**
     * Short description of method __construct
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    private function __construct()
    {
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001829 begin
        $this->init($GLOBALS['config_log']);
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001829 end
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return common_log_Dispatcher
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:000000000000182B begin
        if (is_null(self::$instance))
        	self::$instance = new common_log_Dispatcher();
        $returnValue = self::$instance;
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:000000000000182B end

        return $returnValue;
    }

} /* end of class common_log_Dispatcher */

?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/log/class.SingleFileAppender.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 07.12.2011, 17:57:27 with ArgoUML PHP module 
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
 * include common_log_BaseAppender
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('common/log/class.BaseAppender.php');

/* user defined includes */
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001843-includes begin
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001843-includes end

/* user defined constants */
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001843-constants begin
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001843-constants end

/**
 * Short description of class common_log_SingleFileAppender
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage log
 */
class common_log_SingleFileAppender
    extends common_log_BaseAppender
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute filename
     *
     * @access private
     * @var string
     */
    private $filename = '';

    /**
     * Short description of attribute filehandle
     *
     * @access private
     * @var resource
     */
    private $filehandle = null;

    /**
     * %d datestring
     * %m description(message)
     * %s severity
     * %b backtrace
     * %r request
     * %f file from which the log was called
     * %l line from which the log was called
     * %t timestamp
     *
     * @access public
     * @var string
     */
    public $format = '%d \'%m\' %s %f %l';

    // --- OPERATIONS ---

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
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001855 begin
    	if (isset($configuration['file'])) {
    		$this->filename = $configuration['file'];
    		unset($configuration['file']);
    	} elseif (isset($configuration['config'])) {
    		// backward compatibility
    		$this->filename = $configuration['config'];
    		unset($configuration['config']);
    	}
    	
    	if (isset($configuration['format'])) {
    		$this->format = $configuration['format'];
    		unset($configuration['format']);
    	}
    	parent::init($configuration);
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001855 end
    }

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
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001852 begin
    	if (is_null($this->filehandle)) {
    		$this->filehandle = @fopen($this->filename, 'a');
    	}
    	
    	$map = array(
    			'%d' => date('Y-m-d H:i:s',$item->getDateTime()),
    			'%m' => $item->getDescription(),
    			'%s' => $item->getSeverityDescriptionString(),
    			'%t' => $item->getDateTime(),
    			'%r' => $item->getRequest(),
    			'%f' => $item->getCallerFile(),
    			'%l' => $item->getCallerLine(),
    	);
    	
    	if (strpos($this->format, '%b')) {
    		$map['%b'] = 'Backtrace not yet supported';
    	}
    	
    	$str = strtr($this->format, $map)."\n";
    	
    	@fwrite($this->filehandle, $str);
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001852 end
    }

    /**
     * Short description of method __destruct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function __destruct()
    {
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001850 begin
    	if (!is_null($this->filehandle)) {
    		@fclose($this->filehandle);
    	}
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001850 end
    }

} /* end of class common_log_SingleFileAppender */

?>
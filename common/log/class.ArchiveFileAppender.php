<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/log/class.ArchiveFileAppender.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 09.12.2011, 11:59:31 with ArgoUML PHP module 
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
 * include common_log_SingleFileAppender
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('common/log/class.SingleFileAppender.php');

/* user defined includes */
// section 127-0-1-1-7b882644:1342260c2b6:-8000:0000000000001861-includes begin
// section 127-0-1-1-7b882644:1342260c2b6:-8000:0000000000001861-includes end

/* user defined constants */
// section 127-0-1-1-7b882644:1342260c2b6:-8000:0000000000001861-constants begin
// section 127-0-1-1-7b882644:1342260c2b6:-8000:0000000000001861-constants end

/**
 * Short description of class common_log_ArchiveFileAppender
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage log
 */
class common_log_ArchiveFileAppender
    extends common_log_SingleFileAppender
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute directory
     *
     * @access public
     * @var string
     */
    public $directory = '';

    // --- OPERATIONS ---

    /**
     * Short description of method init
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array configuration
     * @return boolean
     */
    public function init($configuration)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7b882644:1342260c2b6:-8000:0000000000001866 begin
        if (isset($configuration['directory']) && $configuration['directory']) {
        	$this->directory = $configuration['directory'];
        } elseif (isset($configuration['file'])) {
        	$this->directory = dirname($configuration['file']);
        }
        
        if (!empty($this->directory))
        	$returnValue = parent::init($configuration);
        else
        	$returnValue = false;
        // section 127-0-1-1-7b882644:1342260c2b6:-8000:0000000000001866 end
        
        return (bool) $returnValue;
        // section 127-0-1-1-7b882644:1342260c2b6:-8000:0000000000001866 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method initFile
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initFile()
    {
        // section 127-0-1-1-7b882644:1342260c2b6:-8000:0000000000001868 begin
    	if ($this->maxFileSize > 0 && file_exists($this->filename) && filesize($this->filename) >= $this->maxFileSize) {
	    	$filebase = basename($this->filename);
	    	$dotpos = strrpos($filebase, ".");
	    	$prefix = $this->directory.DIRECTORY_SEPARATOR.substr($filebase, 0, $dotpos)."_".date('Y-m-d');
	    	$sufix = substr($filebase, $dotpos);
	    	$count_string = "";
	    	$count = 0;
	    	while (file_exists($prefix.$count_string.$sufix)) {
	    		$count_string = "_".++$count;
	    	}
	    	rename($this->filename, $prefix.$count_string.$sufix);
    	}
    	$this->filehandle = @fopen($this->filename, 'a');
        // section 127-0-1-1-7b882644:1342260c2b6:-8000:0000000000001868 end
    }

} /* end of class common_log_ArchiveFileAppender */

?>
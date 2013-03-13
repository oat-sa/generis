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
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);\n *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/log/class.Item.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 09.12.2011, 14:07:22 with ArgoUML PHP module 
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
// section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000435B-includes begin
// section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000435B-includes end

/* user defined constants */
// section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000435B-constants begin
// section 127-0-1-1--5509896f:133feddcac3:-8000:000000000000435B-constants end

/**
 * Short description of class common_log_Item
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage log
 */
class common_log_Item
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute datetime
     *
     * @access private
     * @var int
     */
    private $datetime = 0;

    /**
     * Short description of attribute description
     *
     * @access private
     * @var string
     */
    private $description = '';

    /**
     * Short description of attribute severity
     *
     * @access private
     * @var int
     */
    private $severity = 0;

    /**
     * Short description of attribute backtrace
     *
     * @access private
     * @var array
     */
    private $backtrace = array();

    /**
     * Short description of attribute request
     *
     * @access private
     * @var string
     */
    private $request = '';

    /**
     * Short description of attribute tags
     *
     * @access private
     * @var array
     */
    private $tags = array();

    /**
     * Short description of attribute authentifiedUser
     *
     * @access private
     * @var string
     */
    private $authentifiedUser = '';

    /**
     * Short description of attribute errorFile
     *
     * @access private
     * @var string
     */
    private $errorFile = '';

    /**
     * Short description of attribute errorLine
     *
     * @access private
     * @var int
     */
    private $errorLine = 0;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string description
     * @param  int severity
     * @param  int datetime
     * @param  string user
     * @param  array backtrace
     * @param  array tags
     * @param  string request
     * @param  string errorFile
     * @param  int errorLine
     * @return mixed
     */
    public function __construct($description, $severity, $datetime, $user = null, $backtrace = array(), $tags = array(), $request = "", $errorFile = '', $errorLine = 0)
    {
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017DA begin
        $this->description		= $description;
        $this->severity			= $severity;
        $this->datetime			= $datetime;
        $this->tags				= is_array($tags) ? $tags : array($tags);
        $this->request			= $request;
        $this->authentifiedUser	= $user;
        $this->errorFile		= $errorFile;
        $this->errorLine		= $errorLine;
        
        // limit backtrace
        if (count($backtrace) > 50) {
        	$backtrace = array_slice($backtrace, -50);
        }
        
        $cleanbacktrace = array();
        foreach ($backtrace as $key => $row) {
        	
        	if (isset($backtrace[$key]['object'])) {
        		unset($backtrace[$key]['object']);
        	}

        	// WARNING
			// do NOT modify the variables in the backtrace directly or
	        // objects passed by reference will be modified aswell
        	if (isset($backtrace[$key]['args'])) {
	        	$vars = array();
	        	foreach ($backtrace[$key]['args'] as $k => $v) {
		        	switch (gettype($v)) {
	        			case 'boolean' :
	        			case 'integer' :
	        			case 'double' :
	        				$vars[$k] = (string)$v;
	        				break;
	        			case 'string' :
	        				$vars[$k] = strlen($v) > 128 ? 'string('.strlen($v).')' : $v;
	        				break;
	        			case 'class' :
	        				$vars[$k] = get_class($v);
	        				break;
	        			default:
	        				$vars[$k] = gettype($v);
	        		}
		        }
	        	$backtrace[$key]['args'] = $vars;
        	}
        }
        $this->backtrace		= $backtrace;
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017DA end
    }

    /**
     * Short description of method getDateTime
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return int
     */
    public function getDateTime()
    {
        $returnValue = (int) 0;

        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017C8 begin
        $returnValue = $this->datetime;
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017C8 end

        return (int) $returnValue;
    }

    /**
     * Short description of method getDescription
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getDescription()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017CB begin
        $returnValue = $this->description;
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017CB end

        return (string) $returnValue;
    }

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

        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017CD begin
        $returnValue = $this->severity;
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017CD end

        return (int) $returnValue;
    }

    /**
     * Short description of method getBacktrace
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getBacktrace()
    {
        $returnValue = array();

        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017CF begin
        $returnValue = $this->backtrace;
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017CF end

        return (array) $returnValue;
    }

    /**
     * Short description of method getRequest
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getRequest()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017D1 begin
        $returnValue = $this->request;
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017D1 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getCallerFile
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getCallerFile()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017D6 begin
        $returnValue = $this->errorFile;
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017D6 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getCallerLine
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return int
     */
    public function getCallerLine()
    {
        $returnValue = (int) 0;

        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017D8 begin
        $returnValue = $this->errorLine;
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017D8 end

        return (int) $returnValue;
    }

    /**
     * Short description of method getTags
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getTags()
    {
        $returnValue = array();

        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017DC begin
        $returnValue = $this->tags;
        // section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:00000000000017DC end

        return (array) $returnValue;
    }

    /**
     * Short description of method getSeverityDescriptionString
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getSeverityDescriptionString()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--209aa8b7:134195b5554:-8000:0000000000001846 begin
        switch ($this->severity) {
        	case common_Logger::TRACE_LEVEL:
        		$returnValue = "TRACE";break;
        	case common_Logger::DEBUG_LEVEL:
        		$returnValue = "DEBUG";break;
        	case common_Logger::INFO_LEVEL:
        		$returnValue = "INFO";break;
        	case common_Logger::WARNING_LEVEL:
        		$returnValue = "WARNING";break;
        	case common_Logger::ERROR_LEVEL:
        		$returnValue = "ERROR";break;
        	case common_Logger::FATAL_LEVEL:
        		$returnValue = "FATAL";break;
        	default:
        		$returnValue = "UNKNOWN";
        }
        // section 127-0-1-1--209aa8b7:134195b5554:-8000:0000000000001846 end

        return (string) $returnValue;
    }

    /**
     * returns the user that was authentified while this item was created
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getUser()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-56e04748:1341d1d0e41:-8000:0000000000001832 begin
        $returnValue = $this->authentifiedUser;
        // section 127-0-1-1-56e04748:1341d1d0e41:-8000:0000000000001832 end

        return (string) $returnValue;
    }

} /* end of class common_log_Item */

?>
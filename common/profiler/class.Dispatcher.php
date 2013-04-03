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

/**
 * Generis Object Oriented API - common/log/class.Dispatcher.php
 *
 * This file is part of Generis Object Oriented API.
 *
 * @author Sam, <sam@taotesting.com>
 * @package common
 * @subpackage profiler
 */

/**
 * include common_log_Appender
 *
 * @author Sam, <sam@taotesting.com>
 */
require_once('common/log/interface.Appender.php');

/* user defined includes */
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001808-includes begin
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001808-includes end

/* user defined constants */
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001808-constants begin
// section 127-0-1-1--13fe8a1d:134184f8bc0:-8000:0000000000001808-constants end

/**
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package common
 * @subpackage log
 */
class common_profiler_Dispatcher
        implements common_profiler_Appender
{

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

    /**
     * Short description of method getLogThreshold
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return int
     */
    public function getLogThreshold()
    {
        $returnValue = (int) 0;

        return (int) $returnValue;
    }

    /**
     * Init profiler appenders according to config
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  array configuration
     * @return boolean
     */
    public function init($configuration)
    {
        $returnValue = (bool) false;

    	$this->appenders = array();
    	foreach ($configuration as $appenderConfig) {
    		if (isset($appenderConfig['class'])) {
    			
    			$classname = $appenderConfig['class'];
    			if (!class_exists($classname)){
    				$classname = 'common_profiler_'.$classname;
                }
    			if (class_exists($classname) && is_subclass_of($classname, 'common_profiler_Appender')) {
    				$appender = new $classname();
    				if (!is_null($appender) && $appender->init($appenderConfig)) {
    					$this->addAppender($appender);
    				}
    			}
    		}
    	}
    	$returnValue = (count($this->appenders) > 0);

        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return common_log_Dispatcher
     */
    public static function singleton()
    {
        $returnValue = null;

        if (is_null(self::$instance)) {
        	self::$instance = new common_profiler_Dispatcher();
        }
        $returnValue = self::$instance;

        return $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access private
     * @author Sam, <sam@taotesting.com>
     * @return mixed
     */
    private function __construct()
    {
    	if(isset($GLOBALS['COMMON_PROFILER_CONFIG'])) {
    		$this->init($GLOBALS['COMMON_PROFILER_CONFIG']);
		}else{
        	$this->init(array());
        }
    }

    /**
     * Short description of method addAppender
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  Appender appender
     * @return mixed
     */
    public function addAppender(common_log_Appender $appender)
    {
        $this->appenders[] = $appender;
    }

} /* end of class common_profiler_Dispatcher */

?>
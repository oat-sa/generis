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
 * 
 *
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package common
 * @subpackage log
 */
interface common_profiler_Appender
{

    /**
     * Short description of method getLogThreshold
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @return int
     */
    public function getLogThreshold();

    /**
     * Short description of method init
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  array configuration
     * @return boolean
     */
    public function init($configuration);
	
	/**
     * Log current context of execution
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  common_profiler_Context count
     * @return boolean
     */
	public function logContext(common_profiler_Context $context);
	
	/**
     * Log a specific event identified by a unique flag
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  string flag
     * @param  int duration
     * @param  int total
     * @return boolean
     */
	public function logEventTimer($flag, $duration, $total);
	
	/**
     * Log current context of execution
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  int memPeak
     * @param  int memMax
     * @return boolean
     */
	public function logMemoryPeakUsage($memPeak, $memMax);
	
	/**
     * Log the number of queries
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  integer count
     * @return boolean
     */
	public function logNrOfQueries($count);
	
	/**
     * Log slow queries and their parameters
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  array slowQueries
     * @return boolean
     */
	public function logSlowQueries($slowQueries);
	
	/**
     * Log stats on all queries
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  array slowQueries
     * @return boolean
     */
	public function logQuery($queries);
	
	/**
     * Finalize profiler data logging, the data bulk ready for processing or storage
     *
     * @access public
     * @author Sam, <sam@taotesting.com>
     * @param  array slowQueries
     * @return boolean
     */
	public function flush();
	
} /* end of interface common_profiler_Appender */
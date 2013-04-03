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
 * @access public
 * @author Sam, <sam@taotesting.com>
 * @package common
 * @subpackage log
 */
class common_profiler_SystemProfileAppender
        implements common_profiler_Appender
{

    /**
     * Short description of method getLogThreshold
     *
     * @access public
     * @author Sam
     * @return int
     */
    public function getLogThreshold()
    {
        $returnValue = (int) 0;

        return (int) $returnValue;
    }

    /**
     * Short description of method init
     *
     * @access public
     * @author Sam
     * @param  array configuration
     * @return boolean
     */
    public function init($configuration){
		
        $returnValue = (bool) false;
    	
    	if (isset($configuration['tag']) && !empty($configuration['tag'])) {
    		$this->tag = (isset($configuration['tag']) && !empty($configuration['tag'])) ? strval($configuration['tag']) : 'PROFILER';
    	}
    	$returnValue = true;

        return (bool) $returnValue;
    }
	
	protected function log($msg){
	}
	
	public function logContext(common_profiler_Context $context){
	}
	
	public function logEventTimer($flag, $duration, $total){
	}
	
	public function logMemoryPeakUsage($memPeak, $memMax){
	}
	
	public function logNrOfQueries($count){
	}
	
	public function logSlowQueries($slowQueries){
	}
	
	public function logQuery($queries){
	}
	
	public function flush(){
		//check if flush condition is fullfilled
	}
	
} /* end of abstract class common_profiler_SystemAppender */

?>
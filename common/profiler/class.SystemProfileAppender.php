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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
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
        extends common_profiler_Appender
{

	protected $data = array();

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
    	
    	parent::init($configuration);
		$this->data = array();
		
    	$returnValue = true;

        return (bool) $returnValue;
    }
	
	public function logContext(common_profiler_Context $context){
		$this->data['call'] = $context->getCalledUrl();
	}
	
	public function logTimer($flag, $duration, $total){
		if(!isset($this->data['timer'])){
			$this->data['timer'] = array();
	}
		if(!isset($this->data['timer'][$flag])){
			$this->data['timer'][$flag] = array();
		}
		$this->data['timer'][$flag][] = $duration;
	}
	
	public function logMemoryPeak($memPeak, $memMax){
		if(!isset($this->data['mem'])){
			$this->data['mem'] = array();
	}
		$this->data['mem']['peak'] = $memPeak;
		$this->data['mem']['max'] = $memMax;
	}
	
	public function logQueriesCount($count){
		if(!isset($this->data['query'])){
			$this->data['query'] = array();
	}
		$this->data['query']['count'] = $count;
	}
	
	public function logQueriesSlow($slowQueries){
		if(!isset($this->data['query'])){
			$this->data['query'] = array();
	}
	
		$logs = array();
		foreach($slowQueries as $statementKey => $queries){
			$count = count($queries);
			$logs[$statementKey] = array();
			for($i=0; $i<$count; $i++){
				$logs[$statementKey] = $queries[$i]->toArray();
	}
		}
	
		$this->data['query']['slow'] = $logs;
	}
	
	public function logQueriesSlowest($slowestQueries){
		if(!isset($this->data['query'])){
			$this->data['query'] = array();
		}
		$this->data['query']['slowest'] = array();
		foreach($slowestQueries as $query){
			$this->data['query']['slowest'][] = $query->toArray();
		}
	}
	
	public function logQueriesStat($queries){
		$count = $this->getConfigOption('queries', 'count');
		if(!is_null($count)){
			$this->data['query']['stat'] = $queries;
		}
	}
	
	public function flush(){
		//nothing to flush here
		var_dump($this->data);
		common_Logger::d($this->data, 'PROFILER');
	}
	
} /* end of abstract class common_profiler_SystemAppender */

?>
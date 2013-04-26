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
	protected $archivers = array();
	protected $backup = true;
	protected $file = '';
	protected $maxFileSize = 1048576;
	 
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
		
		if (isset($configuration['directory']) && !empty($configuration['directory'])) {
			if(is_dir($configuration['directory']) && is_writable($configuration['directory'])){
				$this->directory = strval($configuration['directory']);
			}else{
				throw new InvalidArgumentException('the "directory" is not writable');
			}
    	}else{
			throw new InvalidArgumentException('the "directory" is required in the configuration');
		}
		
		if(isset($configuration['sent_time_interval']) && !empty($configuration['sent_time_interval'])){
			$this->sentInterval = intval($configuration['sent_time_interval']);
		}
		
		if(isset($configuration['sent_backup']) && !empty($configuration['sent_backup'])){
			$this->backup = (bool) $configuration['sent_backup'];
		}
		
		$this->archivers = array();
		foreach ($configuration['archivers'] as $archiverConfig){
    		if(isset($archiverConfig['class'])){
    			$classname = $archiverConfig['class'];
    			if (!class_exists($classname)){
    				$classname = 'common_profiler_archiver_'.$classname;
                }
    			if (class_exists($classname)){
    				$archiver = new $classname();
    				if ($archiver instanceof common_profiler_archiver_Archiver && !is_null($archiver) && $archiver->init($archiverConfig)) {
						$this->archivers[] = $archiver;
					}
				}
			}
		}	
		
    	$fileName = (isset($configuration['file_name']) && !empty($configuration['file_name'])) ? strval($configuration['file_name']) : 'systemProfiles';
		$this->file = $this->directory.$fileName;
		
		$this->counterFile = $this->directory.'counter';
		
		$this->sentFolder = $this->directory.'sent';
		
    	if (isset($configuration['max_file_size'])) {
    		$this->maxFileSize = $configuration['max_file_size'];
    	}
		
    	$returnValue = true;

        return (bool) $returnValue;
    }
	
	public function logContext(common_profiler_Context $context){
		$this->data['context'] = $context->toArray();
	}
	
	public function logTimer($flag, $duration, $total){
		if(!isset($this->data['timer'])){
			$this->data['timer'] = array();
		}
		if(!isset($this->data['timer'][$flag])){
			$this->data['timer'][$flag] = array();
		}
		if(!isset($this->data['timer']['TOTAL'])){
			$this->data['timer']['TOTAL'] = $total;
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
	
	protected function clear(){
		if(file_exists($this->file)) helpers_File::remove($this->file);
		if(file_exists($this->counterFile)) helpers_File::remove($this->counterFile);
		if(file_exists($this->sentFolder)) helpers_File::remove($this->sentFolder);
	}
	
	public function flush(){
		
		$profileData = $this->data;
		
		$systemDataStr = '';
		if(isset($profileData['context']) && isset($profileData['context']['system'])){
			$systemDataStr = json_encode($profileData['context']['system']);
			unset($profileData['context']['system']);
		}
		
		$profileDataStr = json_encode($profileData);
		if(!file_exists($this->file) && !empty($systemDataStr)){
			//initilize file:
			$profileDataStr = '['.$systemDataStr.','.$profileDataStr;
		}
		
		
		$send = false;
		$currentTimestamp = time();
		if(file_exists($this->counterFile)){
			$lastSent = intval(file_get_contents($this->counterFile));
			$send = ($currentTimestamp > $lastSent + $this->sentInterval);
		}else{
			//initialize counter file somehow
			file_put_contents($this->counterFile, $currentTimestamp);
		}
		
		$profileDataStr .= ($send)?']':',';//finalize the file by closing the array or continue appending
		file_put_contents($this->file, $profileDataStr, FILE_APPEND);
		
		if($send){
			file_put_contents($this->counterFile, $currentTimestamp);
			foreach($this->archivers as $archiver){
				$archiver->store($this->file);
			}
			if($this->backup){
				helpers_File::copy($this->file, $this->sentFolder.DIRECTORY_SEPARATOR.'sent_'.$currentTimestamp, false);
				helpers_File::remove($this->file);
			}
		}
		
	}
	
} /* end of class common_profiler_SystemAppender */
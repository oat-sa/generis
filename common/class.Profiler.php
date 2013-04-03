<?php
/**
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
 * Profiler class
 *
 * @author Somsack Sipasseuth ,<sam@taotesting.com>
 * @package generis
 * @subpackage common
 */
class common_Profiler
{
	/*
	 * @var common_Profiler
	 */
    private static $instance = null;

    /**
     * @var int|mixed
     */
    private $startTime = 0;
    /**
     * @var array
     */
    private $startTimeLogs = array();
	
    /**
     * @var array
     */
    private $elapsedTimeLogs = array();
	
    /**
     * @var array
     */
    private $slowQueries = array();
	private $queries = array();
	
    /**
     * @var array
     */
    protected $loggers = array();//specify how messages should be logged, common_Logger, client popup
	
    /**
     * @var int
     */
    private $slowQueryTimer = 0;
	
    /**
     * @var int
     */
    private $slowQueryThreshold = 100;//in milliseconds (ms)

    /**
     * Singleton
     *
     * @static
     * @return null
     */
    public static function singleton()
    {
        $returnValue = null;

		if (is_null(self::$instance)){
			self::$instance = new self();
        }
		$returnValue = self::$instance;

        return $returnValue;
    }

    /**
     * Constructor
     *
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     * @access private
     */
    private function __construct()
    {
		$this->startTime = self::getCurrentTime();
	}

    /**
     *
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     * @return mixed
     */
    protected function getCurrentTime()
    {
		return microtime(true);
	}

    /**
     *
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public function register()
    {
		register_shutdown_function(array($this, 'shutdownProfiler'));
	}

    /**
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public function shutdownProfiler()
    {
		$this->logContext();
		$this->logEventTimer();
		$this->logMemoryPeakUsage();
		$this->logNrOfQueries();
		$this->logSlowQueries();
//		$this->logQueries();
	}

    /**
     *
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    protected function logEventTimer(){
		$total = $this->getCurrentTime() - $this->startTime;
		$totalRounded = round($total * 1000, 3);
		$sumDurations = 0;
		foreach ($this->elapsedTimeLogs as $event => $duration) {
			$this->log($event . ': ' . round($duration * 1000, 3) . 'ms/'.$totalRounded.'ms (' . round($duration / $total * 100, 1) . '%)');
			if ($event == 'start' || $event == 'dispatch') $sumDurations += $duration;
		}
		$uncovered = $total - $sumDurations;
		$this->log('???: ' . round($uncovered * 1000, 3) . 'ms/'.$totalRounded.'ms (' . round($uncovered / $total * 100, 1) . '%)');
	}
	
	/**
     *
     * @param $mem
     * @return string
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    protected function prettyPrintMemory($mem){
		$returnValue = '';
		if ($mem < 1024){
			$returnValue = $mem.'B';
		}else if($mem < 1048576){
			$returnValue = round($mem/1024, 2).'KB';
		}else{
			$returnValue = round($mem/1048576, 2).'MB';
		}
		return $returnValue;
	}
	
	/**
     *
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    protected function logMemoryPeakUsage(){
		$memPeak = memory_get_peak_usage(true);
		$memMax = ini_get('memory_limit');
		$memPc = 0;
        if(substr($memMax, -1) == 'M'){
			$memMax = substr($memMax, 0, -1);
			$memMax = $memMax*1048576;
		}
		$memPc = round($memPeak/$memMax*100,1);
		
		$this->log('peak mem usage: '.$this->prettyPrintMemory($memPeak).'/'.$this->prettyPrintMemory($memMax).' ('.$memPc.'%)');
	}
	
	/**
     *
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    protected function logNrOfQueries(){
		$this->log(core_kernel_classes_DbWrapper::singleton()->getNrOfQueries().' queries for this request');
	}
	
	/**
     *
     */
    protected function logContext(){
		$requestUrl = Context::getInstance()->getExtensionName() . '/' . Context::getInstance()->getModuleName() . '/' . Context::getInstance()->getActionName();
		$this->log('Profiling action called: '.$requestUrl);
		
		$this->log('server signature : '.$_SERVER['SERVER_SIGNATURE'].$_SERVER['SERVER_ADMIN'].' -> '.$_SERVER['PHP_SELF']);
	}
	
	/**
	 * 
     * @param $msg
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    protected function log($msg){
		common_Logger::d($msg, array('PROFILER'));
	}
	
	/**
     * @static
     * @param $flag
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public static function start($flag){
		self::singleton()->startTimer($flag);
	}
	
	/**
     * @static
     * @param $flag
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public static function stop($flag){
		self::singleton()->stopTimer($flag);
	}
	
	/**
     * @static
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     *
     */
    public static function queryStart(){
		self::singleton()->startSlowQuery();
	}
	
	/**
     * @static
     * @param $statement
     * @param array $params
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public static function queryStop($statement, $params = array()){
		self::singleton()->stopSlowQuery($statement, $params);
	}
	
	/**
     * @param string $flag
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public function startTimer($flag = 'global'){
		$this->startTimeLogs[$flag] = $this->getCurrentTime();
	}
	
	/**
	 * 
     * @param string $flag
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public function stopTimer($flag = 'global'){
		$startTime = isset($this->startTimeLogs[$flag])?$this->startTimeLogs[$flag]:$this->startTime;
		$this->elapsedTimeLogs[$flag] = $this->getCurrentTime() - $startTime;
	}
	
	/**
	 * 
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public function startSlowQuery(){
		$this->slowQueryTimer = $this->getCurrentTime();
	}
	
	/**
	 * 
     * @param $statement
     * @param array $params
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public function stopSlowQuery($statement, $params = array()){
		if($this->slowQueryTimer){//log only if timer has been started
			$statementKey = md5($statement);
			$time = $this->getCurrentTime() - $this->slowQueryTimer;
			$queryData = array(
				'statement' => $statement,
				'params' => $params,
				'time' => $time
			);
			if(1000*$time > $this->slowQueryThreshold){//compare to threshold (ms)
				if(!isset($this->slowQueries[$statementKey])){
					$this->slowQueries[$statementKey] =  array();
				}
				$this->slowQueries[$statementKey][] = $queryData;
			}
			
			if(!isset($this->queries[$statementKey])){
				$this->queries[$statementKey] = array('statement' => $statement, 'count'=>0, 'cumul'=>0);
			}
			$this->queries[$statementKey]['count']++;
			$this->queries[$statementKey]['cumul'] += $time;
		}
		$this->slowQueryTimer = 0;
	}
	
	/**
     *
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    protected function logSlowQueries(){
		foreach($this->slowQueries as $logs) {
			$count = count($logs);
			for($i=0;$i<$count;$i++){
				$this->log('slow query: '.$logs[$i]['statement'].' : '.round($logs[$i]['time']*1000, 3).'ms');
			}
		}
	}
	
	protected function logQueries(){
		foreach($this->queries as $query){
			if($query['count']>10) $this->log('Query count: '.$query['statement'].' => '.$query['count']);
		}
	}
	
	/**
	 * (experimental)
	 * call common_Profiler::singleton()->initMysqlProfiler(); when after the DbWrapper has been constructed
	 * 
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public function initMysqlProfiler(){
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		if($dbWrapper instanceof core_kernel_classes_MysqlDbWrapper){
			$dbWrapper->exec('SET profiling = 1');
			$this->log('MySQL profiler enabled');
		}
	}

	/*
	 * (experimental)
	 * log profiles from mysql built-in profiler
	 * 
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    protected function logMysqlProfiles(){
		
		$this->initMysqlProfiler();
		
		//get sql profile data
		$profiles = array();
		$profile = array();
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		
		$sqlProfilesResult = $dbWrapper->query('SHOW PROFILES');
		while ($row = $sqlProfilesResult->fetch()) {
			$id = $row['Query_ID'];

			$sqlDataResult = $dbWrapper->query('SHOW PROFILE FOR QUERY ' . $id);
			$profileData = array();
			while ($r = $sqlDataResult->fetch()) {
				$profileData[$r[0]] = $r;
			}

			$profiles[$id] = $profileData;
			$profiles[$id]['duration'] = $row['Duration'];
			$profiles[$id]['query'] = $row['Query'];
		}

		$sqlResult = $dbWrapper->query('SHOW PROFILE');
		while ($row = $sqlResult->fetch()) {
			$profile[$row[0]] = $row;
		}

		$report = array(
			'queriesCount' => $dbWrapper->getNrOfQueries(),
			'profile' => $profile,
			'profiles' => $profiles
		);
		
		$this->log(json_encode($report));
	}
	
}
?>

<?php
class common_Profiler
{
	
	private static $instance = null;
	private $startTime = 0;
	private $startTimeLogs = array();
	private $elapsedTimeLogs = array();
	private $slowQueries = array();
	protected $loggers = array();//specify how messages should be logged, common_Logger, client popup
	private $slowQueryTimer = 0;
	private $slowQueryThreshold = 100;//in milliseconds (ms)
	
	public static function singleton()
    {
        $returnValue = null;

		if (is_null(self::$instance)){
			self::$instance = new self();
        }
		$returnValue = self::$instance;

        return $returnValue;
    }
	
	private function __construct()
    {
		$this->startTime = self::getCurrentTime();
	}
	
	protected function getCurrentTime()
    {
		return microtime(true);
	}
	
	public function register()
    {
		register_shutdown_function(array($this, 'shutdownProfiler'));
	}
	
	public function shutdownProfiler()
    {
		$this->logRequestUrl();
		$this->logTimer();
		$this->logMemoryPeakUsage();
		$this->logNrOfQueries();
		$this->logSlowQueries();
	}
	
	protected function logTimer(){
		$total = $this->getCurrentTime() - $this->startTime;
		$totalRounded = round($total * 1000, 3);
		$sumDurations = 0;
		foreach ($this->elapsedTimeLogs as $event => $duration) {
			$this->log($event . ': ' . round($duration * 1000, 3) . 'ms/'.$totalRounded.'ms (' . round($duration / $total * 100, 1) . '%)');
			if ($event == 'start' || $event == 'dispatch')
				$sumDurations += $duration;
		}
		$uncovered = $total - $sumDurations;
		$this->log('???: ' . round($uncovered * 1000, 3) . 'ms/'.$totalRounded.'ms (' . round($uncovered / $total * 100, 1) . '%)');
	}
	
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
	
	protected function logNrOfQueries(){
		$this->log(core_kernel_classes_DbWrapper::singleton()->getNrOfQueries().' queries for this request');
	}
	
	protected function logRequestUrl(){
		$requestUrl = Context::getInstance()->getExtensionName() . '/' . Context::getInstance()->getModuleName() . '/' . Context::getInstance()->getActionName();
		$this->log('Profiling action called: '.$requestUrl);
	}
	protected function log($msg){
		common_Logger::d($msg, array('PROFILER'));
	}
	
	public static function start($flag){
		self::singleton()->startTimer($flag);
	}
	
	public static function stop($flag){
		self::singleton()->stopTimer($flag);
	}
	
	public static function queryStart(){
		self::singleton()->startSlowQuery();
	}
	
	public static function queryStop($statement, $params = array()){
		self::singleton()->stopSlowQuery($statement, $params);
	}
	
	public function startTimer($flag = 'global'){
		$this->startTimeLogs[$flag] = $this->getCurrentTime();
	}
	
	public function stopTimer($flag = 'global'){
		$startTime = isset($this->startTimeLogs[$flag])?$this->startTimeLogs[$flag]:$this->startTime;
		$this->elapsedTimeLogs[$flag] = $this->getCurrentTime() - $startTime;
	}
	
	public function startSlowQuery(){
		$this->slowQueryTimer = $this->getCurrentTime();
	}
	
	public function stopSlowQuery($statement, $params = array()){
		if($this->slowQueryTimer){//log only if timer has been started
			$statementKey = md5($statement);
			$time = $this->getCurrentTime() - $this->slowQueryTimer;
			if(1000*$time > $this->slowQueryThreshold){//compare to threshold (ms)
				if(!isset($this->slowQueries[$statementKey])){
					$this->slowQueries[$statementKey] =  array();
				}
				$this->slowQueries[$statementKey][] = array(
					'statement' => $statement,
					'params' => $params,
					'time' => $time
				);
			}
		}
		$this->slowQueryTimer = 0;
	}
	
	protected function logSlowQueries(){
		foreach($this->slowQueries as $logs) {
			$count = count($logs); 
			for($i=0;$i<$count;$i++){
				$this->log('slow query : '.$logs[$i]['statement'].' : '.round($logs[$i]['time']*1000, 3).'ms');
			}
		}
	}
	
	/*
	 * (experimental)
	 * call common_Profiler::singleton()->initMysqlProfiler(); when after the DbWrapper has been constructed
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

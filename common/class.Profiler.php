<?php
class common_Profiler
{
	private static $instance = null;
	private $startTime = 0;
	private $startTimeLogs = array();
	private $elapsedTimeLogs = array();
	
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
	
	private function getCurrentTime()
    {
		return microtime(true);
	}
	
	public function register()
    {
		register_shutdown_function(array($this, 'shutdownProfiler'));
	}
	
	public function shutdownProfiler()
    {
		$this->log('profiler shutting down');
		$this->logTimer();
		$this->logMemoryPeakUsage();
		$this->logNrOfQueries();
	}
	
	public function logTimer(){
		$total = $this->getCurrentTime() - $this->startTime;
		$totalRounded = round($total * 1000, 3);
		$sumDurations = 0;
		foreach ($this->elapsedTimeLogs as $event => $duration) {
			$this->log($event . ': ' . round($duration * 1000, 3) . 'ms/'.$totalRounded.'ms (' . round($duration / $total * 100, 1) . '%)');
			if ($event == 'start' || $event == 'dispatch')
				$sumDurations += $duration;
		}
		$uncovered = $total - $sumDurations;
		$this->log('uncovered: ' . round($uncovered * 1000, 3) . 'ms/'.$totalRounded.'ms (' . round($uncovered / $total * 100, 1) . '%)');
	}
	
	private function prettyPrintMemory($mem){
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
	
	public function logMemoryPeakUsage(){
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
	
	public function logNrOfQueries(){
		$this->log(core_kernel_classes_DbWrapper::singleton()->getNrOfQueries().' queries for this request');
	}
	
	private function log($msg){
		common_Logger::d($msg, array('PROFILER'));
	}
	
	public static function start($eventKey){
		self::singleton()->startTimer($eventKey);
	}
	
	public static function stop($eventKey){
		self::singleton()->stopTimer($eventKey);
	}
	
	public function startTimer($eventKey = 'global'){
		$this->startTimeLogs[$eventKey] = $this->getCurrentTime();
	}
	
	public function stopTimer($eventKey = 'global'){
		$startTime = isset($this->startTimeLogs[$eventKey])?$this->startTimeLogs[$eventKey]:$this->startTime;
		$this->elapsedTimeLogs[$eventKey] = $this->getCurrentTime() - $startTime;
	}
}
?>

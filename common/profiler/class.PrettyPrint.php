<?php

class common_profiler_PrettyPrint{
	
	/**
     *
     * @param $mem
     * @return string
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public static function memory($mem){
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
     * @param $mem
     * @return string
     * @author Somsack Sipasseuth ,<sam@taotesting.com>
     */
    public static function percentage($part, $total, $decimal = 1){
		return round($part/$total*100,1);
	}
}

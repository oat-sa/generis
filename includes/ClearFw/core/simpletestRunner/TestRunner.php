<?php

set_time_limit(900);	//a suite must never takes more than 15minutes!

/**
 *
 * Help you to run the test into the ClearFw Context
 *
 * @author CRP Henri Tudor - TAO Team
 * @license GPLv2
 *
 */

class TestRunner
{
    /**
     * Search and find test case into a directory
     * @param string $path to folder to search in
     * @param boolean $recursive if true it checks the subfoldfer
     * @return array the list of test cases paths
     */
    public static function findTest($path, $recursive = false){
        $tests = array();
        if(file_exists($path)){
            if(is_dir($path)){
                foreach(scandir($path) as $file){
                    if(!preg_match("/^\./",$file)){
                        if(is_dir($path."/".$file) && $recursive){
                            $tests = array_merge($tests, self::findTest($path."/".$file, true));
                        }
                        if(preg_match("/TestCase\.php$/", $file)){
                            $tests[] = $path."/".$file;
                        }
                    }
                }
            }
        }
        return $tests;
    }

}

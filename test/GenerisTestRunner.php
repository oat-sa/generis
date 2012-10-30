<?php
require_once dirname(__FILE__).'/../common/inc.extension.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';
require_once INCLUDES_PATH.'/ClearFw/core/simpletestRunner/_main.php';


/**
 * @author CRP Henri Tudor - TAO Team
 * @license GPLv2
 *
 */

class GenerisTestRunner extends TestRunner
{
    /**
     *
     * @var boolean
     */
    private static $connected = false;

    /**
     * shared methods for test initialization
     */
    public static function initTest(){
        //connect the API
        if(!self::$connected){
            core_control_FrontController::connect(SYS_USER_LOGIN, SYS_USER_PASS, DATABASE_NAME);
            self::$connected = true;
        }
    }
}

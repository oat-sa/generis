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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
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
        	$userService = core_kernel_users_Service::singleton();
        	$userService->login(SYS_USER_LOGIN, SYS_USER_PASS, new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole'));
            self::$connected = true;
        }
    }
}

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
 * Generis Object Oriented API - common\inc.extension.php
 *
 * This file is part of Generis Object Oriented API.
 *
 *
 * @author lionel.lecaque@tudor.lu
 * @package generis
 * @subpackage common
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

/*
 * to use it in command line
 */
if(PHP_SAPI == 'cli'){
	$_SERVER['HTTP_HOST'] = 'http://localhost';
	$_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__).'/../..';
}
//load config
require_once  dirname(__FILE__). '/class.Config.php';
common_Config::load();

//load constants
require_once  dirname(__FILE__). '/constants.php';

//set the time zone
if(function_exists("date_default_timezone_set") && defined('TIME_ZONE')){
	date_default_timezone_set(TIME_ZONE);
}

//used for backward compat
$GLOBALS['default_lang']	= 'EN';

require_once INCLUDES_PATH		. '/ClearFw/clearbricks/common/lib.l10n.php';

// 3rd part lib autoload
require_once VENDOR_PATH		. 'autoload.php';

/**
 * permits to include classes automatically
 *
 * @function generis_autoload
 * @param 	string		$pClassName		Name of the class
 * @package generis
 * @subpackage common
 *
 */

function generis_extension_autoload($pClassName) {
	
    if(strpos($pClassName, '_') !== false){
		$tokens = explode("_", $pClassName);
		$size = count($tokens);
		$path = '';
		for ( $i = 0 ; $i<$size-1 ; $i++){
			$path .= $tokens[$i].'/';
		}
		
		$filePath = '/' . $path . 'class.'.$tokens[$size-1] . '.php';
		if (file_exists(GENERIS_BASE_PATH .$filePath)){
			require_once GENERIS_BASE_PATH .$filePath;
			return;
		}
		$filePathInterface = '/' . $path . 'interface.'.$tokens[$size-1] . '.php';
		if (file_exists(GENERIS_BASE_PATH .$filePathInterface)){
			require_once GENERIS_BASE_PATH .$filePathInterface;
			return;
		}
		
		if (file_exists(ROOT_PATH .$filePath)){
			require_once ROOT_PATH .$filePath;
			return;
		} elseif (file_exists(ROOT_PATH .$filePathInterface)){
		        require_once ROOT_PATH .$filePathInterface;
		        return;
		}
	}
	else{
		$packages = array(DIR_CORE,DIR_CORE_HELPERS,DIR_CORE_UTILS);
		foreach($packages as $path) {
			if (file_exists($path. $pClassName . '.class.php')) {
				require_once $path . $pClassName . '.class.php';
				return;
			}
		}
	}
}

spl_autoload_register("generis_extension_autoload");
set_include_path(get_include_path() . PATH_SEPARATOR . GENERIS_BASE_PATH);

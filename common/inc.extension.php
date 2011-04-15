<?php
error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common\inc.extension.php
 *
 * This file is part of Generis Object Oriented API.
 *
 *
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

/*
 * to use it in command line
 */
if(PHP_SAPI == 'cli'){
	$_SERVER['HTTP_HOST'] = 'http://localhost';
	$_SERVER['DOCUMENT_ROOT'] = dirname(__FILE__).'/../..';
}

require_once  dirname(__FILE__). '/config.php';
require_once  dirname(__FILE__). '/constants.php';

//set the time zone
if(function_exists("date_default_timezone_set") && defined('TIME_ZONE')){
	date_default_timezone_set(TIME_ZONE);
}

#log
#	 debug_level 	= 0;
#	 info_level 	= 1;
#	 warning_level 	= 2;
#	 error_level	= 3;
#	 fatal_level 	= 4;
$GLOBALS['config_log'] 		= array(
array(	'nom' => 'FileAppender', 
		'level' =>  0 , 		
		'config' => dirname(__FILE__).'/../log/log.txt'),

);
//used for backward compat
$GLOBALS['default_lang']	= 'EN';

require_once dirname(__FILE__)	. '/ext/class.ClassLoader.php';
require_once INCLUDES_PATH		. '/ClearFw/clearbricks/common/_main.php';

$__generis_autoload['filemanager']			= INCLUDES_PATH.'/ClearFw/clearbricks/filemanager/class.filemanager.php';
$__generis_autoload['fileZip']				= INCLUDES_PATH.'/ClearFw/clearbricks/zip/class.zip.php';
$__generis_autoload['fileUnzip']			= INCLUDES_PATH.'/ClearFw/clearbricks/zip/class.unzip.php';


global $__classLoader;
$__classLoader = new common_ext_ClassLoader();
$__classLoader->setFiles($__generis_autoload);
$__classLoader->addPackage( INCLUDES_PATH.'/ClearFw/log/');
$__classLoader->addPackage(DIR_CORE);
$__classLoader->addPackage(DIR_CORE_HELPERS);
$__classLoader->addPackage(DIR_CORE_UTILS);

/**
 * @function generis_autoload
 * permits to include classes automatically
 * @param 	string		$pClassName		Name of the class
 */

function generis_extension_autoload($pClassName) {
	
	global $__classLoader;
	
	
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
		if (file_exists(ROOT_PATH .$filePath)){
			require_once ROOT_PATH .$filePath;
			return;
		}
	}
	else{
		$files = $__classLoader->getFiles();
		if(isset($files[$pClassName])){
			require_once ($files[$pClassName]);
			return;
		}
		foreach($__classLoader->getPackages() as $path) {
			if (file_exists($path. $pClassName . '.class.php')) {
				require_once $path . $pClassName . '.class.php';	
				return;
			}
		}
	}
}

spl_autoload_register("generis_extension_autoload");
set_include_path(get_include_path() . PATH_SEPARATOR . GENERIS_BASE_PATH);

$extensionManager = common_ext_ExtensionsManager::singleton();
$extensionManager->loadExtensions();

?>
<?php
/**
 * Generis Object Oriented API - common\common.php
 *
 *
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 02.04.2009, 14:14:33 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author lionel.lecaque@tudor.lu
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License (GPL) Version 2
 * @package common
 */

if(!isset($_SESSION))
session_start();

# all error
error_reporting(E_ALL);

# xdebug custom error reporting
if (function_exists("xdebug_enable"))  {
	xdebug_enable();
}

require_once dirname(__FILE__). '/config.php';
require_once  dirname(__FILE__). '/constants.php';

require_once dirname(__FILE__). '/ext/class.ClassLoader.php';

require_once INCLUDES_PATH.'/ClearFw/clearbricks/common/_main.php';

# plugins directory
define("DIR_PLUGIN"			, dirname(__FILE__). "/../plugins/");

# actions directory
define("DIR_ACTIONS"		, dirname(__FILE__). "/../actions/");

# models directory
define("DIR_MODELS"			, dirname(__FILE__). "/../models/");

# plugin directory
define('DIR_PLUGINS'		, dirname(__FILE__).'/../plugins/');

# views directory
define("DIR_VIEWS"			, dirname(__FILE__).'/../views/');

# helpers directory
define("DIR_HELPERS"		, dirname(__FILE__) . "/../helpers/");

# session namespace
//define('SESSION_NAMESPACE', 'ClearFw');

# default module name
define('DEFAULT_MODULE_NAME', 'ExtensionsManager');

#default action name
define('DEFAULT_ACTION_NAME', 'index');

#BASE PATH: the root path in the file system (usually the document root)
//define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);
define('BASE_PATH', ROOT_PATH . '/generis');


#BASE URL (usually the domain root)
define('BASE_URL', ROOT_URL. '/generis');

#BASE WWW the web resources path
define('BASE_WWW', BASE_URL . '/' . DIR_VIEWS);							

# theme directory
$GLOBALS['dir_theme']		= "/";

# language
$GLOBALS['lang']			= 'en';


$__generis_autoload['accesBD']				= dirname(__FILE__).'/../core/kernel/accesBD.php' ;
$__generis_autoload['accesBDsubscriber']	= dirname(__FILE__).'/../core/kernel/accesBDSubscriber.php';
$__generis_autoload['API_impl']				= dirname(__FILE__).'/../core/kernel/API_impl.php' ;
$__generis_autoload['rdfs_cache']			= dirname(__FILE__).'/../core/kernel/cache.php';
$__generis_autoload['generisModel']			= dirname(__FILE__).'/../core/kernel/model.php' ;
$__generis_autoload['modelManager']			= dirname(__FILE__).'/../core/kernel/modelManager.php';
$__generis_autoload['generisrdfmodel']		= dirname(__FILE__).'/../core/kernel/rdfmodel.php' ;
$__generis_autoload['generisrdfsmodel']		= dirname(__FILE__).'/../core/kernel/rdfsmodel.php';
$__generis_autoload['filemanager']			= INCLUDES_PATH.'/ClearFw/clearbricks/filemanager/class.filemanager.php';
$__generis_autoload['fileZip']				= INCLUDES_PATH.'/ClearFw/clearbricks/zip/class.zip.php';
$__generis_autoload['fileUnzip']			= INCLUDES_PATH.'/ClearFw/clearbricks/zip/class.unzip.php';
global $__classLoader;
$__classLoader = new common_ext_ClassLoader();
$__classLoader->setFiles($__generis_autoload);

$__classLoader->addPackage(INCLUDES_PATH.'/ClearFw/log/');
$__classLoader->addPackage(DIR_ACTIONS);
$__classLoader->addPackage(DIR_CORE);
//clearfw helpers
$__classLoader->addPackage(DIR_CORE_HELPERS);
$__classLoader->addPackage(DIR_CORE_UTILS);
//generis helpers
$__classLoader->addPackage(DIR_HELPERS);

/**
 * @function generis_autoload
 * permits to include classes automatically
 * @param 	string		$pClassName		Name of the class
 */

function generis_autoload($pClassName) {
	
		global $__classLoader;
	
		$files = $__classLoader->getFiles();
		if(!empty($files) && is_array($files)){
			if(isset($files[$pClassName])){
				require_once ($files[$pClassName]);
				return;
			}
		}
		$packages = $__classLoader->getPackages();
	
		if(!empty($packages) && is_array($packages)){
			foreach($packages as $path) {
				
				if (file_exists($path. $pClassName . '.class.php')) {
					require_once $path . $pClassName . '.class.php';	
					return;
				}
				if (file_exists($path. 'class.'.$pClassName . '.php')) {
					require_once $path . 'class.'. $pClassName . '.php';
					return;
				}
			}
		}
		$split = explode("_",$pClassName);
		$path = dirname(__FILE__).'/../';
		for ( $i = 0 ; $i<sizeof($split)-1 ; $i++){
			$path .= $split[$i].'/';
		}
		$filePath = $path . 'class.'.$split[sizeof($split)-1] . '.php';
		if (file_exists($filePath)){
			require_once $filePath;
			return;
		}
	

}

spl_autoload_register("generis_autoload");
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/..');


helpers_Scriptloader::addCssFiles(array(
	BASE_WWW . 'css/custom-theme/jquery-ui-1.8.custom.css',
	BASE_WWW . 'js/jwysiwyg/jquery.wysiwyg.css',
	BASE_WWW . 'js/jquery.jqGrid-3.6.4/css/ui.jqgrid.css',
	BASE_WWW . 'css/layout.css',
	BASE_WWW . 'js/jquery.uploadify-v2.1.0/uploadify.css'
));

$gridi18nFile = 'js/jquery.jqGrid-3.6.4/js/i18n/grid.locale-'.strtolower($GLOBALS['lang']).'.js';
if(!file_exists(BASE_PATH. '/views' . $gridi18nFile)){
	$gridi18nFile = 'js/jquery.jqGrid-3.6.4/js/i18n/grid.locale-en.js';
}

helpers_Scriptloader::addJsFiles(array(
	BASE_WWW . 'js/jquery-1.4.2.min.js',
	BASE_WWW . 'js/jquery-ui-1.8.custom.min.js',
	BASE_WWW . 'js/jsTree/jquery.tree.min.js',
	BASE_WWW . 'js/jsTree/plugins/jquery.tree.contextmenu.js',
	BASE_WWW . 'js/jsTree/plugins/jquery.tree.checkbox.js',
	BASE_WWW . 'js/jwysiwyg/jquery.wysiwyg.js',
	BASE_WWW . $gridi18nFile,
	BASE_WWW . 'js/jquery.jqGrid-3.6.4/js/jquery.jqGrid.min.js',
	BASE_WWW . 'js/jquery.numeric.js',
	ROOT_URL 	. '/filemanager/views/js/fmRunner.js',
	ROOT_URL 	. '/filemanager/views/js/jquery.fmRunner.js',
	BASE_WWW . 'js/eventMgr.js',
	BASE_WWW . 'js/gateway/Main.js',
	BASE_WWW . 'js/helpers.js',
	BASE_WWW . 'js/uiBootstrap.js',
	BASE_WWW . 'js/jquery.uploadify-v2.1.0/jquery.uploadify.v2.1.0.min.js',
	BASE_WWW . 'js/jquery.uploadify-v2.1.0/swfobject.js'
));





?>
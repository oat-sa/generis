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

# xdebug custom error reporting
if (function_exists("xdebug_enable"))  {
	xdebug_enable();
}

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

# default module name
define('DEFAULT_MODULE_NAME', 'ExtensionsManager');

#default action name
define('DEFAULT_ACTION_NAME', 'index');

#BASE PATH: the root path in the file system (usually the document root)
define('BASE_PATH', ROOT_PATH . '/generis');


#BASE URL (usually the domain root)
define('BASE_URL', ROOT_URL. '/generis');

# theme directory
$GLOBALS['dir_theme']		= "/";

# language
$GLOBALS['lang']			= 'en';

?>
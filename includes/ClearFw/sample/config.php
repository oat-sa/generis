<?php
/**
 * main configuration file
 */

# plugins directory
define("DIR_PLUGIN"			, dirname(__FILE__). "/../plugins/");

# actions directory
define("DIR_ACTIONS"		, dirname(__FILE__). "/../actions/");

# models directory
define("DIR_MODELS"			, dirname(__FILE__). "/../models/");

# plugin directory
define('DIR_PLUGINS'		, dirname(__FILE__).'/../plugins/');

# views directory
define("DIR_VIEWS"			, "views/");

# helpers directory
define("DIR_HELPERS"		, dirname(__FILE__) . "/../helpers/");

# core directory
define("DIR_CORE"			, dirname(__FILE__) . "/core/");

# core helpers directory
define("DIR_CORE_HELPERS"	, DIR_CORE . "helpers/");

# core utils directory
define("DIR_CORE_UTILS"		, DIR_CORE . "util/");

# database config
define("DATABASE_LOGIN"		, "root");
define("DATABASE_PASS"		, "");
define("DATABASE_URL"		, "");
define("DATABASE_DRIVER"	, "");
define("DATABASE_NAME"		, "localhost");

# session namespace
define('SESSION_NAMESPACE', 'PHPFramework');

# default module name
define('DEFAULT_MODULE_NAME', 'AdvancedDefault');

#default action name
define('DEFAULT_ACTION_NAME', 'index');

$GLOBALS['classpath']			= array(DIR_CORE,
										DIR_CORE_UTILS,
										DIR_ACTIONS,
										DIR_MODELS);

# theme directory
$GLOBALS['dir_theme']		= "default/";

# language
$GLOBALS['lang']			= 'en';
?>
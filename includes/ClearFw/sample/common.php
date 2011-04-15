<?php
/**
 * this code is executed before all other script
 */

# constants definition
define('HTTP_GET', 'GET');
define('HTTP_POST', 'POST');
define('HTTP_PUT', 'PUT');
define('HTTP_DELETE', 'DELETE');
define('HTTP_HEAD', 'HEAD');

# all error
error_reporting(E_ALL);

# xdebug custom error reporting
if (function_exists("xdebug_enable"))  {
	xdebug_enable();
}

require_once dirname(__FILE__). "/config.php";
require dirname(__FILE__).'/clearbricks/common/_main.php';

/**
 * @function fw_autoload
 * permits to include classes automatically
 * @param 	string		$pClassName		Name of the class
 */

function fw_autoload($pClassName) {
	if (isset($GLOBALS['classpath']) && is_array($GLOBALS['classpath'])) {
		foreach($GLOBALS['classpath'] as $path) {
			if (file_exists($path. $pClassName . '.class.php')) {
    			require_once $path . $pClassName . '.class.php';
    			break;
			}
		}
	}
}

spl_autoload_register("fw_autoload");
spl_autoload_register("Plugin::pluginClassAutoLoad");

?>
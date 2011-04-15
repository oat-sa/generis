<?php

$GLOBALS['classpath'] = array(dirname(__FILE__).'/../');
function __autoload($pClassName) {

	$found = false;
	if (isset($GLOBALS['classpath']) && is_array($GLOBALS['classpath'])) {
		foreach($GLOBALS['classpath'] as $path) {
			if (file_exists($path. $pClassName . '.class.php')) {
    			require_once $path . $pClassName . '.class.php';
    			$found = true;
    			break;
			}
		}
	}
	if (!$found) {
		echo '__autoload(): Class \''.$pClassName.'\' not found<br />';
		echo 'classPath: <br />';
		var_dump($GLOBALS['classpath']);
		exit;
	}
	
}
$GLOBALS['config_log'] = array(
	array('nom' => 'FileAppender', 'level' => Logger::debug_level, 'config' => '/tmp/log.txt'),
	);	
error_reporting(E_ALL);
$logger = new Logger('test', Logger::debug_level);
$logger->debug('test', __FILE__, __LINE__);
?>

<?php
# local namespace
define('LOCAL_NAMESPACE', '');

# paths
define('ROOT_PATH', '');
define('ROOT_URL',  '');

# language
define('DEFAULT_LANG', '');
$GLOBALS['default_lang']	= DEFAULT_LANG;

#mode
define('DEBUG_MODE', false);

#application state
define('SYS_READY', true);

# background user: to be used only for system related tasks
define('SYS_USER_LOGIN', 'generis');
define('SYS_USER_PASS', md5('g3n3r1s'));

#the time zone, required since PHP5.3
define("TIME_ZONE", 'Europe/Paris');

# Cache
define('CACHE_MAX_SIZE', 64000);



#if there is a .htaccess with an http auth, used for Curl request or virtual http requests
define('USE_HTTP_AUTH', false);
define('USE_HTTP_USER', '');
define('USE_HTTP_PASS', '');
<?php

#Generis framework config 

#generis paths
define('INCLUDES_PATH' , GENERIS_BASE_PATH.'/includes');
define('EXTENSION_PATH' , GENERIS_BASE_PATH.'/..');
define('MANIFEST_NAME' , 'manifest.php');
define('GENERIS_FILES_PATH' , GENERIS_BASE_PATH.'/data/');
define('GENERIS_CACHE_PATH', GENERIS_FILES_PATH.'cache/');

# uri providers ('MicrotimeUriProvider'|'MicrotimeRandUriProvider'|'DatabaseSerialUriProvider')
define('GENERIS_URI_PROVIDER', 'DatabaseSerialUriProvider');

# path to RDFAPI-PHP
define('RDFAPI_INCLUDE_DIR', INCLUDES_PATH.'/rdfapi-php/api/');

# session namespace
define('SESSION_NAMESPACE', 'ClearFw');

# core directory
define("DIR_CORE"			, INCLUDES_PATH . "/ClearFw/core/");

# core helpers directory
define("DIR_CORE_HELPERS"	, DIR_CORE . "helpers/");

# core utils directory
define("DIR_CORE_UTILS"		, DIR_CORE . "util/");

# constants definition
define('HTTP_GET', 		'GET');
define('HTTP_POST', 	'POST');
define('HTTP_PUT', 		'PUT');
define('HTTP_DELETE', 	'DELETE');
define('HTTP_HEAD', 	'HEAD');

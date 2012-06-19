<?php
#log
#	 trace_level 	= 0;
#	 debug_level 	= 1;
#	 info_level 	= 2;
#	 warning_level 	= 3;
#	 error_level	= 4;
#	 fatal_level 	= 5;
$GLOBALS['COMMON_LOGGER_CONFIG'] = array(
/*
 array(
 		'class'			=> 'SingleFileAppender',
 		'threshold'		=> 4 ,
 		'file'			=> dirname(__FILE__).'/../../log/error.txt',
 		'format'		=> '%m'
 ),
array(
		'class'			=> 'ArchiveFileAppender',
		'mask'			=> 62 , // 111110
		'tags'			=> array('GENERIS', 'TAO')
		'file'			=> '/var/log/tao/debug.txt',
		'directory'		=> '/var/log/tao/',
		'max_file_size'	=> 10000000
),
		array(
				'class'			=> 'UDPAppender',
				'host'			=> '127.0.0.1',
				'port'			=> 5775,
				'threshold'		=> 1
		)
		/**/
);
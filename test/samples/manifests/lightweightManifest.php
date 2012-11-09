<?php
/**
 * This lightweight manifest is based on the TAO filemanager one.
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
$extpath = dirname(__FILE__).DIRECTORY_SEPARATOR;
$taopath = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'tao'.DIRECTORY_SEPARATOR;

return array(
	'name' => 'lightweight',
	'description' => 'lightweight testing manifest',
	'version' => '1.0',
	'author' => 'TAO Team',
	'dependencies' => array('tao'),
	'classLoaderPackages' => array(
		dirname(__FILE__).'/actions/'
	 ),
	'models' => array(
			'http://www.tao.lu/Ontologies/taoFuncACL.rdf'
	),
	'constants' => array(
		# actions directory
		"DIR_ACTIONS"			=> $extpath."actions".DIRECTORY_SEPARATOR,
	
		# models directory
		"DIR_MODELS"			=> $extpath."models".DIRECTORY_SEPARATOR,
	
		# views directory
		"DIR_VIEWS"				=> $extpath."views".DIRECTORY_SEPARATOR,
	
		# helpers directory
		"DIR_HELPERS"			=> $extpath."helpers".DIRECTORY_SEPARATOR,
	
		# default module name
		'DEFAULT_MODULE_NAME'	=> 'Browser',
	
		#default action name
		'DEFAULT_ACTION_NAME'	=> 'index',
	
		#BASE PATH: the root path in the file system (usually the document root)
		'BASE_PATH'				=> $extpath ,
	
		#BASE URL (usually the domain root)
		'BASE_URL'				=> ROOT_URL . 'filemanager/',
	
		#BASE WWW the web resources path
		'BASE_WWW'				=> ROOT_URL . 'filemanager/views/',
	 
	
		#root folder for the files
		'BASE_DATA'				=> $extpath.'views'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR,
		'URL_DATA'				=> ROOT_URL . 'filemanager/views/data/',
	
		#TAO extension Paths
		'TAOBASE_WWW'			=> ROOT_URL  . 'tao/views/',
		'TAOVIEW_PATH'			=> $taopath.'views'.DIRECTORY_SEPARATOR,
		'TAO_TPL_PATH'			=> $taopath.'views'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR,
	
		# use the filemanager without authentication, please be carefull!
		'NO_FM_AUTH'			=> false,

		# Max file size to upload
		'UPLOAD_MAX_SIZE'		=> '3145728',
	
		# Allowed media for upload
		'allowed_media'			=> array(
			'application/ogg',						//OGG
			'audio/ogg',
			'video/ogg',
			'application/pdf',						//PDF
			'application/x-shockwave-flash',		//Flash
			'application/x-subrip',					//Subtitles
			'audio/mpeg',							//MP3 MPEG
			'audio/x-ms-wma',						//Windows Media Audio
			'audio/vnd.rn-realaudio',				//RealAudio
			'audio/x-wav',							//WAV
			'image/gif',							//GIF 
			'image/jpeg',							//JPEG
			'image/png',							//PNG
			'image/tiff',							//TIFF
			'image/svg+xml',						//SVG
			'image/bmp',							//BMP
			'image/vnd.microsoft.icon',				//ICO 
			'video/mpeg',							//MPEG-1
			'video/mp4',							//MP4
			'video/quicktime',						//QuickTime
			'video/x-ms-wmv',						//Windows Media Video
			'video/x-msvideo',						//AVI
			'video/x-flv'							//Flash Video
		
		),
		// unused
		'allowed_file'			=> array(
			'application/pdf',
			'image/vnd.adobe.photoshop',
			'application/postscript',
			'application/msword',
			'application/rtf',
			'application/vnd.ms-excel',
			'application/vnd.ms-powerpoint',
			'application/vnd.oasis.opendocument.text',
			'application/vnd.oasis.opendocument.spreadsheet',
			'text/xml',
		    'text/csv',
			'text/plain',
			'text/richtext',
			'text/rtf'
		)
	)
);
?>
<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of Clearbricks.
#
# Copyright (c) 2003-2008 Olivier Meunier and contributors
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK ------------------------------------

/**
* HTTP utilities
*
* @package Clearbricks
* @subpackage Common
*/
class http
{
	/** @var boolean	Force HTTPS scheme on server port 443 in {@link getHost()} */
	public static $https_scheme_on_443 = false;
	
	/** @var integer	Cache max age for {@link cache()} */
	public static $cache_max_age = 0;
	
	/**
	* Self root URI
	*
	* Returns current scheme, host and port.
	*
	* @return string
	*/
	public static function getHost()
	{
		$server_name = explode(':',$_SERVER['HTTP_HOST']);
		$server_name = $server_name[0];
		if (self::$https_scheme_on_443 && $_SERVER['SERVER_PORT'] == '443')
		{
			$scheme = 'https';
			$port = '';
		}
		elseif (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
		{
			$scheme = 'https';
			$port = ($_SERVER['SERVER_PORT'] != '443') ? ':'.$_SERVER['SERVER_PORT'] : '';
		}
		else
		{
			$scheme = 'http';
			$port = ($_SERVER['SERVER_PORT'] != '80') ? ':'.$_SERVER['SERVER_PORT'] : '';
		}
		
		return $scheme.'://'.$server_name.$port;
	}
	
	/**
	* Self URI
	*
	* Returns current URI with full hostname.
	*
	* @return string
	*/
	public static function getSelfURI()
	{
		return self::getHost().$_SERVER['REQUEST_URI'];
	}
	
	/**
	* Redirect
	*
	* Performs a conforming HTTP redirect for a relative URL.
	*
	* @param string	$page		Relative URL
	*/
	public static function redirect($page)
	{
		if (preg_match('%^http[s]?://%',$page))
		{
			$redir = $page;
		}
		else
		{
			$host = self::getHost();
			$dir = dirname($_SERVER['PHP_SELF']);
			
			if (substr($page,0,1) == '/') {
				$redir = $host.$page;
			} else {
				if (substr($dir,-1) == '/') {
					$dir =  substr($dir,0,-1);
				}
				$redir = $host.$dir.'/'.$page;
			}
		}
		
		# Close session if exists
		if (session_id()) {
			session_write_close();
		}
		
		header('Location: '.$redir);
		exit;
	}
	
	/**
	* Concat URL and path
	*
	* Appends a path to a given URL. If path begins with "/" it will replace the
	* original URL path.
	*
	* @param string	$url		URL
	* @param string	$path	Path to append
	* @return string
	*/
	public static function concatURL($url,$path)
	{
		if (substr($path,0,1) != '/') {
			return $url.$path;
		}
		
		return preg_replace('#^(.+?//.+?)/(.*)$#','$1'.$path,$url);
	}
	
	/**
	* Real IP
	*
	* Returns the real client IP (or tries to do its best).
	*
	* @return string
	*/
	public static function realIP()
	{
		return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
	}
	
	/**
	* Client unique ID
	*
	* Returns a "almost" safe client unique ID.
	*
	* @param string	$key		HMAC key
	* @return string
	*/
	public static function browserUID($key)
	{
		$uid  = '';
		$uid .= isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$uid .= isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';
		$uid .= isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';
		$uid .= isset($_SERVER['HTTP_ACCEPT_CHARSET']) ? $_SERVER['HTTP_ACCEPT_CHARSET'] : '';
		
		return crypt::hmac($key,$uid);
	}
	
	/**
	* Client language
	*
	* Returns a two letters language code take from HTTP_ACCEPT_LANGUAGE.
	*
	* @return string
	*/
	public static function getAcceptLanguage()
	{
		$dlang = '';
		if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		{
			$acclang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$L = explode(';', $acclang[0]);
			$dlang = substr(trim($L[0]),0,2);
		}
		
		return $dlang;
	}
	
	/**
	* HTTP Cache
	*
	* Sends HTTP cache headers (304) according to a list of files and an optionnal.
	* list of timestamps.
	*
	* @param array		$files		Files on which check mtime
	* @param array		$mod_ts		List of timestamps
	*/
	public static function cache($files,$mod_ts=array())
	{
		if (empty($files) || !is_array($files)) {
			return;
		}
		
		array_walk($files,create_function('&$v','$v = filemtime($v);'));
		
		$array_ts = array_merge($mod_ts,$files);
		
		rsort($array_ts);
		$now = time();
		$ts = min($array_ts[0],$now);
		
		$since = null;
		if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			$since = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
			$since = preg_replace ('/^(.*)(Mon|Tue|Wed|Thu|Fri|Sat|Sun)(.*)(GMT)(.*)/', '$2$3 GMT', $since);
			$since = strtotime($since);
			$since = ($since <= $now) ? $since : null;
		}
		
		# Common headers list
		$headers[] = 'Last-Modified: '.gmdate('D, d M Y H:i:s',$ts).' GMT';
		$headers[] = 'Cache-Control: must-revalidate, max-age='.abs((integer) self::$cache_max_age);
		$headers[] = 'Pragma:';
		
		if ($since >= $ts)
		{
			self::head(304,'Not Modified');
			foreach ($headers as $v) {
				header($v);
			}
			exit;
		}
		else
		{
			header('Date: '.gmdate('D, d M Y H:i:s',$now).' GMT');
			foreach ($headers as $v) {
				header($v);
			}
		}
	}

	/**
	* HTTP Etag
	*
	* Sends HTTP cache headers (304) according to a list of etags in client request.
	*
	* @param string	$p_content	Response page content
	*/
	public static function etag()
	{
		# We create an etag from all arguments
		$args = func_get_args();
		if (empty($args)) {
			return;
		}
		
		$etag = '"'.md5(implode('',$args)).'"';
		unset($args);
		
		header('ETag: '.$etag);
		
		# Do we have a previously sent content?
		if (!empty($_SERVER['HTTP_IF_NONE_MATCH']))
		{
			foreach (explode(',',$_SERVER['HTTP_IF_NONE_MATCH']) as $i)
			{
				if (stripslashes(trim($i)) == $etag) {
					self::head(304,'Not Modified');
					exit;
				}
			}
		}
	}
	
	/**
	* HTTP Header
	*
	* Sends an HTTP code and message to client.
	*
	* @param string	$code		HTTP code
	* @param string	$msg			Message
	*/
	public static function head($code,$msg=null)
	{
		$status_mode = preg_match('/cgi/',PHP_SAPI);
		
		if (!$msg)
		{
			$msg_codes = array(
				100 => 'Continue',
				101 => 'Switching Protocols',
				200 => 'OK',
				201 => 'Created',
				202 => 'Accepted',
				203 => 'Non-Authoritative Information',
				204 => 'No Content',
				205 => 'Reset Content',
				206 => 'Partial Content',
				300 => 'Multiple Choices',
				301 => 'Moved Permanently',
				302 => 'Found',
				303 => 'See Other',
				304 => 'Not Modified',
				305 => 'Use Proxy',
				307 => 'Temporary Redirect',
				400 => 'Bad Request',
				401 => 'Unauthorized',
				402 => 'Payment Required',
				403 => 'Forbidden',
				404 => 'Not Found',
				405 => 'Method Not Allowed',
				406 => 'Not Acceptable',
				407 => 'Proxy Authentication Required',
				408 => 'Request Timeout',
				409 => 'Conflict',
				410 => 'Gone',
				411 => 'Length Required',
				412 => 'Precondition Failed',
				413 => 'Request Entity Too Large',
				414 => 'Request-URI Too Long',
				415 => 'Unsupported Media Type',
				416 => 'Requested Range Not Satisfiable',
				417 => 'Expectation Failed',
				500 => 'Internal Server Error',
				501 => 'Not Implemented',
				502 => 'Bad Gateway',
				503 => 'Service Unavailable',
				504 => 'Gateway Timeout',
				505 => 'HTTP Version Not Supported'
			);
			
			$msg = isset($msg_codes[$code]) ? $msg_codes[$code] : '-';
		}
		
		if ($status_mode) {
			header('Status: '.$code.' '.$msg);
		} else {
			if (version_compare(phpversion(),'4.3.0','>=')) {
				header($msg, true, $code);
			} else {
				header('HTTP/1.x '.$code.' '.$msg);
			}
		}
	}
	
	/**
	* Trim request
	*
	* Trims every value in GET, POST, REQUEST and COOKIE vars.
	* Removes magic quotes if magic_quote_gpc is on.
	*/
	public static function trimRequest()
	{
		if(!empty($_GET)) {
			array_walk($_GET,array('self','trimRequestHandler'));
		}
		if(!empty($_POST)) {
			array_walk($_POST,array('self','trimRequestHandler'));
		}
		if(!empty($_REQUEST)) {
			array_walk($_REQUEST,array('self','trimRequestHandler'));
		}
		if(!empty($_COOKIE)) {
			array_walk($_COOKIE,array('self','trimRequestHandler'));
		}
	}
	
	private static function trimRequestHandler(&$v,$key)
	{
		$v = self::trimRequestInVar($v);
	}
	
	private static function trimRequestInVar($value)
	{
		if (is_array($value))
		{
			$result = array();
			foreach ($value as $k => $v)
			{
				if (is_array($v)) {
					$result[$k] = self::trimRequestInVar($v);
				} else {
					if (get_magic_quotes_gpc()) {
						$v = stripslashes($v);
					}
					$result[$k] = trim($v);
				}
			}
			return $result;
		}
		else
		{
			if (get_magic_quotes_gpc()) {
				$value = stripslashes($value);
			}
			return trim($value);
		}
	}
	
	/**
	* Unset global variables
	*
	* If register_globals is on, removes every GET, POST, COOKIE, REQUEST, SERVER,
	* ENV, FILES vars from GLOBALS.
	*/
	public static function unsetGlobals()
	{
		if (!ini_get('register_globals')) {
			return;
		}
		
		if (isset($_REQUEST['GLOBALS'])) {
			throw new Exception('GLOBALS overwrite attempt detected');
		}
		
		# Variables that shouldn't be unset
		$no_unset = array('GLOBALS','_GET','_POST','_COOKIE','_REQUEST',
		'_SERVER','_ENV','_FILES');
		
		$input = array_merge($_GET,$_POST,$_COOKIE,$_SERVER,$_ENV,$_FILES,
				(isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array()));
		
		foreach ($input as $k => $v) { 
			if (!in_array($k,$no_unset) && isset($GLOBALS[$k]) ) {
				$GLOBALS[$k] = null;
				unset($GLOBALS[$k]);
			}
		}
	}
}
?>
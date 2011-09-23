<?php
/**
 * Session class
 * TODO Session class documentation.
 * 
 * @author J�r�me Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class Session
{
	public function __construct()
	{
		if (!isset($_SESSION[SESSION_NAMESPACE])) $_SESSION[SESSION_NAMESPACE] = array();
	}
	
	public static function hasAttribute($name)
	{
		return isset($_SESSION[SESSION_NAMESPACE][$name]);
	}
	
	public static function getAttribute($name)
	{
		return $_SESSION[SESSION_NAMESPACE][$name];
	}
	
	public static function setAttribute($name, $value)
	{
		$_SESSION[SESSION_NAMESPACE][$name] = $value;
	}
	
	public static function removeAttribute($name)
	{
		if(isset($_SESSION[SESSION_NAMESPACE][$name])){
			
			unset($_SESSION[SESSION_NAMESPACE][$name]);
		}
	}
	
	public static function getAttributeNames()
	{
		return array_keys($_SESSION[SESSION_NAMESPACE]);
	}
	
	public static function clear($global)
	{
		if ($global)
			session_unset();
		else
			$_SESSION[SESSION_NAMESPACE] = array();
	}
}
?>
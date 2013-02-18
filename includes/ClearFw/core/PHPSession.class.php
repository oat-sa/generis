<?php
/**
 * Session class
 * TODO Session class documentation.
 * 
 * @author J�r�me Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class PHPSession
{
	private static $instance = null;
	
	public static function singleton() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct()
	{
		if (!isset($_SESSION[SESSION_NAMESPACE])) $_SESSION[SESSION_NAMESPACE] = array();
	}
	
	public function hasAttribute($name)
	{
		return isset($_SESSION[SESSION_NAMESPACE][$name]);
	}
	
	public function getAttribute($name)
	{
		return $_SESSION[SESSION_NAMESPACE][$name];
	}
	
	public function setAttribute($name, $value)
	{
		$_SESSION[SESSION_NAMESPACE][$name] = $value;
	}
	
	public function removeAttribute($name)
	{
		if (isset($_SESSION[SESSION_NAMESPACE][$name])){
			unset($_SESSION[SESSION_NAMESPACE][$name]);
		}
	}
	
	public function getAttributeNames()
	{
		return array_keys($_SESSION[SESSION_NAMESPACE]);
	}
	
	public function clear($global)
	{
		if ($global)
			session_unset();
		else
			$_SESSION[SESSION_NAMESPACE] = array();
	}
}
?>
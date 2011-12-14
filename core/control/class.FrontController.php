<?php
if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

//if (!(isset($_SESSION))) {session_start();}

/**
 * Manage session with generis api using PHP Session. Using this facade is really
 * helpful for developpers if the connection to Generis has to remain open through
 * multiple HTTP requests.
 */
class core_control_FrontController
{
	/**
	 * Allows you to connect to Generis.
	 * 
	 * @param string $login The login identifier of the user.
	 * @param string $password The MD5 hash of the user's password.
	 */
	static function connect($login = '', $password = '',$module = '')
	{
		$status =false;
		if (!empty($login) && !empty($password) && !empty($module)){

			
			$apiModelOo = core_kernel_impl_ApiModelOO::singleton();
			if($apiModelOo->logIn($login, $password, $module, CLASS_ROLE_TAOMANAGER)){
				$session = core_kernel_classes_Session::singleton();
				$_SESSION["generis_session"] = $session;
				
				$status =true;	
			}
		}
		else{
			if (self::isConnected()) {
				$status =true;
				core_kernel_classes_Session::reset($_SESSION["generis_session"]);
				core_kernel_classes_DbWrapper::singleton();
			}
			return $status;
		}
	}
		
	/**
	 * Allows you to know if the last logged in user using the connect method is still
	 * considered as logged or not.
	 * 
	 * @return boolean Returns true if still connected, false if not.
	 */ 
	static function isConnected()
	{
		return (isset($_SESSION["generis_session"]));
	}


	/**
	 * logs you off Generis4.
	 */
	static function logOff()
	{
		// API logoff.
		$api = core_kernel_impl_ApiModelOO::singleton();
		unset($api);
		
		// Database logoff.
		$dbWrapper = core_kernel_impl_ApiModelOO::singleton(core_kernel_classes_DbWrapper::singleton());
		unset($dbWrapper);	
	}
}
?>
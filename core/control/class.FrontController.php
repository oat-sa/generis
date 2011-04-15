<?php
if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

if (!(isset($_SESSION))) {session_start();}



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
	static function connect($login = "", $password = "",$module = "")
	{
		$status =false;
		
		if (($login != "") and ($password != "") and ($module != ""))	
		{

				$status =true;	
				
				$apiModelOo = core_kernel_impl_ApiModelOO::singleton();
				$apiModelOo->logIn($login,$password,$module, CLASS_ROLE_TAOMANAGER);
				
				$_SESSION["generis_session"] = $apiModelOo->session;
				$_SESSION["generis_module"] = $module;
		
		}
		else	
		{

			if (self::isConnected()) {
					$status =true;
					core_kernel_classes_Session::reset($_SESSION["generis_session"]);
					core_kernel_impl_ApiModelOO::singleton()->session = $_SESSION["generis_session"];
					core_kernel_classes_DbWrapper::singleton($_SESSION["generis_module"]);
					core_kernel_classes_Session::singleton()->model->con = core_kernel_classes_DbWrapper::singleton()->dbConnector;
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
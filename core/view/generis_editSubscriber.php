<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* Edit a subscriber
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");	   
require_once("generis_utils.php");
class TAOEditSubscriber
{
	function TAOEditSubscriber()
	{
	}
	
	function getOutput($ressource)
	{
		
		
		$output="";
		
		foreach ($ressource["pass1"] as $key=>$val)
		{
		if ($val=="CRYPTED")
			{$pass=$key;} else {
			$pass=md5($val);}
		}
// ancienne version
//editSubscriber($_SESSION["session"],array($ressource["id"]), array($ressource["login"]),array($pass),array("1"));
// appel webservices
calltoKernel('editSubscriber',array($_SESSION["session"],array($ressource["id"]), array($ressource["login"]),array($pass),array("1")));

// ancienne version
//affiliateSubscriberGroup($_SESSION["session"],array($ressource["id"]),array($ressource["group"]));
// appel webservices
calltoKernel('affiliateSubscriberGroup',array($_SESSION["session"],array($ressource["id"]),array($ressource["group"])));

		//echo $pass;
		/*editUser($_SESSION["session"],array($ressource["login"]),array($pass),array($ressource["umask"]),array($ressource["isadmin"]),array($ressource["lastname"]),array($ressource["firstname"]),array($ressource["email"]),array($ressource["company"]),array($ressource["deflg"]),array(1),array($ressource["group"]));
		return $output;*/
		
	
	}
	   
	
}
?>
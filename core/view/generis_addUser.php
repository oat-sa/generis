<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* Add a user in knowledge base
* @author patrick
* @package usergui
*/
//require_once("GUI_constants.php");		   
//require_once("functions.php");
class TAOaddUser
{
	function TAOaddUser()
	{
	}
	
	function getOutput($ressource)
	{
		
		
		$output="";
		if (isset($ressource["myrights"])) {$mask=$ressource["myrights"];} else {$mask="00";}
		if (isset($ressource["mygroup"])) {$mask.=$ressource["mygroup"];} else {$mask.="00";}
		if (isset($ressource["other"])) {$mask.=$ressource["other"];} else {$mask.="00";}
		if (isset($ressource["selectedgroup"]))
		{
			if (strlen($ressource["selectedgroup"])==1) {$mask.="00".$ressource["selectedgroup"];}
			if (strlen($ressource["selectedgroup"])==2) {$mask.="0".$ressource["selectedgroup"];}
			if (strlen($ressource["selectedgroup"])==3) {$mask.=$ressource["selectedgroup"];}
		}
		else {$mask.="001";}
	if (isset($ressource["subscribers"])) {$mask.=$ressource["subscribers"];} else {$mask.="0";}
	calltoKernel('addUser',array($_SESSION["session"],array($ressource["login"]),array(md5($ressource["pass1"])),array($mask),array($ressource["isadmin"]),array($ressource["lastname"]),array($ressource["firstname"]),array($ressource["email"]),array($ressource["company"]),array($ressource["deflg"]),array(1),array($ressource["group"])));
		//If usercreated is not translated in the current language don^'t reaise any error
		error_reporting(0);
		$_SESSION["msg"]=USERCREATED;
		return $output;
		
	
	}
	   
	
}
?>
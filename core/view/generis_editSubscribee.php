<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* Edit a subscribee
* @author patrick
* @package usergui
*/
//require_once("GUI_constants.php");		   
//require_once("functions.php");
class TAOEditSubscribee
{
	function TAOEditSubscribee()
	{
	}
	
	function getOutput($ressource)
	{
		
		
		$output="";
		//print_r($ressource);
		foreach ($ressource["pass1"] as $key=>$val)
		{
		if ($val=="CRYPTED")
			{$pass=$key;} else {$pass=md5($val);}
		}
		//echo $pass;
	//editSubscribee($session,$idsubscribee,$url,$login,$password,$type,$md);
	
	
	
	// ancienne version
	//editSubscribee($_SESSION["session"],array($ressource["Idsub"]),array($ressource["url"]), 	array($ressource["login"]),array($pass),array($ressource["type"]),array($ressource["dataBaseName"]));
	// appel webservices
	calltoKernel('editSubscribee',array($_SESSION["session"],array($ressource["Idsub"]),array($ressource["url"]), 	array($ressource["login"]),array($pass),array($ressource["type"]),array($ressource["dataBaseName"])));

		return $output;
		
	
	}
	   
	
}
?>
<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* Add a subscibee implementation
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");	   
require_once("generis_utils.php");
class TAOAddSubscribee
{
	function TAOAddSubscribee()
	{
	}
	
	function getOutput($ressource)
	{
		
		
		$output="";
		
		foreach ($ressource["pass1"] as $key=>$val)
		{
		if ($val=="CRYPTED")
			{$pass=$key;} else {$pass=md5($val);}
		}
		//echo $pass;
	//editSubscribee($session,$idsubscribee,$url,$login,$password,$type,$md);
	
	

	// ancienne version
	//addSubscribee($_SESSION["session"],array($ressource["url"]), 	array($ressource["login"]),array($pass),array($ressource["type"]),array($ressource["dataBaseName"]));
	// appel webservices
	/*
	switch ($ressource["type"])
		{
		
		
		}
	*/

	$result = calltoKernel('addSubscribee',array($_SESSION["session"],array($ressource["url"]), 	array($ressource["login"]),array($pass),array($ressource["type"]),array($ressource["dataBaseName"])));

		return $result;
		
	
	}
	   
	
}
?>
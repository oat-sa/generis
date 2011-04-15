<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* Add a subscriber in kbnowledge base
* @author patrick
* @package usergui
*/
//require_once("GUI_constants.php");		   
//require_once("generis_utils.php");
class TAOAddSubscriber
{
	function TAOAddSubscriber()
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
	
	//addSubscriber($login,$password,$enabled)

	// ancienne version
	//$x = addSubscriber($_SESSION["session"],array($ressource["login"]),array($pass),array("1"));
	// appel webservices
	$x = calltoKernel('addSubscriber',array($_SESSION["session"],array($ressource["login"]),array($pass),array("1")));
	
	// ancienne version
	//affiliateSubscriberGroup($_SESSION["session"],array($x["pOKorKO"]),array($ressource["group"]));
	// appel webservices
	calltoKernel('affiliateSubscriberGroup',array($_SESSION["session"],array($x["pOKorKO"]),array($ressource["group"])));
		return $output;
			
	}
		
}
?>
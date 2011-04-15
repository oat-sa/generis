<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* Removes a subscribers group
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");		   
require_once("generis_utils.php");
class TAOremovesubscribersgroup
{
	function TAOremovesubscribersgroup()
	{
	}
	
	function getOutput($ressource)
	{
		
		
		$output="";
		
		// ancienne version
		//removeSubscriberGroup($_SESSION["session"],array($ressource));	
		// appel webservices
		calltoKernel('removeSubscriberGroup',array($_SESSION["session"],array($ressource)));
	

		return $output;
		
	
	}
	   
	
}
?>
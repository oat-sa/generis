<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* Removes a subscriber
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");		   
require_once("generis_utils.php");
class TAOremoveSubscriber
{
	function TAOremoveSubscriber()
	{
	}
	
	function getOutput($ressource)
	{
		
		
		$output="";
		
		// ancienne version
		//removeSubscriber($_SESSION["session"],array($ressource));	
		// appel webservices
		calltoKernel('removeSubscriber',array($_SESSION["session"],array($ressource)));
	

		return $output;
		
	
	}
	   
	
}
?>
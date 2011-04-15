<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* Removes a subscribee
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");   
require_once("generis_utils.php");
class TAOremoveSubscribee
{
	function TAOremoveSubscribee()
	{
	}
	
	function getOutput($ressource)
	{
		
		
		$output="";
		
		//removeSubscribee($_SESSION["session"],array($ressource));	
		calltoKernel('removeSubscribee',array($_SESSION["session"],array($ressource)));
	

		return $output;
		
	
	}
	   
	
}
?>
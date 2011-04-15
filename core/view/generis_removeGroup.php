<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* Removes a group
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");	   
require_once("generis_utils.php");
class TAOremovegroup
{
	function TAOremovegroup()
	{
	}
	
	function getOutput($ressource)
	{
		
		
		$output="";
		
		// ancienne version
		//removeGroup($_SESSION["session"],array($ressource));	
		// appel webservices
		calltoKernel('removeGroup',array($_SESSION["session"],array($ressource)));
	
		return $output;
		
	
	}
	   
	
}
?>
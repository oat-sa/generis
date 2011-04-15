<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* Removes an user
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");		   
require_once("generis_utils.php");
class TAOremoveUser
{
	function TAOremoveUser()
	{
	}
	
	function getOutput($ressource)
	{
		
		
		$output="";
		
	// ancienne version
	//removeUser($_SESSION["session"],array($ressource));	
	// appel webservices
	calltoKernel('removeUser',array($_SESSION["session"],array($ressource)));
	

		return $output;
		
	
	}
	   
	
}
?>
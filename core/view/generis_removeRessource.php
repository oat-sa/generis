<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* Removes a resoruce in knowledge base
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");		   
require_once("generis_utils.php");
class TAOremoveRessource
{
	function TAOremoveRessource()
	{
	}
	
	function getOutput($ressource)
	{
		
		$ressource= base64_decode($ressource);
		
		
		if (!(isset($_SESSION["param2"])))
		{
			$result = calltoKernel('removeSubject',array($_SESSION["session"],$ressource));
		}
		else
		{	
			$result =  calltoKernel('removeStatement',array($_SESSION["session"],$_SESSION["param2"]));
		}
		
		$_SESSION["msg"]="Ressource ".$ressource." successfully removed !";
		$output="";
		return $output;
	
	}
	   
	 
	 
	


}
?>
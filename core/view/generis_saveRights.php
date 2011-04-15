<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* Edit rights on a resource
* @author patrick
* @package usergui
*/
//require_once("GUI_constants.php");		   
//require_once("functions.php");
class TAOsaveRights
{
	function TAOsaveRights()
	{
	}
	
	function getOutput($ressource)
	{
		
		$type=substr($ressource["ressource"],0,1);
		$shorttaordfId=substr($ressource["ressource"],1);
		$output="";
		
		if (isset($ressource["myrights"])) {$mask=$ressource["myrights"];} else {$mask="00";}
		if (isset($ressource["mygroup"])) {$mask.=$ressource["mygroup"];} else {$mask.="00";}
		if (isset($ressource["other"])) {$mask.=$ressource["other"];} else {$mask.="00";}
			switch ($type)
			{
			case "c": {
			$return = calltoKernel('editMaskofClass',array($_SESSION["session"],array($shorttaordfId),array($mask)));
			break;}
			
			case "p": {
			
			$return = calltoKernel('editLocalRightsProperty',array($_SESSION["session"],array($shorttaordfId),array($mask)));
			
			break;}
			
			case "i": {
			$return = calltoKernel('editLocalRightsInstance',array($_SESSION["session"],array($shorttaordfId),array($mask)));
			break;}
		}
		
		if (isset($ressource["selectedsubscriber"]))
		{	
			foreach ($ressource["selectedsubscriber"] as $key=>$val)
			{
				switch ($type)
				{
				case "c": {
				$return = calltoKernel('editGroupsRightsClass',array($_SESSION["session"],array($shorttaordfId),array($key),array($val)));
				
				break;}
				
				case "p": {
				
				$return = calltoKernel('editGroupsRightsProperty',array($_SESSION["session"],array($shorttaordfId),array($key),array($val)));
				break;}
				
				case "i": {
				$return = calltoKernel('editGroupsRightsInstance',array($_SESSION["session"],array($shorttaordfId),array($key),array($val)));
				break;}
				}

			}
			
		}
		
		
		
	
	

		
				


		$_SESSION["msg"]=USEREDITED;
		
		return $output;
		
	
	}
	   
	
}
?>
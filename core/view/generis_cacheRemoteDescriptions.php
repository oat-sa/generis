<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* functions that allow retrival of resources described elsewhere
* 
* @author patrick
* @package usergui
*/

	
/**
* retrieves rdfs file where $url is described, session is updated, checks if no connection was already established with the module 
* (thanks to $_SESSION["connectedModules"])
*
*/

function retrieveRDFSof($url)
	{
		$IndurlModule = substr($url,0,strpos($url,".rdf#"));
		
		foreach ($_SESSION["connectedModules"] as $key=>$val)
		{
			if ($val==$IndurlModule.".rdf") {return $val;}

		}
		
		if (isset($_SESSION["modulesinerror"][$IndurlModule])) {return "Error 00 : connection to module failed";}
		
		$isubscribees = calltoKernel('getSubscribeesurl',array($_SESSION["session"],array("any"),array($IndurlModule.".rdf#")));
		
		$result = calltoKernel('getRDFfromaremotemodule',array($_SESSION["session"],array($_SESSION["datalg"]),array($isubscribees[0][0])));
		if ($result=="Error 00 : connection to module failed") {
			$_SESSION["modulesinerror"][$IndurlModule]=true;
			return $result;}
		
		$_SESSION["session"]=array($result["pSession"][0]);
		
		$_SESSION["connectedModules"][]=$result["pSession"][1];
		
		return $result["pSession"][1];

	}

	/**
	* Returns label and comment of an url
	* 
	* This function calls retrieveRDFSof which retrieves the rdfs if necessary from the remote module where $url is described,
	*/
	
function getlabelandcommentofurl($url)
	{
		set_time_limit(120);
		if ($_SESSION["SHOWCLEAR"])
		{
		
		$ns = retrieveRDFSof($url);
		if ($ns=="Error 00 : connection to module failed") {return array($url,$url);}
		
		if (isset($_SESSION["CACHE_RESSOURCE"][$url]))
			{
				return $_SESSION["CACHE_RESSOURCE"][$url];
				
			}
			else
			{
				
				
				$labelComment = calltoKernel('getLabelComment',array($_SESSION["session"],$url,array("")));
								
				$_SESSION["CACHE_RESSOURCE"][$url]=array($labelComment["label"],$labelComment["comment"]);
				return $_SESSION["CACHE_RESSOURCE"][$url];
			}
		}
		else
		{
			return array($url,$url);
		}
	}
  
	 
	 
	



?>
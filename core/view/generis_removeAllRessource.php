<?php
/**
* Removes all instances of a class in knowledge base
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");		   
require_once("generis_utils.php");
class generisremoveallRessource
{
	function generisremoveallRessource()
	{
	}
	function getOutput($ressource)
	{
		set_time_limit(600);
		$instances = calltoKernel('getInstances',array($_SESSION["session"],array(base64_decode($ressource)),array("")));
		foreach ($instances["pDescription"][0] as $key=>$val)
		{
			$result = calltoKernel('removeSubject',array($_SESSION["session"],$key));
		}
		
		$_SESSION["msg"]="Ressource ".$ressource." successfully removed !";
		return "";
	
	}
}
?>
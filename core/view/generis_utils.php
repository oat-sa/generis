<?php

/**
* static functions used by user gui
* @author patrick
* @package usergui
*/
	
	error_reporting(0);
	//ini_set("display_errors","1");
	ini_set("allow_call_time_pass_reference","On");
	
	include("generis_graphicsTools.php");
	
	define("DISTRIBUTED_KERNEL_ACTIVATED",false,true);
	define("KERNEL_IP", "10.13.1.151", true);
	
	

	require_once(dirname (__FILE__)."/soap/nusoap.php");

	function timeStamp() 
	{ 
    list($usec, $sec) = explode(" ", microtime()); 
    return ((float)$usec + (float)$sec); 
	} 
	
	function calltoKernel($service,$params)
	{
			
		if (DISTRIBUTED_KERNEL_ACTIVATED)

		{
		
		$client = new nusoapclient("http://".KERNEL_IP."/middleware/serverModule.php");
		
		$client->debug_flag=false;
		//print_r($client);
		if($err = $client->getError())
			{return "<b>soap client error1: $err</b><br>";}
			else{
				
				$result =$client->call($service,$params);}
		if ($service=="GetClassPath")
			{;}
		if($err = $client->getError())
			{return "<b>soap client error1: $err</b><br>";}
		

		return $result;

		}
		else 
			
		{

			require_once(dirname (__FILE__)."/../api/generisApiPhp.php");
				
			//require_once("servermodule.php");
			/*Returned results of functions called directly have a different structure than distrubuted called one (Some nusoap bug ?)*/
			error_Reporting(E_ALL);
			$result =  call_user_func_array( $service ,$params);
			
			switch ($service)
			{
				case "getAllClasses":
					$result = array("pDescription" => array (0=>$result["pDescription"]));
					break;
				
				case "getClassDescription":
					
				$result["pDescription"]["PropertiesValues"] = array($result["pDescription"]["PropertiesValues"]);
				//$result = array("pDescription" => array (0=>$result["pDescription"]));
					break;

				case "getInstanceDescription":
					
				$result["pDescription"]["PropertiesValues"] = array($result["pDescription"]["PropertiesValues"]);
				//$result = array("pDescription" => array (0=>$result["pDescription"]));
					break;
				
				case "getInstances":
					
				$result = array("pDescription" => array (0=>$result["pDescription"]));
				//$result = array("pDescription" => array (0=>$result["pDescription"]));
					break;


					
			}
			return $result;
		}
	}
	
	
	function IntegerToArray($num)
	{
	$split_string = strval($num);
	$return_array = array();
	$len = strlen($split_string);
	for ( $i=0; $i<$len; $i++)
	{
	$return_array[] = $split_string{$i};
	}
	return $return_array;
	}

	function modifyType($onetype)
		{
		if (!((strpos($onetype,"~"))===FALSE))
			{
		return substr($onetype,strpos($onetype,"~")+3);
			}
			else {return substr($onetype,2);}
		}

	function groupByPropertyKey($array)
		{
		$grouped=Array();
		foreach ($array as $key=>$val)
			{	
				$prec=array();
				
				if (isset($grouped[$val["PropertyKey"]]["PropertyValue"]))
				{
				$prec = $grouped[$val["PropertyKey"]]["PropertyValue"];
				}
				
				if (isset($val["PropertyValue"]))
				{	if ($val["TripleID"]!="0") $prec["tripleid".$val["TripleID"]] = $val["PropertyValue"];}
				
				
				
				$grouped[$val["PropertyKey"]]=
					array(
					"PropertyLabel" => $val["PropertyLabel"],
					"PropertyComment" => $val["PropertyComment"],
					"PropertyValue" => $prec,
					"PropertyWidget" =>	$val["widget"],
					"PropertyRange" => $val["range"],
					);
			}
		
		return $grouped;
		}
	  
	 function containsValue($string,$grouped)
	 {		$contains=false;

			
				if (is_array($grouped[2])) {
				foreach ($grouped[2] as $keym=>$valm)
					{
						
						if ($valm==$string) {$contains=true;}
					}
				}
				
				
			
			return $contains;
	 }
	
	function getOverDivLink($link,$label,$comment)
	 {		
		
			 $label=trim($label);
			  $comment=trim($comment);
			  $label=str_replace('"','',$label);
			  $comment=str_replace('"','',$comment);
			 
		
		return "<a href=".$link." onmouseover=\"return overlib('<div class=opaque><div class=calprio5>".str_replace("'","\'",$label)."</div><div class=box-data>".str_replace("'","\'",$comment)."</div></div>',FULLHTML);\"  onmouseout=\"return nd();\">".$label."</a>";
	 }

	 function getDiscussionLink($ressource)
	 {
		
		 $authordescription = calltoKernel('getAuthor',array($_SESSION["session"],$ressource));
		
		if ($authordescription[0]=="") {$authordescription[0]="generis";$authordescription[1]="generis@tao.lu";} 
		/*$authordescription[0] = $ressource;
		$authordescription[7] ='unknown';*/
		return '<a href="mailto:'.$authordescription[1].'?subject=Description de la ressource '.$ressource.' &body=Description de la ressource '.$ressource.'">'.AUTHOR." ".$authordescription[0].'</a>';
			
	 }

	
	 
		#######################GUI language management##########################
		
		function loadGUIlanguage($lg="")
		{
			
			if ($lg=="") {$url = "./lg/".$_SESSION["guilg"].".php";}
			else {$url = $lg;}
			
			include($url);
			

		}

		function getlgfiles()
		{
			$d = dir("./lg");
			while (false !== ($entry = $d->read())) {
			if (strlen($entry)==6) {$result[]=substr($entry,0,2);}
			}
			$d->close();

			return $result;
		}
		function isValidURI($uri)
		{
			
			if( preg_match( '/^(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}'
				   .'((:[0-9]{1,5})?\/.*)?$/i' ,$uri))
			{
			  return true;
			}
			else
			{
			  if (strpos($uri,"#")===0) {return true;} else {
			  return false;}
			}
		}



?>

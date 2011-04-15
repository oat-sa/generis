<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* Returns last edited xml of a resource 
* @package Widgets.etesting.authoringItem
* @author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 1.1
*/
if (!(isset($_SESSION))) {session_start();}
//print_r($_SESSION);
$xml=  $_SESSION["ITEMpreview"];
$xml =ereg_replace("--MULTIMEDIA[^-]*--" , "" , $xml ) ;
		$xml =ereg_replace("--TEXTBOX[^-]*--" , "" , $xml ) ;
		$xml=str_replace("&#180;","'",$xml);
		
			
		$xml=str_replace("&lt;font size=&quot;1&quot;&gt;","&lt;font size=&quot;8&quot;&gt;",$xml);
		$xml=str_replace("&lt;font size=&quot;2&quot;&gt;","&lt;font size=&quot;12&quot;&gt;",$xml);
		$xml=str_replace("&lt;font size=&quot;3&quot;&gt;","&lt;font size=&quot;16&quot;&gt;",$xml);
		$xml=str_replace("&lt;font size=&quot;4&quot;&gt;","&lt;font size=&quot;18&quot;&gt;",$xml);
		$xml=str_replace("&lt;font size=&quot;5&quot;&gt;","&lt;font size=&quot;24&quot;&gt;",$xml);
		$xml=str_replace("&lt;font size=&quot;6&quot;&gt;","&lt;font size=&quot;28&quot;&gt;",$xml);
		$xml=str_replace("&lt;font size=&quot;7&quot;&gt;","&lt;font size=&quot;32&quot;&gt;",$xml);
		$xml=str_replace("127.0.0.1",$_SERVER["HTTP_HOST"],$xml);
		$xml=str_replace("localhost",$_SERVER["HTTP_HOST"],$xml);
		//$xml =str_replace("<br />" , "" , $xml ) ;
		echo $xml;
?>
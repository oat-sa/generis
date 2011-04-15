<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* User interfaces to add a subscriebr in knowledge base
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");
include("generis_utils.php");
//include("serverModule.php");	
/*TO DO : Class as others*/
		
		if (!(isset($_SESSION))) {session_start();}
		loadGUIlanguage();
		$output=HEAD."<body class=paneIframe>";
		
		$output.="<BR>".TABLEHEADER."<tr><td rowspan=100 width=14%></td></tr>";
	
			
			// ancienne version
			//$result = getSubscriberDescription($_SESSION["session"],array(1));
			// appel webservices
			$result = calltoKernel('getSubscriberDescription',array($_SESSION["session"],array(1)));

			
			$user = $result["pOKorKO"]["description"];
			
			$groups=$result["pOKorKO"]["groups"];
			
			
			
			$output.='<center><FORM action=./index.php?showuser='.$user["0"].' name=edituser target=_top method=post>';
			$output.='<tr><td colspan=3></td></tr>';
			$output.='<tr><td colspan=3><div class="Title">'.SUBSCRIBERDESCRIPTION.'</div></td></tr>';
			$output.='<tr><td colspan=3></td></tr>';

			$output.='<tr><td><input type=hidden name=editanuser[id] value='.$user[0].'><div class="AUTHINFOS">'.LOGIN.'</div></td><td><input type=text name=editanuser[login] value='.$user[1].'></td></tr>';
			
			$output.='<tr><td><div class="AUTHINFOS">'.PASSWORD.'</div></td><td><input type=text name=editanuser[pass1]['.$user[1].'] value=CRYPTED></td><input type=hidden name=editanuser[group] value='.$_GET["addsubscriber"].'></tr>';



			$AddSubscriber=getButtonimage(APPLY);
			
			$output.='<tr><td colspan=3><center><input type=image src='.$AddSubscriber.' name=AddSubscriber value=Apply>&nbsp;&nbsp;&nbsp;';
			$output.="</form>";

			

		

		$output.=TABLEFOOTER;
	
		echo $output;
?>
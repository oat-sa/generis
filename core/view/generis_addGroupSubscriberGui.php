<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* User gui to add a group of subscriber
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
	
			
			$output.='<center><FORM action=./index.php name=edituser target=_top method=post>';
			$output.='<tr><td colspan=3></td></tr>';
			$output.='<tr><td colspan=3><div class="Title">'.ADDSUBSCRIBERGROUPDESCRIPTION.'</div></td></tr>';
			$output.='<tr><td colspan=3></td></tr>';

			$output.='<tr><td><div class="AUTHINFOS">'.NAME.'</div></td><td><input type=text name=editanuser[login] value=""></td></tr>';
			
			$output.='<input type=hidden name=editanuser[group] value='.$_GET["addgroupsubscriber"].'>';



			$AddSubscriber=getButtonimage(APPLY);
			
			$output.='<tr><td colspan=3><center><input type=image src='.$AddSubscriber.' name=AddGroupSubscriber value=Apply>&nbsp;&nbsp;&nbsp;';
			$output.="</form>";

			

		

		$output.=TABLEFOOTER;
	
		echo $output;
?>
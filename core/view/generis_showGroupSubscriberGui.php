<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* User interface to edit group of subscriber
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");		   

class TAOshowGroupsubscriberGUI
{
	function TAOshowGroupsubscriberGUI()
	{
	}
	function getOutput($ressource)
	{
	$output="";
	$output.="<BR>".TABLEHEADER."<tr><td rowspan=100 width=14%></td></tr>";
		$output.='<tr><td colspan=3><div class="Title">Subscriber Group : '.$ressource.'</div></td></tr><tr><tr><td colspan=3><br><br><br><br></td></tr>';
		
$addasubscriber=getButtonimage(ADDSUBSCRIBERWITHIN.$ressource);
$addagroupsubscriber=getButtonimage(ADDSUBGROUPSUBSCRIBER.$ressource,true);
$removeasubscribersgroupp=getButtonimage(REMOVE,true);
		$output.='<td><FORM action=./generis_addSubscriberGui.php method=get>
			<input type=hidden name=addsubscriber value='.$ressource.'>
			<input type=image src='.$addasubscriber.' name=addasubscriber value="'.ADDSUBSCRIBERWITHIN.$ressource.'">
		</form></td>';

		$output.='<td><FORM action=./generis_addGroupSubscriberGui.php method=get>
			<input type=hidden name=addgroupsubscriber value='.$ressource.'>
			<input type=image src='.$addagroupsubscriber.' name=addagroupsubscriber value="'.ADDSUBGROUPSUBSCRIBER.$ressource.'">
		</form></td>';
		
		$output.='<td><FORM action=./index.php name=edituser target=_top method=post>
			<input type=hidden name=removesubscribersgroup value='.$ressource.'>
			<input type=image src='.$removeasubscribersgroupp.' name=removeasubscribersgroupp value="Remove Group '.$ressource.'">
		</form></td></tr>';
$output.=TABLEFOOTER;
		return $output;
	
	}
	   
	 
	 
	


}
?>
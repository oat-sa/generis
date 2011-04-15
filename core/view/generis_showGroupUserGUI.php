<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* 
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");		   

class TAOshowGroupUserGUI
{
	function TAOshowGroupUserGUI()
	{
	}
	function getOutput($ressource)
	{
		$output="";
		$output.="<BR>".TABLEHEADER."<tr><td rowspan=100 width=14%></td></tr>";
		
		$addanuser=getButtonimage(ADDUSERWITHIN.$ressource, true);
		$removeagroup=getButtonimage(REMOVE." ".$ressource);
		
		$output.='<tr><td colspan=3><div class="Title">'.GROUPOFUSERSDESCR.' '.$ressource.'</div></td></tr><tr></tr><tr>';
		
		
		$output.='<td align=right><FORM action=./index.php name=edituser target=_top method=post>
			<input type=hidden name=removegroup value='.$ressource.'>
			<input type=image src='.$removeagroup.' name=removeagroup >
		</form></td>';

		$output.='<td align=left> <FORM action=./generis_addUserGui.php method=get>
			<input type=hidden name=adduser value='.$ressource.'>
			<input type=image src='.$addanuser.' name=addanuser >
		</form></td>';

		$output.="</tr>";


		$output.=TABLEFOOTER;
		return $output;
	
	}
	   
	 
	 
	


}
?>
<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* 
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");	   

class TAOshowglobalUserGUI
{
	function TAOshowglobalUserGUI()
	{
	}
	function getOutput($ressource)
	{
		$output="";
		$output.='<FORM action=./index.php target=_top method=post>';
		$output.="<BR>".TABLEHEADER."<tr><td rowspan=100 width=14%></td></tr>";
		$output.='<tr><td colspan=3></td></tr>';
		$output.='<tr><td colspan=3><div class="Title">'.GROUPOFUSERSDESCR.'</div></td></tr>';
		$output.='<tr><td colspan=3></td></tr>';
		$output.='<tr><td colspan=3></td></tr>';

		$output.='<tr>';
		$addagroup=getButtonimage(APPLY);
		
		$output.='
			
			<td><input type=hidden name=adduser value='.$ressource.'><div class="AUTHINFOS">'.GROUPOFUSERSNAME.'</div></td>
			<td><input type=text name=nameofgroup value=""></td></tr><tr></tr><tr><td></td>
			<td><input type=image src='.$addagroup.' name=addagroup value="Add Group"></td>
				</tr>';
		
		
$output.=TABLEFOOTER.'</form>';
		return $output;
	
	}
	   
	 
	 
	


}
?>
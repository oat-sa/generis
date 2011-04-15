<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* 
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");	   

class TAOshowSubscriberGUI
{
	function TAOshowSubscriberGUI()
	{
	}
	function getOutput($ressource)
	{
	
		$output='';
		

		
		
		$output.="<BR>".TABLEHEADER."<tr><td rowspan=100 width=14%></td></tr>";
	
			// ancienne version
			//$result = getSubscriberDescription($_SESSION["session"],array($ressource));
			// appel webserices
			$result = calltoKernel('getSubscriberDescription',array($_SESSION["session"],array($ressource)));
			
			$user = $result["pOKorKO"]["description"];
			
			$groups=$result["pOKorKO"]["groups"];
			
			
			
			$output.='<center><FORM action=./index.php?showuser='.$user[0].' name=edituser target=_top method=post>';
			$output.='<tr><td colspan=3><Hr></td></tr>';
			$output.='<tr><td colspan=3><div class="Title">'.SUBSCRIBERDESC.'</div></td></tr>';
			$output.='<tr><td colspan=3><Hr></td></tr>';

			$output.='<tr><td><input type=hidden name=editanuser[id] value='.$user[0].'><div class="AUTHINFOS">'.USERNAME.'</div></td><td><input type=text name=editanuser[login] value='.$user[1].'></td></tr>';
			
			$output.='<tr><td><div class="AUTHINFOS">'.PASSWORD.'</div></td><td><input type=text name=editanuser[pass1]['.$user[2].'] value=CRYPTED></td></tr>';


			$output.='<tr><td><div class="AUTHINFOS">'.SUBSCRIBERGROUP.'</div></td><td><select name=editanuser[group]>';
			foreach ($groups as $key=>$val)

			{if ($val[0]==$user[5]) {$output.="<option selected value=$val[0]>$val[1]";} else {$output.="<option value=$val[0]>$val[1]";}}
			
			
			$output.='</select></td></tr><tr></tr>';
			
			$apply=getButtonimage(APPLY);
			$output.='<tr><td align=right><input type=image src='.$apply.' name=EditSubscriber value=Apply>';
			$output.="</form></td>";

			$RemoveSubscriber=getButtonimage(REMOVE,true);

			$output.="<td align=left><FORM action=./index.php name=edituser target=_top method=post>";
			$output.='<input type=hidden name=login value='.$user[0].'>
			<input type=image src='.$RemoveSubscriber.' name=RemoveSubscriber value="Remove Subscriber"></form></td></tr>';
			$output.="";

		$output.=TABLEFOOTER;
	
		return $output;
	
	}
	   
	 
	 
	


}
?>
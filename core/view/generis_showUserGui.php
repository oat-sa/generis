<?php
/*
	
   
   
    

    
    
    
    

    
    along with this program; if not, Edit to the Free Software
    

*/
/**
* 
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");
class TAOshowUserGUI
{
	/*
	*Private
	*@param $mask mask in the form MMGGOOSUBR ex : 1020220012
	*@return array( ["MyRights"] (["00"] => "" [01] => "SELECTED"), etc...
	*/
	
	function TAOshowUserGUI()
	{
	}
	function getOutput($ressource)
	{
	
		$output='';
		

		
		
		$output.='<FORM action=./index.php?showuser='.$user["login"].' name=edituser target=_top method=post><BR>'.TABLEHEADER.'<tr><td rowspan=100 width=14%></td></tr>';
			
			// ancienne version
			//$result = getUserdescription($_SESSION["session"],array($ressource));
			// appel webservices
			$result = calltoKernel('getUserdescription',array($_SESSION["session"],array($ressource)));
//print_r($result);
			// ancienne version
			//$groups = getGroups($_SESSION["session"]);
			// appel webservices
			$groups = calltoKernel('getGroups',array($_SESSION["session"]));

			$groups=$groups["pDescription"];
			$user=$result["pDescription"];
			
			
			$output.='<center>';
			$output.='<tr><td colspan=3></td></tr>';
			$output.='<tr><td colspan=3><div class="Title">'.SETTINGS.'</div></td></tr>';
			$output.='<tr><td colspan=3></td></tr>';
			$output.='<tr><td colspan=3></td></tr>';
			$output.='<tr><td><input type=hidden name=editanuser[login] value='.$user["login"].'><div class="AUTHINFOS">'.USERNAME.'</div></td><td>'.$user["login"].'</td></tr>';
			$output.='<tr><td><div class="AUTHINFOS">'.ISMEMBEROF.'</div></td><td><select name=editanuser[group][] MULTIPLE>';
			foreach ($groups as $key=>$val)

			{
				if 
					(strpos($user["group"],$val) ===false)
					{$output.="<option>$val";}
					else  {$output.="<option selected>$val";} 
			}
			
			
			$output.='</select></td></tr>';
			
			$output.='<tr><td><div class="AUTHINFOS">'.PASSWORD.'</div></td><td><input type=text name=editanuser[pass1]['.$user["password"].'] value=CRYPTED></td></tr>';
			//$output.='<tr><td><div class="AUTHINFOS">'.xUMASK.'</td><td><input type=text name=editanuser[umask] value='.$user["umask"].'></td></tr>';
			$groups = getgroups($_SESSION["session"]);
			$output.='<tr><td colspan=25>';
			
			error_reporting(0);
			
			$output .= '<div class=Title>Default privileges mask (Statements level)</div><br />'.TABLEHEADER;
					$output .= 	'<tr>
			<td><b>Author privileges</b></td>
			<td width="300"><b>Groups Privileges</b></td>
			<td><b>All users privileges</b></td>
			<td><b>Anonymous privileges</b></td>
			
			</tr>';
			
			$authornone ="CHECKED";$usersnone="CHECKED";$anonymousnone="CHECKED";

		 if (substr($user["umask"]["read"],0,1) == "y") {$authornone="";$authorread ="CHECKED";}
		 if (substr($user["umask"]["edit"],0,1) == "y") {$authornone=""; $authorread ="";$authorEdit ="CHECKED";}
		 if (substr($user["umask"]["delete"],0,1) == "y") {$authornone=""; $authorread ="";$authorEdit ="";$authorEditdel ="CHECKED";}
		
		 if (substr($user["umask"]["read"],1,1) == "y") {$usersnone="";$usersread ="CHECKED";}
		 if (substr($user["umask"]["edit"],1,1) == "y") {$usersnone=""; $usersread =""; $usersEdit ="CHECKED";}
		if (substr($user["umask"]["delete"],1,1) == "y") {$usersnone=""; $usersread =""; $usersEdit ="";$usersEditdel ="CHECKED";}

		 if (substr($user["umask"]["read"],2,1) == "y") {$anonymousnone="";$anonymousread ="CHECKED";}
		
		 
		
		$readallowedgroups =  substr($user["umask"]["read"],2);
		$Editallowedgroups = substr($user["umask"]["edit"],2);
		$Deleteallowedgroups = substr($user["umask"]["delete"],2);

		
			$grouprights =array();$groupsrightform ='';
			foreach ($groups["pDescription"] as $gkey=>$gval)
				{
					$no="CHECKED";$read="";$Edit="";$Delete="";
					if (strpos($readallowedgroups,"[".$gval."]") > 0) {$no="";$read="CHECKED";$Edit="";}
					if (strpos($readallowedgroups,",".$gval.",") > 0) {$no="";$read="CHECKED";$Edit="";}
					if (strpos($readallowedgroups,"[".$gval.",") > 0) {$no="";$read="CHECKED";$Edit="";}
					if (strpos($readallowedgroups,",".$gval."]") > 0) {$no="";$read="CHECKED";$Edit="";}

					if (strpos($Editallowedgroups,"[".$gval."]") > 0) {$no="";$read="";$Edit="CHECKED";}
					if (strpos($Editallowedgroups,",".$gval.",") > 0) {$no="";$read="";$Edit="CHECKED";}
					if (strpos($Editallowedgroups,"[".$gval.",") > 0) {$no="";$read="";$Edit="CHECKED";}
					if (strpos($Editallowedgroups,",".$gval."]") > 0) {$no="";$read="";$Edit="CHECKED";}

					if (strpos($Deleteallowedgroups,"[".$gval."]") > 0) {$no="";$read="";$Edit="";$Delete="CHECKED";}
					if (strpos($Deleteallowedgroups,",".$gval.",") > 0) {$no="";$read="";$Edit="";$Delete="CHECKED";}
					if (strpos($Deleteallowedgroups,"[".$gval.",") > 0) {$no="";$read="";$Edit="";$Delete="CHECKED";}
					if (strpos($Deleteallowedgroups,",".$gval."]") > 0) {$no="";$read="";$Edit="";$Delete="CHECKED";}

					$grouprights[$gval] = array("none" =>$no,"read" =>$read, "Edit" =>$Edit);
					$groupsrightform.= '<b>'.$gval.'</b>&nbsp;:<br />
					<input type=radio '.$no.' value=""  name="editanuser[rights][groups]['.$gval.']">&nbsp;None<br /><input type=radio '.$read.' value="r"  name="editanuser[rights][groups]['.$gval.']">&nbsp;Read<br /><input type=radio '.$Edit.' value="re"  name="editanuser[rights][groups]['.$gval.']">&nbsp;Edit<br /><input type=radio '.$Delete.' value="red"  name="editanuser[rights][groups]['.$gval.']">&nbsp;Delete<br /><br />
					';
				}
		

		$output .='
		<tr>
		
		<td>
		<input type=radio '.$authornone.' value="---" name="editanuser[rights][author]"> None<br />
		<input type=radio '.$authorread.' value="y--" name="editanuser[rights][author]"> Read<br />
		<input type=radio '.$authorEdit.' value="yy-" name="editanuser[rights][author]"> Edit<br />
		<input type=radio '.$authorEditdel.' value="yyy" name="editanuser[rights][author]"> Delete<br />
		</td>
		
		<td>
		'.$groupsrightform.'
		</td>
		
		<td>
		<input type=radio '.$usersnone.' value="---" name="editanuser[rights][users]"> None<br />
		<input type=radio '.$usersread.' value="y--" name="editanuser[rights]][users]"> Read<br />
		<input type=radio '.$usersEdit.' value="yy-" name="editanuser[rights][users]"> Edit<br />
			<input type=radio '.$usersEditdel.' value="yyy" name="editanuser[rights][users]"> Delete<br />
		</td>

		<td>
		
		<input type=radio '.$anonymousnone.' value="---" name="editanuser[rights][anonymous]"> None<br />
		<input type=radio '.$anonymousread.' value="y--" name="editanuser[rights][anonymous]"> Read<br />
		
		</td>
				
				
				</tr></table>
				';

			$output.="</td></tr>";

			if ($user["admin"]=="1") {$admin="CHECKED";$usern = "";} else {$admin="";$usern = "CHECKED";} 
			
			$output.='<tr><td><div class="AUTHINFOS">'.ISADMIN.'</div></td><td><input type=radio name=editanuser[isadmin] value=1 '.$admin.'>Is Admin<input type=radio name=editanuser[isadmin] value=0 '.$usern.'>Is User</td></td></tr>';
			
			$output.='<tr><td><div class="AUTHINFOS">'.LASTNAME.'</div></td><td><input type=text name=editanuser[lastname] value="'.$user["lastname"].'"></td></tr>';

			$output.='<tr><td><div class="AUTHINFOS">'.FIRSTNAME.'</div></td><td><input type=text name=editanuser[firstname] value="'.$user["firstname"].'"></td></tr>';
			
			$output.='<tr><td><div class="AUTHINFOS">'.COMPANY.'</div></td><td><input type=text name=editanuser[company] value="'.$user["company"].'"></td></tr>';

			$output.='<tr><td><div class="AUTHINFOS">'.EMAIL.'</div></td><td><input type=text name=editanuser[email] value="'.$user["e_mail"].'"></td></tr>';

			$output.='<tr><td><div class="AUTHINFOS">'.DEFAULTDATALG.'</div></td><td><input type=text name=editanuser[deflg] value="'.$user["deflg"].'"></td></tr>';
			
			$apply=getButtonimage(APPLY);


			$output.='<tr><td align=right><input type=image src='.$apply.' name=EditUser value=Apply>';
			$output.="</form></td>";
			$output.="<td align=left><FORM action=./index.php name=edituser target=_top method=post>";
				$RemoveUser=getButtonimage(REMOVEUSE,true);
			$output.='<input type=hidden name=login value='.$user["login"].'>
			<input type=image src='.$RemoveUser.' name=RemoveUser value="Remove User"></td></tr>';
			$output.="";

		$output.=TABLEFOOTER.'</form>';
	
		return $output;
	
	}
	   
	 
	 
	


}
?>
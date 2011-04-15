<?php

/**
* User interfaces to edit user settings
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");   
$localcache_range=array();
function getHTMLcombobox($postHTMLID,$range,$selected)
{
global $localcache_range;
$widget='<SELECT name='.$postHTMLID.'>';

if (isset($localcache_range[$range])) {$result = $localcache_range[$range];}
else {
$result = calltoKernel('getInstances',array($_SESSION["session"],array($range),array(""),false,true));
$localcache_range[$range]= $result;
}
$listinstances=$result["pDescription"][0];
foreach ($listinstances as $keyi=>$vali)
	{
	if  ($vali["InstanceKey"]==$selected)
	{$widget.='
	<option value='.$vali["InstanceKey"].' selected>'.strip_tags($vali["InstanceLabel"]).'</option>';
	}
	else {											
	$widget.='
	<option value='.$vali["InstanceKey"].'>'.strip_tags($vali["InstanceLabel"]).'</option>';
	}
	}
	$widget.= '
							</SELECT>
							';
return $widget;
}


class TAOsettingsGUI
{
	function TAOsettingsGUI()
	{
	}

	
	function getOutput($ressource)
	{
	
		$output='';
		

			$apply=getButtonimage(APPLY);
		
			$output.="
			
			<FORM action=./index.php?settings=1 name=edituser target=_top method=post>
			<br />
			<a href=\"http://127.0.0.1/generis/core/view/index.php?settings=stop\" target=_top>Back to resources Management</a>
			<br />
			";
			$result = calltoKernel('getUserdescription',array($_SESSION["session"],array($_SESSION["cuser"])));
			$groups = getgroups($_SESSION["session"]);
			error_reporting(E_ALL);
			$user=$result["pDescription"];
			

			$output.='<div style=position:absolute;top:10%;left:45%;>';
			error_reporting(0);
			$output .= '<div class=Title>Default privileges mask ([Scope : rdf:Statement] [Methods: read, edit, delete])</div><br />'.TABLEHEADER;
			$output .= 	'
			<tr class=tableheader>
			<td> <b>Author privileges</b></td>
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
			$grouprights =array();$groupsrightslist ='';$groupsrightsprivs ='';
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
					$groupsrightslist.= '<td>--<b>'.$gval.'</b>--</td>';
					$groupsrightsprivs.= '<td><input type=radio '.$no.' value=""  name="editanuser[rights][groups]['.$gval.']">&nbsp;None<br /><input type=radio '.$read.' value="r"  name="editanuser[rights][groups]['.$gval.']">&nbsp;Read<br /><input type=radio '.$Edit.' value="re"  name="editanuser[rights][groups]['.$gval.']">&nbsp;Edit<br /><input type=radio '.$Delete.' value="red"  name="editanuser[rights][groups]['.$gval.']">&nbsp;Delete<br />
					</td>';
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
				<input type=radio '.$usersnone.' value="---" name="editanuser[rights][users]"> None<br />
				<input type=radio '.$usersread.' value="y--" name="editanuser[rights]][users]"> Read<br />
				<input type=radio '.$usersEdit.' value="yy-" name="editanuser[rights][users]"> Edit<br />
					<input type=radio '.$usersEditdel.' value="yyy" name="editanuser[rights][users]"> Delete<br />
				</td>

				<td>
				
				<input type=radio '.$anonymousnone.' value="---" name="editanuser[rights][anonymous]"> None<br />
				<input type=radio '.$anonymousread.' value="y--" name="editanuser[rights][anonymous]"> Read<br />
				
				</td>
							
				</tr>
				<tr class=tableheader>
				'.$groupsrightslist.'
				</tr>
								
				<tr>
				'.$groupsrightsprivs.'
				</tr>
				</table>
				';

$output.="</div>";


			$output.=TABLEHEADER."
			<tr><td rowspan=100 width=14%></td><td></td><td width=50%></td><td width=50%></td></tr>";
		
			
			
			
			
			$output.='<tr><td colspan=3></td></tr>';
			$output.='<tr><td colspan=3><div class="Title">'.USERSETTINGS.'</div></td></tr>';
			$output.='<tr><td colspan=3></td></tr>';
			$output.='<tr><td><input type=hidden name=editanuser[login] value='.$user["login"].'><div class="AUTHINFOS">'.USERNAME.'</div></td><td>'.$user["login"].'</td></tr>';
			$output.='<tr><td><input type=hidden name=editanuser[group] value='.$user["group"].'></td></tr>';
			$output.='<tr><td><div class="AUTHINFOS">'.PASSWORD.'</div></td><td><input size=38 type=text name=editanuser[pass1]['.$user["password"].'] value=CRYPTED></td></tr>';
			if ($user["admin"]=="1") {$admin="1";} else {$admin="0";} 
			$output.='<tr><td></td><td><input type=hidden name=editanuser[isadmin] value='.$admin.'></td></tr>';
			$output.='<tr><td><div class="AUTHINFOS">'.LASTNAME.'</div></td><td><input size=38 type=text name=editanuser[lastname] value='.$user["lastname"].'></td></tr>';

			$output.='<tr><td><div class="AUTHINFOS">'.FIRSTNAME.'</div></td><td><input size=38 type=text name=editanuser[firstname] value='.$user["firstname"].'></td></tr>';
			
			$output.='<tr><td><div class="AUTHINFOS">'.COMPANY.'</div></td><td><input size=38 type=text name=editanuser[company] value='.$user["company"].'></td></tr>';

			$output.='<tr><td><div class="AUTHINFOS">'.EMAIL.'</div></td><td><input size=38 type=text name=editanuser[email] value='.$user["e_mail"].'></td></tr>';

						
			$output.='<tr><td><div class="AUTHINFOS">'.DEFAULTDATALG.'</div></td><td>
			
			<select name=editanuser[deflg]>
			<option value='.$user["deflg"].' selected>'.$user["deflg"].'
			<option value=AA>AA<option value=AB>AB<option value=AF>AF<option value=AM>AM<option value=AR>AR<option value=AS>AS<option value=AY>AY<option value=AZ>AZ<option value=BA>BA<option value=BE>BE<option value=BG>BG<option value=BH>BH<option value=BI>BI<option value=BN>BN<option value=BO>BO<option value=BR>BR<option value=CA>CA<option value=CO>CO<option value=CS>CS<option value=CY>CY<option value=DA>DA<option value=DE>DE<option value=DZ>DZ<option value=EL>EL<option value=EN>EN<option value=EO>EO<option value=ES>ES<option value=ET>ET<option value=EU>EU<option value=FA>FA<option value=FI>FI<option value=FJ>FJ<option value=FO>FO<option value=FR>FR<option value=FY>FY<option value=GA>GA<option value=GD>GD<option value=GL>GL<option value=GN>GN<option value=GU>GU<option value=HA>HA<option value=HI>HI<option value=HR>HR<option value=HU>HU<option value=HY>HY<option value=IA>IA<option value=IE>IE<option value=IK>IK<option value=IN>IN<option value=IS>IS<option value=IT>IT<option value=IW>IW<option value=JA>JA<option value=JI>JI<option value=JW>JW<option value=KA>KA<option value=KK>KK<option value=KL>KL<option value=KM>KM<option value=KN>KN<option value=KO>KO<option value=KS>KS<option value=KU>KU<option value=KY>KY<option value=LA>LA<option value=LN>LN<option value=LO>LO<option value=LT>LT<option value=LV>LV<option value=MG>MG<option value=MI>MI<option value=MK>MK<option value=ML>ML<option value=MN>MN<option value=MO>MO<option value=MR>MR<option value=MS>MS<option value=MT>MT<option value=MY>MY<option value=NA>NA<option value=NE>NE<option value=NL>NL<option value=NO>NO<option value=OC>OC<option value=OM>OM<option value=OR>OR<option value=PA>PA<option value=PL>PL<option value=PS>PS<option value=PT>PT<option value=QU>QU<option value=RM>RM<option value=RN>RN<option value=RO>RO<option value=RU>RU<option value=RW>RW<option value=SA>SA<option value=SD>SD<option value=SG>SG<option value=SH>SH<option value=SI>SI<option value=SK>SK<option value=SL>SL<option value=SM>SM<option value=SN>SN<option value=SO>SO<option value=SQ>SQ<option value=SR>SR<option value=SS>SS<option value=ST>ST<option value=SU>SU<option value=SV>SV<option value=SW>SW<option value=TA>TA<option value=TE>TE<option value=TG>TG<option value=TH>TH<option value=TI>TI<option value=TK>TK<option value=TL>TL<option value=TN>TN<option value=TO>TO<option value=TR>TR<option value=TS>TS<option value=TT>TT<option value=TW>TW<option value=UK>UK<option value=UR>UR<option value=UZ>UZ<option value=VI>VI<option value=VO>VO<option value=WO>WO<option value=XH>XH<option value=YO>YO<option value=ZH>ZH<option value=ZU>ZU
			</select></td></tr>';
			
			
			

$listmethodsofClasses = 
array("setInstance","setSubClass","setProperty","remove", "computescore");

/***********************************************************************************************************************/

$output.='<tr><td colspan=10><div class=Title>Resource\'s statements Privileges pattern</div></td></tr>';
$output.="<tr ><td colspan=10><table class=tabcontent>
			<tr class=tableheader>";		
			$output.='<td width=450>';
			$output.="Scope - Pattern";
			//$output.="";
			$output.='</td>';
			$output.='<td>';
			$output.="Author";
			$output.='</td>';
			$output.='<td width=300>';
			$output.="Groups";
			$output.='</td>';
			$output.='<td>';
			$output.="Users";
			$output.='</td>';
			$output.='<td>';
			$output.="Anonymous";
			$output.='</td>';
//print_r($user["umask"]["scopes"]);
//print_r($statementsprivileges);
foreach ($user["umask"]["scopes"] as $scope => $statementsprivileges)
		{
			
			error_reporting(0);
			foreach ($statementsprivileges["-"] as $privkey => $privilege) 

			{	

				//print_r($statementsprivileges);
				//add a blank privilege


				//id of privileges to be applied on the statement pattern for a specific scope
				$idPriv = '['.$privilege["ID"].']['.$privkey.']';
				set_time_limit(300);
				$output.='
				
				<tr>';
				$output.='<td>'.$idPriv;
				$postHTMLID = "editanuser[resrights]".$idPriv."[Scope]";
				//echo $idPriv." ".$postHTMLID."<br />";
				$output.=getHTMLcombobox($postHTMLID,"http://www.w3.org/2000/01/rdf-schema#Class",$scope);
				$postHTMLID = "editanuser[resrights]".$idPriv."[Predicate]";		
				$output.='<div style="position:relative;left:30px;top:30px;height:400px;">';
				//echo $idPriv." ".$postHTMLID."<br />";	
				$output.="<br/>&lt;Predicate&gt;<br/>".getHTMLcombobox($postHTMLID,"http://www.w3.org/1999/02/22-rdf-syntax-ns#Property",$privilege["predicate"])."<br/>";
				$postHTMLID = "editanuser[resrights]".$idPriv."[Object_R]";
				//echo $idPriv." ".$postHTMLID."<br />";
				$output.="&lt;Object (R)&gt;<br/>".getHTMLcombobox($postHTMLID,"http://www.w3.org/2000/01/rdf-schema#Resource",$privilege["object"])."<br />";
		

				$postHTMLID = "editanuser[resrights]".$idPriv."[Object_L]";

				$val["PropertyValue"][0] ="";
				
				$widget= "<input size=60 type=text name=editanuser[resrights]".$idPriv."[Object_L] value=\"".$privilege["object"]."\"><BR>";
			


				$output.="&lt;Object (literal)&gt;<br/>".$widget;
				
				$output.="<br/><br/><br/><input type=submit name=editanuser[removeresrights]'.$idPriv.' value=Remove >";
				$output.='</div>';
				$output.='</td>';
				
				
				$authornone ="CHECKED";
				$usersnone="CHECKED";
				$anonymousnone="CHECKED";
				$authorread ="";$authorEdit ="";$authorEditdel ="";$authorAssert ="";
				$usersread =""; $usersEdit ="";$usersEditdel ="";$usersAssert ="";
				$anonymousread ="";

				if (substr($privilege["read"],0,1) == "y") {$authornone="";$authorread ="CHECKED";}
				if (substr($privilege["edit"],0,1) == "y") {$authornone=""; $authorread ="";$authorEdit ="CHECKED";}
				if (substr($privilege["assert"],0,1) == "y") {$authornone=""; $authorread ="";$authorEdit ="";$authorEditdel ="";$authorAssert ="CHECKED";}
				if (substr($privilege["delete"],0,1) == "y") {$authornone=""; $authorread ="";$authorEdit ="";$authorEditdel ="CHECKED";}

				if (substr($privilege["read"],1,1) == "y") {$usersnone="";$usersread ="CHECKED";}
				if (substr($privilege["edit"],1,1) == "y") {$usersnone=""; $usersread =""; $usersEdit ="CHECKED";}
				
				if (substr($privilege["assert"],1,1) == "y") {$usersnone=""; $usersread =""; $usersEdit ="";$usersEditdel ="";$usersAssert ="CHECKED";}
				if (substr($privilege["delete"],1,1) == "y") {$usersnone=""; $usersread =""; $usersEdit ="";$usersEditdel ="CHECKED";}

				 if (substr($privilege["read"],2,1) == "y") {$anonymousnone="";$anonymousread ="CHECKED";}
				$readallowedgroups =  substr($privilege["read"],2);
				$Editallowedgroups = substr($privilege["edit"],2);
				$Deleteallowedgroups = substr($privilege["delete"],2);
				$Assertallowedgroups = substr($privilege["assert"],2);
				
				$grouprights =array();$groupsrightform ='';
				
				foreach ($groups["pDescription"] as $gkey=>$gval)
					{
						$no="CHECKED";$read="";$Edit="";$Delete="";$Assert="";
						$grouprights[$gval] = array("none" =>$no,"read" =>$read, "Edit" =>$Edit,"Assert" => $Assert);
							
						
						
					
					if (strpos($readallowedgroups,"[".$gval."]")> 0) {$no="";$read="CHECKED";$Edit="";$Delete="";$Assert="";}
					if (strpos($readallowedgroups,",".$gval.",")> 0) {$no="";$read="CHECKED";$Edit="";$Delete="";$Assert="";}
					if (strpos($readallowedgroups,"[".$gval.",")> 0) {$no="";$read="CHECKED";$Edit="";$Delete="";$Assert="";}
					if (strpos($readallowedgroups,",".$gval."]")> 0) {$no="";$read="CHECKED";$Edit="";$Delete="";$Assert="";}
					if (strpos($Editallowedgroups,"[".$gval."]")> 0) {$no="";$read="";$Edit="CHECKED";$Delete="";$Assert="";}
					if (strpos($Editallowedgroups,",".$gval.",")> 0) {$no="";$read="";$Edit="CHECKED";$Delete="";$Assert="";}
					if (strpos($Editallowedgroups,"[".$gval.",")> 0) {$no="";$read="";$Edit="CHECKED";$Delete="";$Assert="";}
					if (strpos($Editallowedgroups,",".$gval."]")> 0) {$no="";$read="";$Edit="CHECKED";$Delete="";$Assert="";}
					if (strpos($Assertallowedgroups,"[".$gval."]")> 0) {$no="";$read="";$Edit="";$Delete="";$Assert="CHECKED";}
					if (strpos($Assertallowedgroups,",".$gval.",")> 0) {$no="";$read="";$Edit="";$Delete="";$Assert="CHECKED";}
					if (strpos($Assertallowedgroups,"[".$gval.",")> 0) {$no="";$read="";$Edit="";$Delete="";$Assert="CHECKED";}
					if (strpos($Assertallowedgroups,",".$gval."]")> 0) {$no="";$read="";$Edit="";$Delete="";$Assert="CHECKED";}
					if (strpos($Deleteallowedgroups,"[".$gval."]")> 0) {$no="";$read="";$Edit="";$Delete="CHECKED";$Assert="";}
					if (strpos($Deleteallowedgroups,",".$gval.",")> 0) {$no="";$read="";$Edit="";$Delete="CHECKED";$Assert="";}
					if (strpos($Deleteallowedgroups,"[".$gval.",")> 0) {$no="";$read="";$Edit="";$Delete="CHECKED";$Assert="";}
					if (strpos($Deleteallowedgroups,",".$gval."]")> 0) {$no="";$read="";$Edit="";$Delete="CHECKED";$Assert="";}
					
					$groupsrightform.= '
					
					<center>--<b>'.$gval.'</b>--</center><br />
					<input type=radio '.$no.' value="" name="editanuser[resrights]'.$idPriv.'[groups]['.$gval.']">&nbsp;None&nbsp;
					<input type=radio '.$read.' value="r"  name="editanuser[resrights]'.$idPriv.'[groups]['.$gval.']">&nbsp;Read&nbsp;
					<input type=radio '.$Edit.' value="re"  name="editanuser[resrights]'.$idPriv.'[groups]['.$gval.']">&nbsp;Edit&nbsp;
					<input type=radio '.$Assert.' value="rea"  name="editanuser[resrights]'.$idPriv.'[groups]['.$gval.']">&nbsp;Assert&nbsp;
					<input type=radio '.$Delete.' value="red"  name="editanuser[resrights]'.$idPriv.'[groups]['.$gval.']">&nbsp;Delete&nbsp;<br /><br />
						';
					}

				$output .='<td>
				<input type=radio '.$authornone.' value="----" name="editanuser[resrights]'.$idPriv.'[author]">&nbsp;None<br />
				<input type=radio '.$authorread.' value="y---" name="editanuser[resrights]'.$idPriv.'[author]">&nbsp;Read<br />
				<input type=radio '.$authorEdit.' value="yy--" name="editanuser[resrights]'.$idPriv.'[author]">&nbsp;Edit<br />
				<input type=radio '.$authorAssert.' value="yyy-" name="editanuser[resrights]'.$idPriv.'[author]">&nbsp;Assert<br/>
				<input type=radio '.$authorEditdel.' value="yyyy" name="editanuser[resrights]'.$idPriv.'[author]">&nbsp;Delete<br/>
				</td>
				
				<td>
				'.$groupsrightform.'
				</td>
				
				<td>
				<input type=radio '.$usersnone.' value="----" name="editanuser[resrights]'.$idPriv.'[users]">&nbsp;None<br />
				<input type=radio '.$usersread.' value="y---" name="editanuser[resrights]'.$idPriv.'[users]">&nbsp;Read<br />
				<input type=radio '.$usersEdit.' value="yy--" name="editanuser[resrights]'.$idPriv.'[users]">&nbsp;Edit<br />
				<input type=radio '.$usersAssert.' value="yyy-" name="editanuser[resrights]'.$idPriv.'[users]">&nbsp;Assert<br />
				<input type=radio '.$usersEditdel.' value="yyy-" name="editanuser[resrights]'.$idPriv.'[users]">&nbsp;Delete<br />
				</td>

				<td>
				<input type=radio '.$anonymousnone.' value="----" name="editanuser[resrights]'.$idPriv.'[anonymous]">&nbsp;None<br />
				<input type=radio '.$anonymousread.' value="y---" name="editanuser[resrights]'.$idPriv.'[anonymous]">&nbsp;Read<br />
				</td>';
				$output.='</tr>
				
				';
				//break(2);
				
			}
		}

/**
* Generate button to create a new pattern, scope selection needed 
*/
$output.='<tr><td colspan=10><center>Add a new Privileges pattern about&nbsp;:&nbsp;';
$postHTMLID = "editanuser[newresrights]";
$output.=getHTMLcombobox($postHTMLID,"http://www.w3.org/2000/01/rdf-schema#Class",$scope);
$output.='&nbsp;<input type=submit name=EditUser value=Create></center></td></tr>';

			
/*************************************************************************************************
*								Resource's methods Privileges pattern
**************************************************************************************************/
$output.='<tr><td colspan=10><div class=Title>Resource\'s methods Privileges pattern</div></td></tr>';
//return $output;


$output.="<tr ><td colspan=10><table width=100% class=tabcontent>
			<tr class=tableheader>";
			$output.='<td>';
			$output.="Scope - ";
			
			$output.="Methods";
			$output.='</td>';
			$output.='<td>';
			$output.="Author";
			$output.='</td>';
			$output.='<td>';
			$output.="Groups";
			$output.='</td>';
			$output.='<td>';
			$output.="Users";
			$output.='</td>';
			$output.='<td>';
			$output.="Anonymous";
			$output.='</td>';
			$output.='</tr>';

error_reporting("^E_NOTICE");
foreach ($user["umask"]["scopes"] as $scope => $statementsprivileges)
		{

			foreach ($statementsprivileges as $method => $privilege) 
			{
				error_reporting(0);
				$idPriv = '['.$privilege["ID"].']';
				if ($method!="-")
				{
				$output.='<tr>';
				$output.='<td>';
				//$scope = "#11712899715102";
				$widget="";
				$postHTMLID = "editanuser[resrights][methods]".$idPriv."[Scope]";	
				$output.=getHTMLcombobox($postHTMLID,"http://www.w3.org/2000/01/rdf-schema#Class",$scope);
				
				$output.='<span style="position:relative;left:-200px;top:30px;">';
				
				$widget= '<SELECT name="editanuser[resrights][methods]'.$idPriv.'[Method]">';
				foreach ($listmethodsofClasses as $amethod) 	
							{
							$widget.='<option value='.$amethod;
							if ($method == $amethod) {$widget.=" selected";}
							$widget.='>'.$amethod.'</option>';
							}
				$widget.= '</SELECT>';

				$output.=$widget;

				$output.="";
				$output.='</span><br /><br /><input type=submit name=editanuser[removeresrightsmethods]'.$idPriv.' value=Remove ></td>';

				$authornone ="CHECKED";$usersnone="CHECKED";$anonymousnone="CHECKED";
				$authorread="";;$usersread ="";$anonymousread ="";
				if (substr($privilege["privileges"],0,1) == "y") {$authornone="";$authorread ="CHECKED";}
				
				if (substr($privilege["privileges"],1,1) == "y") {$usersnone="";$usersread ="CHECKED";}
				
				if (substr($privilege["privileges"],2,1) == "y") {$anonymousnone="";$anonymousread ="CHECKED";}

				$readallowedgroups =  substr($privilege["privileges"],2);
				

				$grouprights =array();$groupsrightform ='';
				foreach ($groups["pDescription"] as $gkey=>$gval)
					{
						$no="CHECKED";$read="";
					if (strpos($readallowedgroups,"[".$gval."]")> 0) {$no="";$read="CHECKED";}
					if (strpos($readallowedgroups,",".$gval.",")> 0) {$no="";$read="CHECKED";}
					if (strpos($readallowedgroups,"[".$gval.",")> 0) {$no="";$read="CHECKED";}
					if (strpos($readallowedgroups,",".$gval."]")> 0) {$no="";$read="CHECKED";}


					
						$grouprights[$gval] = array("none" =>$no,"read" =>$read, "Edit" =>$Edit);
						$groupsrightform.= '<b>'.$gval.'&nbsp;</b>&nbsp;<input type=radio '.$no.' value="-"  name="editanuser[resrights][methods]'.$idPriv.'[groups]['.$gval.']">&nbsp;no&nbsp;<input type=radio '.$read.' value="'.$gval.'"  name="editanuser[resrights][methods]'.$idPriv.'[groups]['.$gval.']">&nbsp;yes&nbsp;<br /><br />
						';
					}
				$output.='
				<td>
				<input type=radio '.$authornone.' value="-" name="editanuser[resrights][methods]'.$idPriv.'[author]">no<br />
				<input type=radio '.$authorread.' value="y" name="editanuser[resrights][methods]'.$idPriv.'[author]">yes<br />
				
				</td>
				
				<td>
				'.$groupsrightform.'
				</td>
				
				<td>
				<input type=radio '.$usersnone.' value="-" name="editanuser[resrights][methods]'.$idPriv.'[users]">no<br />
				<input type=radio '.$usersread.' value="y" name="editanuser[resrights][methods]'.$idPriv.'[users]">yes<br />
				
				</td>

				<td>
				
				<input type=radio '.$anonymousnone.' value="-" name="editanuser[resrights][methods]'.$idPriv.'[anonymous]">no<br />
				<input type=radio '.$anonymousread.' value="y" name="editanuser[resrights][methods]'.$idPriv.'[anonymous]">yes<br />
				
				</td>
				';

				$output.='</tr>';

				
				}
			}
		}

$output.="</table></td></tr>";
			

		
	

/**
* Generate button to create a new pattern, scope selection needed 
*/
$output.='<tr><td colspan=10><center>Add a new Privileges pattern about&nbsp;:&nbsp;';
$postHTMLID = "editanuser[newresmethrights]";
$output.=getHTMLcombobox($postHTMLID,"http://www.w3.org/2000/01/rdf-schema#Class",$scope);
$output.='&nbsp;<input type=submit name=EditUser value=CreateMethod><br/><br/>';		

		
	$output.='<input type=image src='.$apply.' name=EditUser value=Apply></center></td></tr>';

		$output.=TABLEFOOTER;
		$output.=TABLEFOOTER;
		$output.="</form>";
	




		return $output;
	
	}
	   
	 
	 
	


}


/*
			

			$output.='<tr>';
			$output.='<td>';$val["PropertyRange"]="http://www.w3.org/2000/01/rdf-schema#Class";
			$scope = $val["PropertyValue"][0] ="#11712899715102";	$widget="";error_reporting(E_ALL);getHTMLcombobox($postHTMLID,);$output.=$widget;
			$output.='</td>';

			$output.='<td>';
			$widget= '<SELECT name="instanceCreation[properties]['.$val["PropertyKey"].'][]">';
			foreach ($listmethodsofClasses as $method) 	{$widget.='<option value='.$method.' selected>'.$method.'</option>';}
			$widget.= '</SELECT>';
			$output.=$widget;
			$output.='</td>';
			
			$grouprights =array();$groupsrightform ='';
			foreach ($groups["pDescription"] as $gkey=>$gval)
				{
					$no="CHECKED";$read="";$Edit="";$Delete="";
					$grouprights[$gval] = array("none" =>$no,"read" =>$read, "Edit" =>$Edit);
					$groupsrightform.= '<b>'.$gval.'</b>&nbsp;:<br />
					<input type=radio '.$no.' value=""  name="editanuser[resrights]['.$scope.'][groups]['.$gval.']">&nbsp;yes<br /><input type=radio '.$read.' value="r"  name="editanuser[resrights]['.$scope.'][groups]['.$gval.']">&nbsp;no<br />
					';
				}
			$output.='
			<td>
			<input type=radio '.$authornone.' value="---" name="editanuser[resrights]['.$scope.'][author]">yes<br />
			<input type=radio '.$authorread.' value="y--" name="editanuser[resrights]['.$scope.'][author]">no<br />
			
			</td>
			
			<td>
			'.$groupsrightform.'
			</td>
			
			<td>
			<input type=radio '.$usersnone.' value="---" name="editanuser[resrights]['.$scope.'][users]">yes<br />
			<input type=radio '.$usersread.' value="y--" name="editanuser[resrights]['.$scope.']][users]">no<br />
			
			</td>

			<td>
			
			<input type=radio '.$anonymousnone.' value="---" name="editanuser[resrights]['.$scope.'][anonymous]">yes<br />
			<input type=radio '.$anonymousread.' value="y--" name="editanuser[resrights]['.$scope.'][anonymous]">no<br />
			
			</td>
			';

			$output.='</tr>';

			

				$output.='<tr>';
			$output.='<td>';$val["PropertyRange"]="http://www.w3.org/2000/01/rdf-schema#Class";
			
			$scope = $val["PropertyValue"][0] ="http://www.w3.org/2000/01/rdf-schema#Class";	$widget="";error_reporting(E_ALL);getHTMLcombobox($postHTMLID,);$output.=$widget;
			$output.='</td>';

			$output.='<td>';
			$widget= '<SELECT name="instanceCreation[properties]['.$val["PropertyKey"].'][]">';
			$listmethodsofClasses = 
			array("setInstance","setProperty","remove", "computescore","setSubClass");
			foreach ($listmethodsofClasses as $method) 	
				{
					$widget.='<option value='.$method.' selected>'.$method.'</option>';
				}
			$widget.= '</SELECT>';
			$output.=$widget;
			$output.='</td>';
			
			$grouprights =array();$groupsrightform ='';
			foreach ($groups["pDescription"] as $gkey=>$gval)
				{
					$no="CHEKCED";$read="";$Edit="";$Delete="";
					$grouprights[$gval] = array("none" =>$no,"read" =>$read, "Edit" =>$Edit);
					$groupsrightform.= '<b>'.$gval.'</b>&nbsp;:<br />
					<input type=radio '.$no.' value=""  name="editanuser[resrights]['.$scope.'][groups]['.$gval.']">&nbsp;yes<br /><input type=radio '.$read.' value="r"  name="editanuser[resrights]['.$scope.'][groups]['.$gval.']">&nbsp;no<br />
					';
				}
			$output.='
			<td>
			<input type=radio '.$authornone.' value="---" name="editanuser[resrights]['.$scope.'][author]">yes<br />
			<input type=radio '.$authorread.' value="y--" name="editanuser[resrights]['.$scope.'][author]">no<br />
			</td>
			<td>
			'.$groupsrightform.'
			</td>
			<td>
			<input type=radio '.$usersnone.' value="---" name="editanuser[resrights]['.$scope.'][users]">yes<br />
			<input type=radio '.$usersread.' value="y--" name="editanuser[resrights]['.$scope.']][users]">no<br />
			</td>
			<td>
			<input type=radio '.$anonymousnone.' value="---" name="editanuser[resrights]['.$scope.'][anonymous]">yes<br />
			<input type=radio '.$anonymousread.' value="y--" name="editanuser[resrights]['.$scope.'][anonymous]">no<br />
			</td>
			';

			$output.='</tr>';
*/		
?>
<?php

include_once("generis_ConstantsOfGui.php");
include_once("../api/generisApiPhp.php");
include_once("generis_utils.php");

if (isset($_POST["uri"])) {$urii =$_POST["uri"]; } else {$urii = $_GET["param1"];}
if (!(isset($_SESSION))) {session_start();}
//print_r($_POST["rights"]);
if (isset($_POST["rights"]))
	{
		foreach ($_POST["rights"] as $tripleid=>$reqmask)
			{
				$readmask = $reqmask["author"][0].$reqmask["users"][0].$reqmask["anonymous"][0];
				$editmask = $reqmask["author"][1].$reqmask["users"][1].$reqmask["anonymous"][1];
				$deletemask = $reqmask["author"][2].$reqmask["users"][2].$reqmask["anonymous"][2];
				
				//$mask = $reqmask["author"].$reqmask["users"].$reqmask["anonymous"];
				
				$readgroups="[";$Editgroups="[";$Delgroups="[";
				foreach ($reqmask["groups"] as $groupid => $privilege)
					{
						if ($privilege == "r")
						$readgroups.=$groupid.",";
						if ($privilege == "re")
						{
						$readgroups.=$groupid.",";
						$Editgroups.=$groupid.",";
						}
						if ($privilege == "red")
						{
						$readgroups.=$groupid.",";
						$Editgroups.=$groupid.",";
						$Delgroups.=$groupid.",";
						}
					}
				
				if ($readgroups!="[") {$readgroups = substr($readgroups,0,strlen($readgroups)-1);} 
				if ($Editgroups!="[") {$Editgroups = substr($Editgroups,0,strlen($Editgroups)-1);} 
				if ($Delgroups!="[") {$Delgroups = substr($Delgroups,0,strlen($Delgroups)-1);} 
				$readgroups.="]";$Editgroups.="]";$Delgroups.="]";
				//$mask = $mask.$readgroups.$Editgroups;
				$r = $readmask.$readgroups;
				$e = $editmask.$Editgroups;
				$d = $deletemask.$Delgroups;
			
			//print_r(array($tripleid,array($r,$e,$d)));
				setPrivilegesonStatement($_SESSION["session"],$tripleid,array($r,$e,$d));
				//echo "Privileges have been updated : <br>Read: ".$r."<br>Edit: ".$e."<br>Delete: ".$d."<br>";				
			}
	}

error_reporting(0);

$triplerights= getrdfStatements($_SESSION["session"],urldecode($urii),array(""));
//print_r($triplerights);
$groups = getgroups($_SESSION["session"]);


$output= HEAD.'<body class=paneIframe>';

$output .= '<div class=Title>Rights Management for '.urldecode($urii)." (Methods on Statement level)<sup>1</sup></div><br />
<span class=privileges><sup>1</sup><i>You may change privileges on a triple only if you are allowed to delete it </i></span><br />";

$output.="<br /><table  class=tabcontent>
			<tr class=tableheader>";
$output .= 	'
		
		<td><b>ID</b></td>
		<td ><b>Predicate</b></td>
		<td><b>Object</b></td>
		<td><b>Author</b></td>
		<td><b>Author privileges</b></td>
		<td><b>Groups Privileges</b></td>
		<td><b>All users privileges</b></td>
		<td><b>Anonymous privileges</b></td>
		<td><b>&nbsp;</b>  </td>
		</tr>';
foreach ($triplerights as $key=>$val)
	{
		if (($val[10]) == "")  $val[10]="generis";
		
		$authornone ="CHECKED";$usersnone="CHECKED";$anonymousnone="CHECKED";$anonymousread="";$authorread="";$authorEdit ="";$authorEditdel ="";
$usersread =""; $usersEdit ="";$usersEditdel ="";
		 if (substr($val[11],0,1) == "y") {$authornone="";$authorread ="CHECKED";}
		 if (substr($val[12],0,1) == "y") {$authornone=""; $authorread ="";$authorEdit ="CHECKED";}
		 if (substr($val[13],0,1) == "y") {$authornone=""; $authorread ="";$authorEdit ="";$authorEditdel ="CHECKED";}
		
		 if (substr($val[11],1,1) == "y") {$usersnone="";$usersread ="CHECKED";}
		 if (substr($val[12],1,1) == "y") {$usersnone=""; $usersread =""; $usersEdit ="CHECKED";}
		if (substr($val[13],1,1) == "y") {$usersnone=""; $usersread =""; $usersEdit ="";$usersEditdel ="CHECKED";}

		 if (substr($val[11],2,1) == "y") {$anonymousnone="";$anonymousread ="CHECKED";}
	
		 
		
		$readallowedgroups =  substr($val[11],2);
		$Editallowedgroups = substr($val[12],2);
		$Deleteallowedgroups = substr($val[13],2);

		
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
					$groupsrightform.= '<center>--<b>'.$gval.'</b>--</center><br />
					<input type=radio '.$no.' value=""  name="rights['.$key.'][groups]['.$gval.']">&nbsp;None&nbsp;<input type=radio '.$read.' value="r"  name="rights['.$key.'][groups]['.$gval.']">&nbsp;Read&nbsp;<input type=radio '.$Edit.' value="re"  name="rights['.$key.'][groups]['.$gval.']">&nbsp;Edit&nbsp;<input type=radio '.$Delete.' value="red"  name="rights['.$key.'][groups]['.$gval.']">&nbsp;Delete<br /><br />
					';
				}
		

		$output .='
		<tr class="tableline">
		<FORM action="generis_rightsEdition.php?uri='.$urii.'" method=post>
		<input type=hidden name=uri value="'.$urii.'">
		<td valign=top >'.$key.'</td>
		<td valign=top ><b>'.substr($val[2],strpos($val[2],"#")+1).'</b></td>             
		<td valign=top><b>'.substr($val[3],strpos($val[3],"#"),20).'</b></td>
		<td valign=top>'.$val[10].'</td>
		

		<td valign=top>
		<input type=radio '.$authornone.' value="---" name="rights['.$key.'][author]"> None<br />
		<input type=radio '.$authorread.' value="y--" name="rights['.$key.'][author]"> Read<br />
		<input type=radio '.$authorEdit.' value="yy-" name="rights['.$key.'][author]"> Edit<br />
		<input type=radio '.$authorEditdel.' value="yyy" name="rights['.$key.'][author]"> Delete<br />
		</td>
		
		<td width=200px valign=top>
		'.$groupsrightform.'
		</td>
		
		<td valign=top>
		<input type=radio '.$usersnone.' value="---" name="rights['.$key.'][users]"> None<br />
		<input type=radio '.$usersread.' value="y--" name="rights['.$key.'][users]"> Read<br />
		<input type=radio '.$usersEdit.' value="yy-" name="rights['.$key.'][users]"> Edit<br />
		<input type=radio '.$usersEditdel.' value="yyy" name="rights['.$key.'][users]"> Delete<br />
		</td>

		<td valign=top>
		
		<input type=radio '.$anonymousnone.' value="---" name="rights['.$key.'][anonymous]"> None<br />
		<input type=radio '.$anonymousread.' value="y--" name="rights['.$key.'][anonymous]"> Read<br />
		
		</td>
		
		
		<td><input type=submit value='.APPLY.'></td>
		</FORM>
		</tr>
		';

	}
$output.="</table>";
error_reporting(0);
//print_r($_POST);
if (isset($_POST["resrights"]))
	{
		
		$uri = $_POST["uri"];
		setPrivileges($_SESSION["session"],$uri,"","---[]",$_SESSION["cuser"]);
		foreach ($_POST["resrights"] as $method=>$privis)
			{
				if ($privis["author"] != "y") 
						{
							$privis["author"]="-";
						}
				if ($privis["users"] != "y") 
						{
							$privis["users"]="-";
						}	
				if ($privis["anonym"] != "y") 
						{
							$privis["anonym"]="-";
						}
				$privis["privs_groups"]="[";
				foreach ($privis["groups"] as $gid=>$privg)
						{
							$privis["privs_groups"].=$gid.",";
						}
				if ($privis["privs_groups"]!="[") {$privis["privs_groups"] = substr($privis["privs_groups"],0,strlen($privis["privs_groups"])-1);} 
				$privis["privs_groups"].="]";
				$privileges = $privis["author"].$privis["users"].$privis["anonym"].$privis["privs_groups"];
			//echo $method; echo "     ";echo $privileges; echo "<br>";
				setPrivileges($_SESSION["session"],$uri,$method,$privileges,$_SESSION["cuser"]);
				//echo "<br /><br />".$method."   ".$privileges;
			}
	}


//print_r($_SESSION);


$privilegesonresources= getPrivileges($_SESSION["session"],urldecode($urii),array(""));
//print_r($privilegesonresources);


$listmethods = 
array();

$typeOf = isClass($_SESSION["session"],urldecode($urii));

foreach ($typeOf as $key=>$val)
	{
		if ($val == "http://www.w3.org/2000/01/rdf-schema#Class") $listmethods =array_merge(array("setInstance","setSubClass","setProperty","remove","removeInstances"),$listmethods);

		if ($val == "http://www.w3.org/1999/02/22-rdf-syntax-ns#Property") $listmethods =array_merge(array("setsubProperty","remove"),$listmethods);
	}

$listmethods =array_unique($listmethods);


$output .= '<br /><div class=Title>Rights Management for '.urldecode($urii)." (Methods on resources level)</div><br /><br />
";
$output.='
<FORM action="generis_rightsEdition.php?uri='.$urii.'" method=post>
<input type=hidden name=uri value="'.$urii.'">

<table class=tabcontent width=100%>
';

error_reporting(0);


$output .= 	'		
		<tr class=tableheader>
		<td><b>Author privileges</b></td>
		<td><b>Groups Privileges</b></td>
		<td><b>All users privileges</b></td>
		<td><b>Anonymous privileges</b></td>
		<td><b>&nbsp;</b></td>
		</tr><tr>';
		
		$output .= 	'<td valign=top>';
		foreach ($listmethods as $key=>$val)
			{	$privis = str_split($privilegesonresources[$val][1][3]);
				$checked ="";
				if ($privis[0]=="y") $checked ="CHECKED";
				$output.=' <input type=checkbox '.$checked.' value="y" name="resrights['.$val.'][author]"> '.$val.'<br />';
			}
		$output .= 	'</td>';

		$output .= 	'<td valign=top>';
		
		foreach ($groups["pDescription"] as $gkey=>$gval)
				{
					$output.="<b>".$gval."</b>&nbsp;:<br />";
					foreach ($listmethods as $key=>$val)
					{	
						$allowedgroups =  substr($privilegesonresources[$val][1][3],2);
						//echo "<br/>".$allowedgroups."<br/>";
						$checked ="";
						if (strpos($allowedgroups,"[".$gval.",") > 0) {$checked ="CHECKED";}
						if (strpos($allowedgroups,",".$gval."]") > 0) {$checked ="CHECKED";}
						if (strpos($allowedgroups,"[".$gval."]") > 0) {$checked ="CHECKED";}
						if (strpos($allowedgroups,",".$gval.",") > 0) {$checked ="CHECKED";}
						$output.=' <input type=checkbox '.$checked.' value="y" name="resrights['.$val.'][groups]['.$gval.']"> '.$val.'<br />';
					}
				}
		$output .= 	'</td>';
		$output .= 	'<td valign=top>';
		foreach ($listmethods as $key=>$val)
			{	$privis = str_split($privilegesonresources[$val][1][3]);
				$checked ="";
				if ($privis[1]=="y") $checked ="CHECKED";
				$output.=' <input type=checkbox '.$checked.' value="y" name="resrights['.$val.'][users]"> '.$val.'<br />';
			}
		$output .= 	'</td>';
		$output .= 	'<td valign=top>';
		foreach ($listmethods as $key=>$val)
			{	$privis = str_split($privilegesonresources[$val][1][3]);
				$checked ="";
				if ($privis[2]=="y") $checked ="CHECKED";
				$output.=' <input type=checkbox '.$checked.' value="y" name="resrights['.$val.'][anonym]"> '.$val.'<br />';
			}
		$output .= 	"</td><td><input type=hidden name=resrights[force][force] value=force><input type=submit value=".APPLY."></td>";

$output .= 	'</tr>';

echo $output."</table></form>";

?>
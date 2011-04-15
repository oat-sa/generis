<?php


if (!(isset($_SESSION))) {session_start();}


$loginPortal = $_SESSION["LoginPortal"];
$loginPass = $_SESSION["PassPortal"];

include("../../common/config.php");
include(dirname (__FILE__).'/../include/adodb5/adodb.inc.php');
include(dirname (__FILE__).'/../core/view/generis_ConstantsOfGui.php');
include(dirname (__FILE__).'/../core/view/generis_utils.php');
loadGUIlanguage(dirname (__FILE__).'/../geenris/view/lg/EN.php');
include("CommonFunctions.php");

if (isset($_POST["ID"]))
{
		if (($_POST["enabled"]) == "on")		{$checked="CHECKED";} else 			{$checked="";}
		updatemodule($_POST["ID"],$_POST["modulename"],$_POST["Modulelogin"],$_POST["Modulepass"],$_POST["ModuleURL"],$checked);
}
if (isset($_GET["erase"]))
{
		if (($_POST["enabled"]) == "on")		{$checked="CHECKED";} else 			{$checked="";}
	unaffectModule($_GET["erase"],$loginPortal);	
}
if (isset($_POST["newID"]))
{
		if (($_POST["enabled"]) == "on") {$checked="CHECKED";} else 			{$checked="";}
		affectnewModule($loginPortal,$_POST["modulename"],$_POST["Modulelogin"],$_POST["Modulepass"],$_POST["ModuleURL"],$checked);
}
$modules = getModulesof($loginPortal,$loginPass);

echo '<head>

<LINK media=screen href="../core/view/CSS/generis_default.css" type=text/css rel=stylesheet>
<body class=paneIFrame>';
//$output= HEAD;
$output.= '<span class=leftmargin>'.TABLEHEADER;
$output.="<tr><td colspan=7><b><a href=ManageModules.php?new=on><img border=0 src=../view/icons/new.png>&nbsp;&nbsp;".NEWSUBSCRIPTION."</a></b></td></tr>";
$output.="<tr><td> </td><td> </td><td><b>".MODULENAME."</b></td><td><b>".MODULELOGIN."</b></td><td><b>".MODULEPASS."</b></td><td><b>".MODULEURL."</b></td><td><b>".ENABLED."</b></td></tr>";

foreach ($modules as $key=>$val)
					{	
if ($val[3]=="") {$val[3]=$_SERVER["HTTP_HOST"];}
$output.="<tr><td><a href=ManageModules.php?edit=".$val[5]."><img  border=0 src=../view/icons/edit.png></a></td> <td><a href=ManageModules.php?erase=".$val[5]."><img border=0 src=../view/icons/erase.png></a></td><td>$val[0]</td><td>$val[1]</td><td>$val[2]</td><td>$val[3]</td><td><input type=checkbox ".$val[4]."></td></tr>";
					}

$output.=TABLEFOOTER.'</span>';


if (isset($_GET["edit"]))
	{	$modules = getModules($_GET["edit"]);
		$module = $modules[0];
		$output.="<span class=leftmargin><form action=ManageModules.php method=post><input type=hidden name=ID value=".$_GET["edit"].">";
		$output.= TABLEHEADER;
		$output.="<tr><td> </td><td><b>".MODULENAME."</b></td><td><b>".MODULELOGIN."</b></td><td><b>".MODULEPASS."</b></td><td><b>".MODULEURL."</b></td><td><b>".ENABLED."</b></td></tr>";
		
		$output.="<tr><td><input type=image src=../../view/icons/save.png></td><td><input type=textbox name=modulename value=$module[0]></td><td><input type=textbox name=Modulelogin value=$module[1]></td><td><input type=textbox name=Modulepass value=$module[2]></td><td><input type=textbox name=ModuleURL value=$module[3]></td><td><input name=enabled type=checkbox ".$module[4]."></td></tr>";
		$output.="</FORM></span>";
	}
if (isset($_GET["new"]))
	{	
		
		$output.="<span class=leftmargin><form action=ManageModules.php method=post><input type=hidden name=newID value=>";
		$output.= TABLEHEADER;
		$output.="<tr><td> </td><td><b>".MODULENAME."</b></td><td><b>".MODULELOGIN."</b></td><td><b>".MODULEPASS."</b></td><td><b>".MODULEURL."</b></td><td><b>".ENABLED."</b></td></tr>";
		
		$output.="<tr><td><input type=image border=0 border=0 src=../view/icons/save.png></td><td><input type=textbox name=modulename ></td><td><input type=textbox name=Modulelogin ></td><td><input type=textbox name=Modulepass ></td><td><input type=textbox name=ModuleURL ></td><td><input name=enabled type=checkbox></td></tr>";
		$output.="</FORM></span>";
	}
$output.="</body>";

echo $output;
?>
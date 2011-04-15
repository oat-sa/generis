<?php
define('GENERIS_PORTAL_DBNAME','generisportal');
if (!(isset($_SESSION))) {session_start();}


$GLOBALS['time_start'] = microtime(true);
include_once("../common/common.php");
//include_once("../common/ext/loader/extension.php");

//$ext = extension::getExtension();
//$log = $ext->loadExtension(EXTENSION);

//$_SESSION["ext"]=$ext;

	error_reporting(E_ALL);


include(dirname (__FILE__).'/../includes/adodb5/adodb.inc.php');
include(dirname (__FILE__).'/../core/view/generis_utils.php');

loadGUIlanguage(dirname (__FILE__).'/../core/view/lg/EN.php');

/*
* Return specialization described in globalparameters table of the corresponding modulename, this information is contained in the session returned by the authenticate service
*/
function getModuleSpecialisation($modulename)
	{
		//hack, should not be based on name, it works because module are created only using the install script
		if (!((stripos($modulename,"Subjects"))==0)) return "Subjects";
		if (!((stripos($modulename,"Groups"))==0)) return "Groups";
		if (!((stripos($modulename,"Results"))==0)) return "Results";
		if (!((stripos($modulename,"Tests"))==0)) return "Tests";
		if (!((stripos($modulename,"Items"))==0)) return "Items";
		if (!((stripos($modulename,"Delivery"))==0)) return "Delivery";
		return "Kernel";
	}

function getModulesof($login,$password)
{
	$con = NewADOConnection(SGBD_DRIVER);
	$con->Connect(DATABASE_URL, DATABASE_LOGIN, DATABASE_PASS, GENERIS_PORTAL_DBNAME);
	/*
	 * Check, if the user (login, password) exists.
	 * Return "KO" if not.
	 */
	$query="Select generisPass from generisuser where generisLogin='".$login."'";
	$ok = false;
	$result =  $con->Execute($query);

	while (!is_null($result) and !($result->EOF))
	{
		$row=$result->fields;
	    if ($password == $row[0]) {$ok=true;}
		$result-> MoveNext();
	}

	if (!($ok)) return "KO";

	/*
	 * Find all Modules, that have the same login as the user.
	 * Return an array $resultx containing the modulenames, login and password, indexed with the type of the Module.
	 */
	$resultx=array();
	$query="Select generisModuleName,ModuleLogin,ModulePass from generismodules where generisLogin='".$login."'";
	$result =  $con->Execute($query);

	while (!$result-> EOF)
	{
		$row=$result->fields;
		$rightindex = getModuleSpecialisation($row[0]);
		$resultx[$rightindex][] = $row;
		$result-> MoveNext();
	}

	return $resultx;
}

function affectnewModule($login,$modulename, $log, $pass)
{$con = NewADOConnection(SGBD_DRIVER);
	$con->Connect(DATABASE_URL, DATABASE_LOGIN, DATABASE_PASS, GENERIS_PORTAL_DBNAME);
    $con->debug = false;

	$query="INSERT INTO generismodules (generisModuleName,generisLogin, ModuleLogin,ModulePass) VALUES ('".$modulename."','".$login."','".$log."','".$pass."')";
	$result =  $con->Execute($query);
}

function unaffectnewModule($login,$modulename, $log, $pass)
{$con = NewADOConnection(SGBD_DRIVER);
	$con->Connect(DATABASE_URL, DATABASE_LOGIN, DATABASE_PASS, GENERIS_PORTAL_DBNAME);
    $con->debug = false;

	$query="Delete from generismodules where generisModuleName='".$modulename."' and generisLogin='".$login."' and ModuleLogin='".$log."' and ModulePass='".$pass."'";
	$result =  $con->Execute($query);
}

function newUser($login,$pass)
{$con = NewADOConnection(SGBD_DRIVER);
	$con->Connect(DATABASE_URL, DATABASE_LOGIN, DATABASE_PASS, GENERIS_PORTAL_DBNAME);
    $con->debug = false;

	$query="INSERT INTO generisuser (generisLogin,generisPass) VALUES ('".$login."','".$pass."')";
	$result =  $con->Execute($query);
}

$output='
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<head>
<title>'.$ext->name.'</title>
<LINK media=screen href="../core/view/CSS/generis_default.css"
type=text/css rel=stylesheet>
<meta HTTP-EQUIV="content-type" CONTENT="text/html; charset=UTF-8">
<SCRIPT type="text/javascript">
	function OuvrirFenetre(url){
	window.open (url,"X","toolbar=yes,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=1000,height=650,");
	}
</SCRIPT>
</head>
<body style="font-size:11px;color:black;overflow: scroll;font-family:Verdana;">';

$operation="";

if (isset($_POST["operation"])) {	$operation=$_POST["operation"];}


while (true)
{
	//print_r($_POST);
	if (isset($goto)) {$operation=$goto;}
	
	switch ($operation)
	{
	case "Create":
		newuser($_POST["uname"],$_POST["pass"]);
		$goto="Connect";
		echo "<b>New user account successfully created<br></b>";
		break;

	case "remove":
		unaffectnewModule($_POST["uname"],$_POST["modname"], $_POST["modlogin"],$_POST["modpass"]);
		$goto="Connect";
		break;

	case "Addsubscribee":
		affectnewModule($_POST["uname"],$_POST["modname"], $_POST["modlogin"],$_POST["modpass"]);
		$goto="Connect";
		break;

	case "Connection":
		
		if (!(isset($_SESSION))) {session_start();}
		$_SESSION["All_in_one_modules"]=$_POST["links"];
		//print_R($_SESSION["All_in_one_modules"]);
		$_SESSION["LoginPortal"]=$_POST["uname"];
		$_SESSION["PassPortal"]=$_POST["pass"];

		//print_r($_SESSION);die();
		error_reporting(E_ALL);

		header("Location: ../core/view/".array_shift($_POST["links"]));

		$goto="Connect";
		break;

	case "Connect":


		$_SESSION["userportal"] =$_POST["uname"];
		$result = getModulesof($_POST["uname"],$_POST["pass"]);

		if (($result!="KO")) {
			
			$launchmodules="";

			$allinone="
				<div style=\"position:relative;left:40%;width:50%;top:20;\">
				<form name=allinone action=generisPortal.php method=post>
					<input type=hidden name=uname value=".$_POST["uname"].">
					<input type=hidden name=pass value=".$_POST["pass"].">
					<table border=0 class=\"divSideboxEntry\" cellpadding=3 cellspacing=3>
					<tr>
						<td>
							<div class=\"divLoginboxHeader\">Modules List</div>
							<table  border=0 cellpadding=3 cellspacing=3>";
			$allinone.="
							<tr>
								<td style=\"font-size:11px;color:black;font-family:Verdana;\">Module Name</td>
								<td width=65></td>
								<td style=\"font-size:11px;color:black;font-family:Verdana;\">Selection</td>
								<td></td>
							</tr>";
			
			foreach ($result as $k=>$specialization)
					{
					$heightsection = sizeOf($specialization)+1;
					$allinone.="
					<tr><td colspan=3><div class=\"textBlack\"><center><b>$k</b></center></div></td><td rowspan=".$heightsection."><img src=../core/view/icons/tao/".$k."_3DEffect.gif></td></tr>";


					foreach ($specialization as $key=>$val)
						{
						$link="index.php?bd=".$val[0]."&amp;uname=".$val[1]."&amp;pass=".$val[2]."&amp;datalg=XX&amp;function=1&amp;allinone=1";

						$allinone.="
						<tr><td style=\"font-size:11px;color:black;font-family:Verdana;\">$val[0]</td><td width=65></td><td><center><input CHECKED type=checkbox name=links[".$val[0]."] value=\"".$link."\" ></center></td></tr>";
						}
					}

		//	if ((sizeof($result)==1) && (sizeof($result["Misc"])==1)) {header("Location: ../view/".$index);}
			//Autmomatic conenction
			//$allinone.="<tr><td colspan=4></td><td><input type=hidden name=operation value=All-in-one><input type=submit class=\"HiddenBloc\" name=operation value=All-in-one></td></tr></table></div></form><script>document.forms[0].submit()</script>";

			$allinone.="<tr><td colspan=4></td><td><input type=hidden name=operation value=Connection>
			<input type=submit style=\"border: 1px solid silver;\" name=operation value=Connection></td></tr>
				</table>
				</td>
				</tr>
				</table>
			</form>
			</div>

			";



			$subscriptions="

			<div style=\"position:absolute;left:20;top:20;height:100%\">
			<img alt=\"Tao logo\" src=\"../core/view/icons/GENERIS_EN_cmyk_207x63px.gif\" />
			<br><div class=\"textBlack\"><div class=\"smalltext\">A joint initiative of CRP Henri Tudor and the University of Luxembourg</div></div>
			<div style=\"position:relative;top:10%;\">
			<FORM method=post action=generisPortal.php>
				<input type=hidden name=uname value=".$_POST["uname"].">
				<input type=hidden name=pass value=".$_POST["pass"].">
			<table border=0 class=\"divSideboxEntry\" cellpadding=3 cellspacing=3>
			<tr><td colspan=4><div class=\"divLoginboxHeader\">".$ext->name." portal settings</div></td></tr>
			<tr><td colspan=3><div class=\"textBlack\"><u>Install new module</u></div></td></tr>
			<tr><td>
				<ul><li><a href=".$ext->addScriptInstall.">".$ext->name." Module installation tool</a></ul></td></tr>

			<tr><td /></tr>

			<tr><td colspan=3><div class=\"textBlack\"><u>
			Add New subscription to an existing module</u></div></td></tr>




			<tr><td style=\"font-size:11px;color:black;font-family:Verdana;\">Module Name
				</td><td style=\"font-size:11px;color:black;font-family:Verdana;\"><input style=\"width: 100px; border: 1px solid #BA112B;\" name=modname size=15></td></tr>
			<tr><td style=\"font-size:11px;color:black;font-family:Verdana;\">Login
				</td><td style=\"font-size:11px;color:black;font-family:Verdana;\"><input style=\"width: 100px; border: 1px solid #BA112B;\" name=modlogin size=15></td></tr>
			<tr><td style=\"font-size:11px;color:black;font-family:Verdana;\">Module Pass
				</td><td style=\"font-size:11px;color:black;font-family:Verdana;\"><input style=\"width: 100px; border: 1px solid #BA112B;\" name=modpass size=15></td></tr><tr><td colspan=3></td><td><INPUT type=submit style=\"border: 1px solid silver;\" name=operation value=Addsubscribee></td></tr>



			<tr><td colspan=3><div class=\"textBlack\"><u>
			Modules subscriptions tool</u></div></td></tr>
			<tr><td><ul><li><a href=../install/membership.php>Modules subscriptions tool</a></ul></td></tr>




			</table>
			</FORM>

			</div>
			</div>

			";


			$output.=$allinone.$subscriptions;
			}
			else {$output.= "Bad login/password <br><br><center>If you want to create a new account on the portal, please click on \"Create\" button<br><br><a href=\"javascript:history.go(-1)\">&lt;--Back</a>";}
		break(2);

		default:
			$output.="
			<SCRIPT type=\"text/javascript\">
				var agree = confirm('This software is an exclusive release of the tao software delivered to identified project partners solely for testing and quality purposes. This software, still under development, is not meant to be publicly distributed. Please note that this software may include components that are distributed under the GNU General Public Licence. Once the tao software reaches a final release candidate status, its licence will fully comply with the latter.');

			if (agree)
			;
			else
			window.location=\"http://www.tao.lu/\";

			</script>


			";
			//$output.="<br><div class=\"Date\">";
			//$output.= (date("l d M Y")."   &nbsp;&nbsp;| &nbsp;&nbsp;");
			//$output.= (date("H : i : s"));
			//$output.="</div>";
			$output.= '<FORM action=generisPortal.php method=post>
			<center><br><br><br>
			<table class="divLoginbox" align=center cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td class="divLoginboxHeader" style="font-family:Verdana;border-bottom: #9c9c9c 1px solid;" align="center">Generis Portal</td>
			</tr>
			<tr >
			<td class="divSideboxEntry">

				<table  cellspacing="2" cellpadding="0" width="100%" border="0">
				<tr>

					<td colspan="4" align="left">
							<img width="200" height="1" src="../core/view/icons/spacer.gif" alt="spacer" />
					</td>
				</tr>
				<tr>

					<td rowspan="7">
						&nbsp;&nbsp;<img  logo" src="../core/view/icons/GENERIS_EN_cmyk_207x63px.gif" />

					</td>
					<td  colspan="3">

					</td>
				</tr>

				<tr>
					<td align="right"><div class="textBlack">'.USERNAME.'&nbsp;:</div></td>
					<td align="right"><input id="uname" name="uname" value="" style="width: 100px; border: 1px solid #BA112B;"></td>

					<td align="right">&nbsp;</td>
				</tr>
				<tr>
					<td align="right"><div class="textBlack">'.PASSWORD.'&nbsp;:</div></td>
					<td align="right"><input name="pass" type="password"  style="width: 100px; border: 1px solid #BA112B;"></td>
					<td>&nbsp;</td>
				</tr>
				<tr>

					<td colspan="4">&nbsp;</td>
				</tr>
						<tr><td /></tr><tr><td /></tr>
				<tr>
					<td colspan="2" align="right">
          <input type="submit" value="Connect" name="operation" style="display:none">
          <!-- to make the ENTER KEY "Connect" instead of "Create" -->
					<INPUT type="submit" name=operation value=Create style="border: 1px solid silver;">
					<input type="submit" value="Connect" name="operation" style="border: 1px solid silver;">
					</td>
						<td>&nbsp;</td>
				</tr>

				<tr>
					<td colspan="4" align="left">
					<div class="textBlack"><div class="smalltext">A joint initiative of CRP Henri Tudor and the University of Luxembourg</div></div>
					</td>
				</tr>

				</table>

				</td>
			</tr>
			</table>
			</center>
			</form>
      <script type="text/javascript">
        document.getElementById("uname").focus();
      </script>
      ';

		break(2);
	}
}

error_reporting(E_ALL);
//echo $log;
echo $output;

?>
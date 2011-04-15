<?php
//print_r($_GET); die();
/**
* Init generis frames, set posted data into session
* @package usergui
* @author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 1.1
*/

include("generis_utils.php");

if (!(isset($_SESSION)))
{
session_start();

if (!(isset($_SESSION["datalg"]))){$_SESSION["datalg"]="FR";}
if (!(isset($_SESSION["guilg"]))){$_SESSION["guilg"]="EN";}
if (!(isset($_SESSION["ClassInd"]))){$_SESSION["ClassInd"]="#c2";}
if (!(isset($_SESSION["filter"]))){$_SESSION["filter"]="1";}
if (!(isset($_SESSION["root"]))){$_SESSION["root"]="1";}
}



if (isset($_GET["do"])) 
	{
		$_SESSION["do"] = $_GET["do"];
		$_SESSION["param1"] = (isset($_GET["param1"])) ? $_GET["param1"] : "";
		$_SESSION["param2"] = (isset($_GET["param2"])) ? $_GET["param2"] : "";
		$_SESSION["param3"] = (isset($_GET["param3"])) ? $_GET["param3"] : "";
	}


$redirect=false;
if (isset($_GET["guilg"])) 
	{
		$_SESSION["guilg"] = $_GET["guilg"];
		$_SESSION["msg"]=GUILGCHANGED;
		$_SESSION["do"]="show";
		$_SESSION["param1"]=$_SESSION["lastly_shown_if_refresh"];
	}
loadGUIlanguage();

if (isset($_GET["bd"])) {$_SESSION["bd"]= $_GET["bd"];$redirect=true;}
if (isset($_POST["bdmodule"])) {$_SESSION["bd"]= $_POST["bdmodule"];}
if (isset($_GET["killsession"])) {$_SESSION["killsession"]= $_GET["killsession"];}

/*Navigation by pane : updates session*/
if ($_SESSION["do"]=="show") {	
	unset($_SESSION["admintree"]);
	unset($_SESSION["settings"]);
}


if (isset($_GET["generis_admin"])) {
	$_SESSION["generis_admin"]= $_GET["generis_admin"];
	if ($_GET["generis_admin"]=="stop") {unset($_SESSION["generis_admin"]);}
}

/*Files have been posted (used in case of test or item authoring)*/
if (isset($_FILES)) {
	$_SESSION["file"]= $_FILES;
	foreach ($_SESSION["file"] as $keyuz=>$valuz){
		if ($valuz["tmp_name"]!=""){
			$handle = fopen($valuz["tmp_name"],"rb");
			$temp2 = fread($handle,filesize($valuz["tmp_name"]));
			fclose($handle);
			$_SESSION["file"][$keyuz]["content"] = $temp2;
			}
			else {unset($_SESSION["file"]);}
		} 
	unset($_FILES);
	}

if (isset($_GET["removeall"])) {$_SESSION["removeall"]= $_GET["removeall"]; }
if (isset($_GET["filter"])) {$_SESSION["filter"]= $_GET["filter"];}
if (isset($_GET["root"])) {$_SESSION["root"]= $_GET["root"];}
if (isset($_POST["uname"])) {$_SESSION["uname"] = $_POST["uname"];}
if (isset($_POST["pass"])) {$_SESSION["pass"] = $_POST["pass"];}
if (isset($_GET["uname"])) {$_SESSION["uname"] = $_GET["uname"];}
if (isset($_GET["pass"])) {$_SESSION["pass"] = $_GET["pass"];}
if (isset($_GET["function"])) {$_SESSION["function"] = $_GET["function"];}
if (isset($_SESSION["lastly_shown_if_refresh"]) && !(isset($_SESSION["do"])) ) {
	$_SESSION["do"]="show";
	$_SESSION["param1"]=$_SESSION["lastly_shown_if_refresh"];
	}
if (isset($_GET["datalg"])) {
	$_SESSION["refreshlg"]=true;
	$_SESSION["datalg"] = $_GET["datalg"];$_SESSION["msg"]=DATALGCHANGED;
}
if (isset($_POST["datalg"])) {$_SESSION["datalg"] = $_POST["datalg"];$_SESSION["refreshlg"]=true;}
if (isset($_POST["function"])) {$_SESSION["function"] = $_POST["function"];}
if (isset($_POST["authinprogress"])) {$_SESSION["authinprogress"] = $_POST["authinprogress"];}
if (isset($_POST["rights"])) {$_SESSION["rights"] = $_POST["rights"];}
if (isset($_POST["range"])) {$_SESSION["range"] = $_POST["range"];}
if (isset($_POST["widget"])) {$_SESSION["widget"] = $_POST["widget"];}
if (isset($_POST["saveContent_x"])) {$_SESSION["saveContent"] = $_POST["saveContent_x"];}
if (isset($_POST["AddInquiry"])) {$_SESSION["AddInquiry"] = $_POST["AddInquiry_x"];}
if (isset($_POST["AddInquiry"])) {$_SESSION["AddInquiry"] = $_POST["AddInquiry"];}
if (isset($_POST["AddProp"])) {$_SESSION["AddProp"] = $_POST["AddProp"];}
if (isset($_POST["removeInquiry"])) {$_SESSION["removeInquiry"] = $_POST["removeInquiry"];}
if (isset($_POST["removeProposition"])) {$_SESSION["removeProposition"] = $_POST["removeProposition"]; }
if (isset($_POST["viewitem_x"])) {$_SESSION["viewitem"] = $_POST["viewitem_x"];}
if (isset($_POST["itemcontent"])) {$_SESSION["itemcontent"] = $_POST["itemcontent"];}
if (isset($_POST["testcontent"])) {$_SESSION["testcontent"] = $_POST["testcontent"];}
if (isset($_POST["EditUser_x"])) {$_SESSION["EditUser"] = $_POST["EditUser_x"];	$_SESSION["editanuser"] = $_POST["editanuser"]; }
if (isset($_POST["EditUser"])) {$_SESSION["EditUser"] = $_POST["EditUser"];	$_SESSION["editanuser"] = $_POST["editanuser"]; }
if (isset($_POST["AddGroupSubscriber_x"])) {$_SESSION["AddGroupSubscriber"] = $_POST["AddGroupSubscriber_x"];$_SESSION["editanuser"] = $_POST["editanuser"];}
if (isset($_POST["EditSubscribee_x"])) {$_SESSION["EditSubscribee"] = $_POST["EditSubscribee_x"];$_SESSION["editanuser"] = $_POST["editanuser"];}
if (isset($_POST["EditSubscriber_x"])) 
	{$_SESSION["EditSubscriber"] = $_POST["EditSubscriber_x"];
	$_SESSION["editanuser"] = $_POST["editanuser"];}
if (isset($_POST["AddSubscribee_x"])) {$_SESSION["AddSubscribee"] = $_POST["AddSubscribee_x"];$_SESSION["editanuser"] = $_POST["editanuser"];}
if (isset($_POST["addUser_x"])) {$_SESSION["addUser"] = $_POST["addUser_x"];$_SESSION["editanuser"] = $_POST["editanuser"];}
if (isset($_POST["AddSubscriber_x"])) {$_SESSION["AddSubscriber"] = $_POST["AddSubscriber_x"];$_SESSION["editanuser"] = $_POST["editanuser"];}
if (isset($_POST["RemoveSubscribee_x"])) {$_SESSION["RemoveSubscribee"] = $_POST["RemoveSubscribee_x"];$_SESSION["login"] = $_POST["login"];}
if (isset($_POST["RemoveSubscriber_x"])) {$_SESSION["RemoveSubscriber"] = $_POST["RemoveSubscriber_x"];$_SESSION["login"] = $_POST["login"];}
if (isset($_POST["removesubscribersgroup"])) {$_SESSION["removesubscribersgroup"] = $_POST["removesubscribersgroup"];}
if (isset($_POST["RemoveUser_x"])) {$_SESSION["RemoveUser"] = $_POST["RemoveUser_x"];$_SESSION["login"] = $_POST["login"];}
if (isset($_POST["removegroup"])) {$_SESSION["removegroup"] = $_POST["removegroup"];$_SESSION["login"] = $_POST["removegroup"];}
if (isset($_POST["instanceCreation"])) {$_SESSION["instanceCreation"] = $_POST["instanceCreation"];}
if (isset($_POST["Apply_Changes"])) {$_SESSION["Apply_Changes"] = $_POST["Apply_Changes"];}
if (isset($_POST["Apply_Changes_x"])) {$_SESSION["Apply_Changes"] = $_POST["Apply_Changes_x"];}

if (isset($_GET["ApplyChanges"])) {$_SESSION["Apply_Changes"] = $_GET["ApplyChanges"];}

/*Data for the authoring tool*/

if (isset($_POST["Authoring"])) {$_SESSION["do"]="Authoring";$_SESSION["param1"] = $_POST["Authoring"];}
if (isset($_POST["AuthoringT"])) {$_SESSION["do"]="AuthoringT";$_SESSION["param1"] = $_POST["AuthoringT"];}

if (isset($_POST["SHOWCLEAR"])) {
	if (isset($_SESSION["SHOWCLEAR"]))
		{
		$_SESSION["SHOWCLEAR"] = (!($_SESSION["SHOWCLEAR"])); 
		}
	else
		{
		$_SESSION["SHOWCLEAR"]=true;
		}
	}
//print_R($_SESSION);


/*
if (isset($_POST["overload"])) {$_SESSION["overload"] = $_POST["overload"];
								$_SESSION["Apply_Changes"] = "ApplyChanges";
								}
if (isset($_POST["unload"])) {$_SESSION["unload"] = $_POST["unload"];
								$_SESSION["Apply_Changes"] = "ApplyChanges";
								}
*/
$external="";$sizeleft="23";
if (isset($_POST["idsub"])) {$_SESSION["idsub"]= $_POST["idsub"];}
if (isset($_GET["external"])) {$external="?external=true";$sizeleft="35";}
if (isset($_POST["unaffiliate"])) {$_SESSION["unaffiliate"] = $_POST["unaffiliate"];$_SESSION["Apply_Changes"] = "ApplyChanges";}


if (isset($_POST["nameofgroup"])) {$_SESSION["nameofgroup"]= $_POST["nameofgroup"];}
if (isset($_GET["settings"])) {$_SESSION["settings"]= $_GET["settings"];unset($_SESSION["generis_admin"]);
if ($_GET["settings"]=="stop") {unset($_SESSION["settings"]);}
}

/*Authoring Model different from QCM */
if (isset($_POST["xml"])) {$_SESSION["xml"]= $_POST["xml"];}
if (isset($_POST["instance"])) {$_SESSION["instance"]= $_POST["instance"];}
if (isset($_POST["property"])) {$_SESSION["property"]= $_POST["property"];}
/*****Tocjeck*/
//if (isset($_GET["property"])) {$_SESSION["property"]= $_GET["property"];}

if (isset($_POST["nbinq"])) {$_SESSION["nbinq"]= $_POST["nbinq"];}
if (isset($_POST["nbprop"])) {$_SESSION["nbprop"]= $_POST["nbprop"];}
if (isset($_GET["allinone"])) {$_SESSION["allinone"]=$_GET["allinone"];}

$frame_all_in_one_header="";
$frame_all_in_one_percent="";
$frame_all_in_one_footer="";

if ($redirect) {header("Location: ./index.php");

} 


include("generis_ConstantsOfGui.php");
include_once('../../common/ext/loader/extension.php');
include_once('../../common/common.php');
/*
$ext = extension::getExtension();
$log = $ext->loadExtension(EXTENSION);
$_SESSION["ext"]=$ext;
*/




echo LIGHTHEAD;
echo '<body>
<div class="top">';
include ("generis_TopNavigationBar.php");
echo '</div>';

echo '
<div class="generis_WorkPane">';
echo '
<iframe frameborder="0" id="workpane" name="workpane" SRC="generis_WorkPane.php?PHPSESSID='.session_id().'" WIDTH="100%" HEIGHT="100%">
</iframe>';
echo '
</div>';
echo '

<div class="bottom">';
include ("generis_BotoomNavigationBar.php");
echo "
</div>";
?>

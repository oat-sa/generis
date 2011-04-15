<?php

/**
 * TAONavPane implements header frame with navigation lings among plugins and user informations 
 * TAONavPane also implements authentication call to kernel. 
 * @author patrick
 * @package usergui
 */
include_once("generis_ConstantsOfGui.php");
include_once("generis_authenticate.php");
include_once("generis_utils.php");

function getTab($strings)
{	 
	$return='
		<Table BORDER="0" CELLSPACING="0" CELLPADDING="0" width=100%>';
	if (isset($_SESSION["plugin"]))	{
		$actual =$_SESSION["plugin"];
	} 
	if (isset($_SESSION["admintree"]))	{$actual="2";}
	if 		((!(isset($_SESSION["admintree"]))) 
		&& 	(!(isset($_SESSION["settings"]))) 
		&& 	(!(isset($_SESSION["plugin"]))))	
	{
		$actual="0";
	}
	if (isset($_SESSION["settings"])) {
		$actual="1";
	}

	$return.="
	<tr height=26px>";	
/*
	if (sizeof($strings)<=2) {
		$return.="<td width=100></td>";
	}
*/

	$menu='
	<td valign="top" width=200px>
	<script language="JavaScript" src="../../includes/dhtmlxMenu/js/dhtmlXProtobar.js"></script><script language="JavaScript" src="../../includes/dhtmlxMenu/js/dhtmlXMenuBar.js"></script><script language="JavaScript" src="../../includes/dhtmlxMenu/js/dhtmlXCommon.js"></script>	
<div id="menu_zone" style="background-color:#f5f5f5;height:25px;"></div>


<SCRIPT type="text/javascript">
function OuvrirFenetre(url){
	var newwindow;
	newwindow = window.open (url,"_blank","toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=800,height=600,");

}	
		</SCRIPT>

<script>

function onButtonClick(itemId,itemValue)
{

	switch (itemId) {
	case \'CreateMetaClass\':top.workpane.pane.location=\'generis_UiControllerHtml.php?edit='.urlencode('http://www.w3.org/2000/01/rdf-schema#Class').'&what=AddsubClass\';break;
	case \'CreateClass\':top.workpane.pane.location=\'generis_UiControllerHtml.php?edit='.urlencode('http://www.w3.org/2000/01/rdf-schema#Class').'&what=AddInstance\';break;
	case \'CreateInstance\':top.workpane.pane.location=\'generis_UiControllerHtml.php?edit='.urlencode('http://www.w3.org/2000/01/rdf-schema#Ressource').'&what=AddInstance\';break;
	case \'CreateProperty\':top.workpane.pane.location=\'generis_UiControllerHtml.php?edit='.urlencode('http://www.w3.org/2000/01/rdf-schema#Ressource').'&what=AddProperty\';break;

	case \'SearchStruct\':top.workpane.pane.location=\'generis_UiControllerHtml.php?search='.urlencode('http://www.w3.org/2000/01/rdf-schema#Ressource').'\';break;
	case \'loadfile\':top.workpane.pane.location=\'generis_importfile.php\';break;
	case \'importfile\':top.workpane.pane.location=\'generis_hardimportfile.php\';break;
	case \'exportfile\':top.workpane.pane.location=\'generis_exportfile.php\';break;
	case \'loadM\':top.workpane.pane.location=\'generis_UiControllerHtml.php?loadM=on\';break;
	case \'mysettings\':top.location=\'index.php?settings=1\';break;
	case \'EditMetaClass\':OuvrirFenetre(\'generis_popupTree.php?root=&filter=c\');break;
	case \'EditClass\':OuvrirFenetre(\'generis_popupTree.php?root=&filter=c\');break;
	case \'EditInstance\':OuvrirFenetre(\'generis_popupTree.php?root=&filter=c\');break;
	case \'EditProperty\':OuvrirFenetre(\'generis_popupTree.php?root=&filter=c\');break;
	case \'MModules\':top.workpane.location=\'../../Portal/ManageModules.php\';break;
	case \'SearchText\':alert(\'Please use search text field at the bottom of page\');break;
	case \'discuss\':alert(\'Not implemented Yet\');break;
	case \'helptps\':OuvrirFenetre(\'../../middleware/docs/TAOuserguide.pdf\');break;
	case \'about\':OuvrirFenetre(\'generis_about.html\');break;

	default:alert(itemValue+itemId);top.location=itemValue;break;
}

};



</script>';

		$return.=$menu."</td><td class=red width=3000px></td>";
		//$return.="<td width=2px></td>";
		$return.="<td valign=top width=8%><div id=\"bmenu_zone\" style=\"background-color:#f5f5f5;height:25px;\"></div></td>";




		$return.="<td class=yellow align=\"center\">&nbsp;&nbsp;&nbsp;&nbsp;</td>";
		$return.="</tr></Table border=0>";

		$return.='<script>
			aMenuBar=new dhtmlXMenuBarObject(document.getElementById(\'menu_zone\'),\'100%\',22,"");
		aMenuBar.setOnClickHandler(onButtonClick);
		aMenuBar.setGfxPath("../../includes/dhtmlxMenu/img/");
		aMenuBar.loadXML("generis_xmlDataMenu.php");
		aMenuBar.showBar();

		bMenuBar=new dhtmlXMenuBarObject(document.getElementById(\'bmenu_zone\'),\'100%\',22,"");
		bMenuBar.setOnClickHandler(onButtonClick);
		bMenuBar.setGfxPath("../../includes/dhtmlxMenu/img/");
		bMenuBar.loadXML("generis_xmlMenu.php");
		bMenuBar.showBar();
		</script>';

		return $return;
	}


	function getParams()
	{
		$return='
		<table BORDER="0" CELLSPACING="0" CELLPADDING="3" >';
		$return.="
		<tr>
		<td width=45%></td><td ><span class=guiLabel>".MODULE."&nbsp;:</span></td><td ><span class=textI>[&nbsp;".$_SESSION["bd"]."&nbsp;]</span></td>";

		$return.="<td width=20 ></td><td ><span class=guiLabel>".USER." </span></td><td ><span class=textI>[&nbsp;".$_SESSION["cuser"]."&nbsp;]</span></td>
		</tr>";
		$return.="</table>";
		return $return;
	}


		$output='';
		//$output='';

		$output.='';


		if (!(isset($_SESSION))) {
			session_start();
		}
		//loadGUIlanguage();

		if ((isset($_SESSION["killsession"]))) 
		{
			$gui=$_SESSION["guilg"];
			$data=$_SESSION["datalg"];
			$bd=$_SESSION["bd"];
			$pass=$_SESSION["pass"];
			$function=$_SESSION["function"];
			$uname=$_SESSION["uname"];
			$loginportal=$_SESSION["LoginPortal"];
			$passportal=$_SESSION["PassPortal"];
			if (isset($_SESSION["All_in_one_modules"]))
			{
				$allinone= $_SESSION["All_in_one_modules"];

			}


			if (isset($_SESSION)) {
				session_destroy();
			}

			session_start();
			$_SESSION["All_in_one_modules"]=$allinone;
			$_SESSION["bd"]=$bd;
			$_SESSION["guilg"]=$gui;
			$_SESSION["datalg"]=$data;
			$_SESSION["pass"]=$pass;
			$_SESSION["function"]=$function;
			$_SESSION["uname"]=$uname;
			if (!(isset($_SESSION["datalg"]))){
				$_SESSION["datalg"]="FR";
			}
			if (!(isset($_SESSION["guilg"]))){
				$_SESSION["guilg"]="FR";
			}
			if (!(isset($_SESSION["index"]))){
				$_SESSION["index"]="#c2";
			}
			$_SESSION["show"]="c2";
			$_SESSION["filter"]="2";
			$_SESSION["ClassInd"]="#c2";
			$_SESSION["LoginPortal"]=$loginportal;
			$_SESSION["PassPortal"]=$passportal;
		}

				
		if ( ((isset($_SESSION["uname"]))))

		{							
		//SECTION APPEL WEB SERVICE
				
		set_include_path("../../");

		//creates the api instance
		core_control_FrontController::connect($_SESSION["uname"], md5($_SESSION["pass"]),$_SESSION["bd"]);
		$auth = calltoKernel('authenticate',array(array($_SESSION["uname"]),array($_SESSION["pass"]),array("2"),array($_SESSION["bd"])));
		if ($auth["pSession"][0]!="Authentication failed")
								{

								$_SESSION["msg"]=LOGINOK;
								$_SESSION["do"]="show";
								$_SESSION["param1"]="http://www.tao.lu/Ontologies/generis.rdf#generis_Ressource";
								$_SESSION["type"]="c";

								$_SESSION["session"]=$auth["pSession"];
								$_SESSION["cuser"]=$_SESSION["uname"];
								$_SESSION["connectedModules"]=array();
								//By default does not retrieve description (label and comment) of remote url
								$_SESSION["SHOWCLEAR"]=false;

								$deflg =calltoKernel('getMyDeflg',array($_SESSION["session"]));


								if ($_SESSION["datalg"]=="XX") {$_SESSION["datalg"]=$deflg["pOKorKO"];}

								$updsession =calltoKernel('setLG',array($_SESSION["session"],$_SESSION["datalg"]));
								$_SESSION["session"]=$updsession["pSession"];

								unset($_SESSION["uname"]);
								$_SESSION["ok"]=true;

								}

							else
								{
									$gui=$_SESSION["guilg"];
									$data=$_SESSION["datalg"];
									$bd=$_SESSION["bd"];

									//session_unset();
									//session_destroy();
									unset($_SESSION["uname"]);
									unset($_SESSION["pass"]);
									unset($_SESSION["function"]);
									session_start();
									$_SESSION["show"]="http://www.tao.lu/Ontologies/generis.rdf#generis_Ressource";
									$_SESSION["type"]="c";
									$_SESSION["bd"]=$bd;
									$_SESSION["guilg"]=$gui;
									$_SESSION["datalg"]=$data;
									if (!(isset($_SESSION["datalg"]))){$_SESSION["datalg"]="FR";}
									if (!(isset($_SESSION["guilg"]))){$_SESSION["guilg"]="FR";}
									if (!(isset($_SESSION["index"]))){$_SESSION["index"]="#c2";}

									$_SESSION["msg"]=LOGINKO;

								}

		}

	   if (isset($_SESSION["ok"])) 
	   {

		if (!($external))
		   {

		$moduletype = calltoKernel('getTypeModule',array($_SESSION["session"]));



		if (

				calltoKernel('isAdmin',array($_SESSION["session"]))) {$options[]=array('index.php?admintree=1',ADMIN);}
		   }
		   else
		   {$options = array(array('index.php?external=1',AFFILIATION));
		   }
error_reporting(0);

$tabs = getTab($options);


$params = getParams();
$ext = $_SESSION["ext"];
//error_reporting(E_ALL);
//echo $ext->logo;


//print_r($ext);
$output.='
<table width=100% border=0  CELLSPACING="0" CELLPADDING="0" >
<tr>
<td>
	<table width=100% border=0  CELLSPACING="0" CELLPADDING="0" >
	<tr>
	<td rowspan=2 width=165px  ><img src="./icons/Header_LogoGeneris.gif"/></td><td>
		<table border=0  CELLSPACING="0" CELLPADDING="0" >
			<tr><td width=30%></td><td>'.$params.'</td></tr>
		</table>	
	</td>
	</tr>
	<tr>
	<td valign=bottom>
		<table border=0  CELLSPACING="0" CELLPADDING="0" >
		<tr height=26px><td width="34px" class="aleftcorner" /><td >'.$tabs.'</td></tr>
		</table>	
	</td>
	</tr>
	</table>

</td>
</tr>
<tr height=1px><td /></tr>
';

 if (isset($_SESSION["PLUGIN_FULLNAME"]))
		   {
			$output.='<tr height=17px><td width=100% class=mediumbrown >
			<table border=0  style=background-color:rgb(231,229,211) CELLSPACING="0" CELLPADDING="0" >
			<tr width=100% >

			<td class=lightbrown width=58px></td>
			<td  class=lightbrown width=16px><img src=./icons/ContextualMenu_LeftCorner.gif align=top></td>
			<td  class=brown ><span class=function>&nbsp;&nbsp;'.$_SESSION["PLUGIN_FULLNAME"][0].'&nbsp;&nbsp;</span></td>';
			foreach ($_SESSION["PLUGIN_FULLNAME"][1] as $key=>$val)
			   {
			$output.='
			<td width=2px></td>	<td  class=red ><span class=function>&nbsp;&nbsp;<a style=color:white href='.$val[1].' target=pane>'.$val[0].'</a>&nbsp;&nbsp;</span></td>';


			   }
			  $output.='
			  </tr>
			  </table>
				</td></tr>';
			}
			else 
			{
$output.=	'
<tr height=17px>
<td width=100% >
	<table border=0  CELLSPACING="0" CELLPADDING="0" width=100% >
	<tr width=100% >
	<td class=lightbrown width=25%>&nbsp;&nbsp;&nbsp;</td>
	<td class=mediumbrown width=75% ></td>
	</tr>
	</table>
</td>
</tr>';
			}


$output.='
</table>';
}
echo $output;


?>

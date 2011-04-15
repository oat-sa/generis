<?php 
/**
* Main right pane, calls the controller which will analyse the request and replace the content with the right php file rendering
* @author patrick
* @package usergui
*/

include_once("generis_ConstantsOfGui.php");
include_once("generis_utils.php");

include("generis_UiController.php");

if (!(isset($_SESSION))) {session_start();}


loadGUIlanguage();
error_reporting("^E_NOTICE");
if (isset($_GET["do"])) 
	{
		$_SESSION["do"] = $_GET["do"];
		$_SESSION["param1"] = (isset($_GET["param1"])) ? $_GET["param1"] : $_SESSION["param1"];
		$_SESSION["param2"] = (isset($_GET["param2"])) ? $_GET["param2"] : $_SESSION["param2"];
		$_SESSION["param3"] = (isset($_GET["param3"])) ? $_GET["param3"] : $_SESSION["param3"];
	}

if (isset($_GET["add"])) {$_SESSION["new"]= $_GET["add"];}
if (isset($_GET["external"])) {$external=TRUE;} else {$external=FALSE;}
if (isset($_GET["type"])) {$_SESSION["type"]= $_GET["type"];}
if (isset($_GET["anottate"])) {$_SESSION["anottate"]= $_GET["anottate"];}


$TAOcurrentPane=new TAOPaneController();
$TAOcurrentPaneOutput=$TAOcurrentPane->getOutput($external,"");


echo HEAD.'
	<body class="paneIframe">
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
		<script type="text/javascript" src="./JS/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
		
	'.$TAOcurrentPaneOutput;

?>
 



















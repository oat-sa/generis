<?php
if (!(isset($_SESSION))) {session_start();}

echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<head>
<title>Generis Platform</title>
</head>
<body style="margin-top:0px;margin-left:0px;margin-right:0px;">';
error_Reporting("^E_NOTICE");

if ($_SESSION["do"]=="settings")
	{

include("generis_kernelController.php");

echo '<IFRAME frameborder=0  SRC="generis_UiControllerHtml.php" id="pane" name="pane" WIDTH="100%" HEIGHT="100%">If you can see this, your browser does not understand IFRAME.</IFRAME></body>';
	}
else
	{
echo '<IFRAME frameborder="0" id="tree" name="tree" SRC="generis_treeHTML.php" WIDTH="25%" HEIGHT="100%">If you can see this, your browser does not understand IFRAME.</IFRAME>';
echo '<IFRAME frameborder="0"  SRC="generis_UiControllerHtml.php" id="pane" scrollbar="true" name="pane" WIDTH="75%" HEIGHT="100%">If you can see this, your browser does not understand IFRAME.</IFRAME></body>';
	}

?>

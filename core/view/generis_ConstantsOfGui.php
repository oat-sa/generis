<?php
/*

*/
/**
* User Gui constants for HTML generation
* @author patrick
* @package usergui
*/
define("TABLEHEADER", '
						<table class=generisTable cellpadding=4 cellspacing=4 border=0 >

					 ', true);
define("TABLEHEADER2", '
						<table cellpadding=1 cellspacing=1 border=0 >

					 ', true);

define("TABLEFOOTER", '</table>', true);

define("HEAD", '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<head>
<title>Generis Platform</title>
<LINK media=all href="./CSS/getCSS.php" type="text/css" rel=stylesheet>


<SCRIPT type="text/javascript">
		function OuvrirFenetre(url){
		var newwindow;
		newwindow = window.open (url,"_blank","toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=800,height=600,");




		}
</SCRIPT>

<script type="text/javascript">
  _editor_url = "./HTMLArea-3.0-rc1/";
  _editor_lang = "en";

</script>

<script type="text/javascript" src="./HTMLArea-3.0-rc1/htmlarea.js"></script>

<script type="text/javascript">
      HTMLArea.loadPlugin("ImageManager");
	  //HTMLArea.loadPlugin("SoundManager");
    // tells us that File does not exist:
    // /var/www/generis/core/view/HTMLArea-3.0-rc1/plugins/SoundManager/sound-manager.js
    // it will no more add error messages to the Apache log
      HTMLArea.loadPlugin("CSS");
      HTMLArea.loadPlugin("ContextMenu");
</script>



<link REL="SHORTCUT ICON" HREF="favicon.ico">
</head>
', true);



define("LIGHTHEAD", '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<head>
<title>Generis Platform</title>
<LINK media=all href="./CSS/getCSS.php" type="text/css" rel=stylesheet>
<link REL="SHORTCUT ICON" HREF="favicon.ico">
</head>
', true);


header('Content-Type: text/html; charset=UTF-8');
?>

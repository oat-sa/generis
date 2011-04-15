<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
if (!(isset($_SESSION)))
{
session_start();
}
require_once("generis_ConstantsOfGui.php");		   
require_once("generis_utils.php");

	calltoKernel('setStatement',array($_SESSION["session"],$_POST["addlg"],$_POST["addlg"],$_POST["addlg"],"r",$_POST["addlg"],"","r"));

	header("Location: index.php");
?>
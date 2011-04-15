<?php
include_once("../view/generis_ConstantsOfGui.php");
include_once("../view/generis_utils.php");
require_once("../view/generis_tree.php");
if (!(isset($_SESSION))) {session_start();}

if (isset($_POST["sequenceid"])) 

	{
	
	if ($_POST["sequenceid"]=="") {;} else 
		calltoKernel('removeInstance',array($_SESSION["session"],array(urldecode($_POST["sequenceid"]))));
		//new sequence definition
			
			$sequence = array_flip($_POST["sequence"]);
			ksort($sequence);
			unset($sequence["-1"]);
			
			$result = calltoKernel('setSequence',array($_SESSION["session"],$sequence));
		
		
		$result = calltoKernel('setSequence',array($_SESSION["session"],$sequence));
	$result = calltoKernel('editPropertyValuesforInstance',array($_SESSION["session"],array(urldecode($_POST["ressource"])),array(urldecode($_POST["property"])),array(""),array($result)));
	echo "<script>window.close();</script>";die();
	}





$widget ='<head>

<LINK media=all href="../view/CSS/generis_default.css" type=text/css rel=stylesheet><body class=paneIframe><div style=position:absolute;left:100px;>';





error_Reporting(E_ALL);
$TAOtree = new TAOtree();
$Treeoutput=$TAOtree->getOutput(TRUE,"","",urldecode($_GET["range"]));

$result = calltoKernel('getInstances',array($_SESSION["session"],array(urldecode($_GET["range"])),array(""),false,true));
$listinstances=$result["pDescription"][0];

$ressourceDescription = calltoKernel('getressourceDescription',array($_SESSION["session"],urldecode($_GET["value"]),array("")));



/**
*returns html output of the combobox displaying all availables sequence numbers with $anElement selected (if part of the sequence) according to the sequence $sequenceDescription
*/
function getCombobox($sequenceDescription,$anElement)
	{
		$cb = "<select name=sequence[".$anElement."]><option value=-1> ";
		$i=1;
		//sic.....
		while ($i<=10)
			{
				//TODOIF part of sequence select ....
				
				$ok =false;
					foreach ($sequenceDescription["properties"] as $key=>$val)
						{
							if ($val["PropertyKey"]=="http://www.w3.org/1999/02/22-rdf-syntax-ns#_".$i)
								{
								if ($val["PropertyValue"]==$anElement)
									{$ok=true;}
								}
						}
				if ($ok) $cb .="<option SELECTED value=".$i.">".$i; else
				$cb .="<option value=".$i.">".$i;
				$i++;
			}
		$cb .="</select>";
		return $cb;
	}


$widget.="<form action=UIseq.php method=post><input type=hidden name=sequenceid value=".$_GET["value"]."><input type=hidden name=ressource value=".$_GET["instance"]."><input type=hidden name=property value=".$_GET["property"].">";
foreach ($listinstances as $keyi=>$vali)
{
	
	$combo = getCombobox($ressourceDescription,$vali["InstanceKey"]);
	
	$widget.=$combo.$vali["InstanceLabel"].'<BR>';
		
}
$widget.="<br><br><input type=submit name=getit value=Apply style=\"border: 1px solid silver;\"></form></div>";
echo $widget;




?>														
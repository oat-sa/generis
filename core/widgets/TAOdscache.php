<?php
include_once("../view/generis_ConstantsOfGui.php");
include_once("../view/generis_utils.php");
if (!(isset($_SESSION))) {session_start();}
if (isset($_POST["instance"]))

	{
		
		
		$result = calltoKernel('getRDFfromaremotemodule',array($_SESSION["session"],"",
			array($_POST["module"])
			, true	
		));
		
		$result = calltoKernel('editPropertyValuesforInstance',array($_SESSION["session"],array(urldecode($_POST["instance"])),array(urldecode($_POST["property"])),array(""),array($result["pSession"][1])));
		echo "<script>window.close();</script>";die();
	}
$instance =  (urldecode($_GET["instance"]));
$idproperty =  (urldecode($_GET["property"]));
$shortidproperty = substr($idproperty,strpos($idproperty,"#")+1);



switch ($shortidproperty)
{
case "113282206937612": 
	$gbtype="http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject";break;

case "113282209557150": 
	$gbtype="http://www.tao.lu/Ontologies/TAOGroup.rdf#Group";break;	
case "11328221215458": 
	$gbtype="http://www.tao.lu/Ontologies/TAOTest.rdf#Test";break;	
case "113282215755118": 
	$gbtype="http://www.tao.lu/Ontologies/TAOItem.rdf#Item";break;

}


$idsub = calltoKernel('getSubscribeesurl',array($_SESSION["session"],array($gbtype),array("")));
$output ='<head>

<LINK media=all href="../view/CSS/generis_default.css" type=text/css rel=stylesheet><body class=paneIframe><center>';
foreach ($idsub as $k=>$v)
	{
		$output.= "<br><br><form action=TAOdscache.php method=post><input type=hidden name=instance value=".urlencode($instance).">
		<input type=hidden name=property value=".urlencode($idproperty).">
			<input type=hidden name=module value=".$v[0].">
		<input type=submit name=getit value=Select style=\"border: 1px solid silver;\">&nbsp;&nbsp;&nbsp;".$v[4]." (".$v[3].") </form>";
	}
echo $output;
?>
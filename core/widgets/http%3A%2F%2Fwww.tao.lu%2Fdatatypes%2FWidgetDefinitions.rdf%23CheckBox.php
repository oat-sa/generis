<?php
if ((!(isset($val["PropertyValue"]))) or (sizeOf($val["PropertyValue"])==0)) {$val["PropertyValue"]=array("");}
$widget="";
$widget.='<input type=hidden name="instanceCreation[properties]['.$id.'][]" value="NULL">';
$result = getInstances($_SESSION["session"],array($val["PropertyRange"]),array(""),false,true);
$listinstances=$result["pDescription"];

foreach ($listinstances as $keyi=>$vali)
{
	
	
	if  ((in_array($vali["InstanceKey"],$val["PropertyValue"])) or (in_array("http://10.13.1.225/middleware/taoqual.rdf".$vali["InstanceKey"],$val["PropertyValue"])))
		{
		$widget.='<input type=checkbox CHECKED value="'.$vali["InstanceKey"].'" name="instanceCreation[properties]['.$id.'][]">'.$vali["InstanceLabel"].'
		<BR>';
		}
		else {
		$widget.='<input type=checkbox  value="'.$vali["InstanceKey"].'" name="instanceCreation[properties]['.$id.'][]">'.$vali["InstanceLabel"].'
		<BR>';
		}
}
?>														
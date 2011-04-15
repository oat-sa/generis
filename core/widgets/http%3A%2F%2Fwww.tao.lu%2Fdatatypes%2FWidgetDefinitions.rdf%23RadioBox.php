<?php
if ((!(isset($val["PropertyValue"]))) or (sizeOf($val["PropertyValue"])==0)) {$val["PropertyValue"]=array("");}
$widget="";			
$result = getInstances($_SESSION["session"],array($val["PropertyRange"]),array(""),false,true);
$listinstances=$result["pDescription"];
error_reporting("^E_NOTICE");

//$selectedValue=array_pop($val["PropertyValue"]);

foreach ($listinstances as $keyi=>$vali)
	{
	
		if  ((in_array($vali["InstanceKey"],$val["PropertyValue"])) or (in_array("http://10.13.1.225/middleware/taoqual.rdf".$vali["InstanceKey"],$val["PropertyValue"])))
		{	
			$widget.='
			<input type=radio CHECKED value="'.$vali["InstanceKey"].'" name="instanceCreation[properties]['.$val["PropertyKey"].'][]">&nbsp;'.strip_tags($vali["InstanceLabel"]).'</input>
			<br />';
	}

	else {											
		
			$widget.='
			<input type=radio  value="'.$vali["InstanceKey"].'" name="instanceCreation[properties]['.$val["PropertyKey"].'][]">&nbsp;'.strip_tags($vali["InstanceLabel"]).'</input>
			<br />';
		}

	}			
										
?>																								
<?php
if ((!(isset($val["PropertyValue"]))) or (sizeOf($val["PropertyValue"])==0)) {$val["PropertyValue"]=array("");}
error_reporting(0);
$widget= '
<SELECT name="instanceCreation[properties]['.$val["PropertyKey"].'][]">';
$result = getInstances($_SESSION["session"],array($val["PropertyRange"]),array(""),false,true);
	
	$listinstances=$result["pDescription"];
			
			
			
			foreach ($listinstances as $keyi=>$vali)
								{
									//In case of instance creation, no values are defined for the property, error_reporting is set to 0 to prevent notice messages
									
									if  (in_array($vali["InstanceKey"],$val["PropertyValue"]))
												{
													$widget.='
													<option value='.$vali["InstanceKey"].' selected>'.strip_tags($vali["InstanceLabel"]).'</option>';
												}

												else {											
												$widget.='
												<option value='.$vali["InstanceKey"].'>'.strip_tags($vali["InstanceLabel"]).'</option>';
													}

												}
							$widget.= '
							</SELECT>
							';
?>														
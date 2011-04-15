<?php
if ((!(isset($val["PropertyValue"]))) or (sizeOf($val["PropertyValue"])==0)) {$val["PropertyValue"]=array("");}
$widget= '<SELECT Multiple name="instanceCreation[properties]['.$id.'][]">';
							$result = calltoKernel('getInstances',array($_SESSION["session"],array($val["PropertyRange"]),array(""),false,true));
							$listinstances=$result["pDescription"][0];
							foreach ($listinstances as $keyi=>$vali)
												{
												if  (in_array($vali["InstanceKey"],$val["PropertyValue"]))
												{
													$widget.='<option value='.$vali["InstanceKey"].' selected>'.$vali["InstanceLabel"].'</option>';
												}

												else {											
												$widget.='<option value='.$vali["InstanceKey"].'>'.$vali["InstanceLabel"].'</option>';
													}

												}
							$widget.= '</SELECT>';
?>														
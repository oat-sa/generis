<?php

if ((!(isset($val["PropertyValue"]))) or (sizeOf($val["PropertyValue"])==0)) {$val["PropertyValue"]=array("");}
							
							
							foreach ($val["PropertyValue"] as $idoftriple=>$avalue)
							{
 							$widget= '<input size=50 type=password name=instanceCreation[properties]['.$id.']['.$idoftriple.'] value="'.$avalue.'"><BR>';
							}
?>														
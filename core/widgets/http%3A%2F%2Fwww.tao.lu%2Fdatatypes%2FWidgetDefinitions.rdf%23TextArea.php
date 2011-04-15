<?php

if ((!(isset($val["PropertyValue"]))) or (sizeOf($val["PropertyValue"])==0)) {$val["PropertyValue"]=array("");}
							foreach ($val["PropertyValue"] as $idoftriple=>$avalue)
							{
							$widget= '<textarea cols=50 type=text name=instanceCreation[properties]['.$id.']['.$idoftriple.']> '.str_replace('&#180;','\'',$avalue).'</textarea><BR>';
							}
?>														
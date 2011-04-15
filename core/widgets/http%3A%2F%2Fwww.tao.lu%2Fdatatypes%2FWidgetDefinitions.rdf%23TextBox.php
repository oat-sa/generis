<?php

if ((!(isset($val["PropertyValue"]))) or (sizeOf($val["PropertyValue"])==0)) {$val["PropertyValue"]=array("");}
							
	//$numericid : Id of triple in the knowledge base
	foreach ($val["PropertyValue"] as $idoftriple=>$avalue)
			{
 			$widget= '<input size=50 type=text name=instanceCreation[properties]['.$val["PropertyKey"].']['.$idoftriple.'] value="'.$avalue.'"><BR>';
			}
/*<input type=submit name=overload['.$val["PropertyKey"].']['.$avalue.'] value=+><input type=submit name=unload['.$val["PropertyKey"].']['.$avalue.'] value=->*/

?>							
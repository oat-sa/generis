<?php

if ((!(isset($val["PropertyValue"]))) or (sizeOf($val["PropertyValue"])==0)) {$val["PropertyValue"]=array("");}
$idhtml=rand(0,65535);
	

	/*
	* @about $idoftriple 
	* @comment 
	When editing values assigned to a property for one instance,
	In generis , the distinction is made between  multiple (property - value) and property - multiple values. 
	ex :
	Subject firstname Luc, Subject firstname Pierre
	<>
	Group hasmembers Leopold, Group hasmembers Roger 

	For both cases, ther are multiple statements with id in the knowledge base

	For edition, 
	
	In the first case, the widget for the property is a unique-value widget (TextBox, TextArea) thus it will appear several times, in the second case , the widget is checkboxes, listboxes, it appears one time (a set of checkboxe for one value composed of a selection of checkboxes). At statements level, both solutions are stored in the same way.
	 But it is slightly differently done in the second case, if you unset the firstcheckbox and sets the second one , we edit the statement specific to the second checkbox (or add it if not set yet, would require assert rights), and we removed the statement specific to the selection of the checkbox 

	
	To store the edition made,

	In the first case, the tripleid is used to edit the right statment. if there was several tuple [property value], all statements are edited thanks to the id. In the second case, For one assignation of the property (several values, but one widget), all previous values are removed , and the selection is set. Because from the point of view of statement, unselecting one of the checkboxes matches more with a statement removal rather than an edition of this statements. Thus the user will need removal rights on this kind of property assignation.
	*/
	foreach ($val["PropertyValue"] as $idoftriple=>$avalue)
		{
		$widget= '<TEXTAREA id='.$idhtml.' cols=110 rows=6 type=text name=instanceCreation[properties]['.$val["PropertyKey"].']['.$idoftriple.']>'.$avalue.'</TEXTAREA><BR><script language="javascript" type="text/javascript" defer="1">HTMLArea.replace('.$idhtml.');</script>';
		}

?>														
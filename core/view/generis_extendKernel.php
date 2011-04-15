<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* Functions used to specialize tao kernel 
* Those functions read the tao ontology to retrieve available tao specialisations and informations 
* linked like specific widgets to use, etc.
* @package usergui
* @author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 1.1
*/



/**
* returns Module type
*@return string moduletype
*/

function getTypeOfModule()
{
	if (!(isset($_SESSION)))
					{session_start();}
	
	$return =calltoKernel('getTypeModule',array($_SESSION["session"]));
	return $return;
}

/**
*
* returns specific ranges available for a property according to the specialisation of the module 
* used by taomodulesselection gui and properties management
*Resources defined in a module may be realted to resources defined in other modules of a specific *type, for instance, in the e-testing context, tests definied in a test module have relations with *items defined in item module, this function returns available range for a specific type
*@param string moduletype
*/
function getRangesofProperties()
{
	$moduletype = getTypeOfModule();
	//echo $moduletype;
	/*Temp., normally retrieves TAO ontology, and searches for relations of this module type*/
	switch ($moduletype)
	{
		case "subject" : {return array();break;}
		case "group" : {return array(array("Subjects","http://www.tao.lu/tao.rdfs#TAOSubjects","subject"), array("tests","http://www.tao.lu/tao.rdfs#TAOTests","test"));break;}
		case "item" : {return array();break;}
		case "test" : {return array(array("Items","http://www.tao.lu/tao.rdfs#TAOItems","item"));break;}
		case "Result" : {return array(array("Subjects","http://www.tao.lu/tao.rdfs#TAOSubjects","subject"), array("Tests","http://www.tao.lu/tao.rdfs#TAOTests","test"),array("Groups","http://www.tao.lu/tao.rdfs#TAOGroups","group"), array("Items","http://www.tao.lu/tao.rdfs#TAOItems","item"));break;}
		default: return array();
			
	} 
	
	return array();
}

/**
*returns specific widgets for a moduletype
*/


function getWidgets()
{
	$moduletype = getTypeOfModule();
		
	/*Temp., normally retrieves TAO ontology, and searches specific widget for this module type*/
	switch ($moduletype)
	{
		case "subject" : {return array();break;}
		case "group" : {return array();break;}
		case "item" : {return array(array("Item Authoring","http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Authoring"));break;}
		case "test" : {return array(array("Test Authoring","http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TAuthoring"));break;}
		case "Result" : {return array();break;}
		default: return array();
			
	} 
	
	return array();
}

?>
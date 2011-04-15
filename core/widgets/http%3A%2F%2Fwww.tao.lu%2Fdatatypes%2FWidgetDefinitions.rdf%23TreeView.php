<?php
//If no values have been assigned for this property ($val["PropertyKey"]) an empty array is set to prevent looping in an empty array raising notices
if ((!(isset($val["PropertyValue"]))) or (sizeOf($val["PropertyValue"])==0)) {$val["PropertyValue"]=array("");}

$selected = '';
//initializes the tree.js with global variables containing the idproperty (A form may contain several trees for different properties)
$selected='
	<script language="JavaScript">
	var idproperty=\''.$id.'\';
	var urlicons=\'\';
	var selected=[';

//this loops among defined propertyvalues and builds a javascript list containg all values for this property,
// used by the js tree to "pre" select already checked values for this property in the tree
foreach ($val["PropertyValue"] as $numeric=>$avalue)
		{
		$selected.="'".urlencode($avalue)."',";
		}
//removes the trailing comma
$selected=substr($selected,0,strlen($selected)-1);
//closes the javascript list
$selected.="];</script>";

// new AjaxTree connected with new API still need to be fully tested 
// remove for PIACC

//$api =  core_kernel_impl_ApiModelOO::singleton();
//$api->logIn($_SESSION["cuser"],md5($_SESSION["pass"]),$_SESSION["bd"],true);
//$tree = $api->getResourceTree("http://www.w3.org/2000/01/rdf-schema#Class",3);
//$ajaxTree = new core_view_classes_AjaxTree(true);
//$ajaxTree->setSelectedNode(array_values($val["PropertyValue"]));
//$ajaxTree->setExpanded(false);
//$Treeoutput = $ajaxTree->getAjaxTree($tree) ;

//The tree will send back the newly selected values using post variable similar to this (the form  and action is already  managed at the level of resource edition)

//example of posted data when submitted , similar to all other kind of widgets ivolved in the built form :
//		value=" (URI of selected resource ) http://www.tao.lu/Ontologies/generis.rdf#True"
//		name="instanceCreation[properties][(idproperty)http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent][]">

//


////includes olf generis_tree.php
//include_once("generis_tree.php");
$TAOtree = new TAOTree();
//defines the root for the tree, in this case the range of the property
$Treeoutput=$TAOtree->getOutput(TRUE,"","",$val["PropertyRange"]);

//add the widget to the field of the form
$widget = $selected.$Treeoutput.'<!--<a target=_new href=generis_importfile.php>&nbsp;<b><i><u>Load model</i></b></u></a>--> ';



							
?>														
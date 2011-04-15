
<?php 
//include("../../gui_constants.php");
//require("../../functions.php");
//require("../../../GenerisKernel/UItree.php");
require("../../../taoAPI.php");
if (!(isset($_SESSION))) {session_start();}

//globals
$pGenNS = array("http://www.tao.lu/Ontologies/generis.rdf");
$pGenRes = array("#geneAll_Ressource");
$pVoid = array("");
$pBMOeltID = array("#113706627424288");
$pSession = $_SESSION["session"];
$userModelURI = getNS($pSession);



// interface
print '<html>
<head>
<link media="all" href="../../CSS/generis_default.css" type="text/css" rel="stylesheet">
<link media="screen" href="requetes.css" type="text/css" rel="stylesheet">
<script src="js/prototype.js" type="text/javascript"></script>
<script src="js/scriptaculous.js" type="text/javascript"></script>
<link rel="shortcut icon" href="favicon.ico">
</head>

<body class="paneIframe"><ul>';


//=========================================================================
// serverModule.php instance creation functions.

// create new instances of #113707105956542 (Demand)
print "</li><li>just created instance of Demand with id: "; 
//setInstance return array if ok, and FALSE if an error occured (eg at mysql level)
$insts = setInstance($pSession,array($_SESSION["datalg"]),array("my new instance"),array("no comment"),array("#113707105956542"));
print($insts?$insts["pOKorKO"]:"KO");
print "</li><li>give it a property: "; 
//give a "feature" property related to "my feature", lang is set to pVoid for this property.
//editPropertyValuesforInstance return array with # of the property values.
$prop = editPropertyValuesforInstance($pSession,array($insts["pOKorKO"]),array("http://gorgonzola:82/middleware/businessmodel.rdf#113707119936032"),$pVoid,array("http://gorgonzola:82/middleware/businessmodel.rdf#113707178760146"));
print(($prop&&$prop["pOKorKO"]>0)?"OK":"KO");
print "</li><li>delete this instance: "; 
// delete the newly created instances of #113707105956542 (Demand)
//setInstance return array if ok, and FALSE if an error occured (eg at mysql level)
$prop = removeInstance($pSession,array($insts["pOKorKO"]));
print($prop?"OK":"KO");






//=========================================================================
// RDQL query engine

define('RDFAPI_INCLUDE_DIR', $_SERVER['DOCUMENT_ROOT'].'/generis/generis/RDFLayer/rdfapi-php/api/');	
define('RDFSAPI_INCLUDE_DIR', $_SERVER['DOCUMENT_ROOT'].'/generis/generis/RDFSLayer/rdfsapi/');	
define('OWLAPI_INCLUDE_DIR', $_SERVER['DOCUMENT_ROOT'].'/generis/generis/OWLLayer/owlapi/');	
			
include_once(RDFSAPI_INCLUDE_DIR."rdfsapi.php");
include_once(OWLAPI_INCLUDE_DIR."owlapi.php");
include_once(RDFAPI_INCLUDE_DIR."syntax/RdfSerializer.php");
error_reporting(E_ALL);
$a = new DBStore(ADODB_DB_DRIVER,ADODB_DB_HOST,$_SESSION["bd"],ADODB_DB_USER,ADODB_DB_PASSWORD);

print "<li>vars: driver: ".ADODB_DB_DRIVER.", host: ".ADODB_DB_HOST.", db: ".$_SESSION["bd"].", usr: ".ADODB_DB_USER.", pwd: ".ADODB_DB_PASSWORD.".";

  if($a==null){ print "</li><li>a is null.";
	 }  else { print "</li><li>a is not null. "; //print_r($a);
   } 
   if (is_a($a, "DBStore")) { print "</li><li>a is DBStore.";
	 } 
   if (is_a($a, "POWLStore")) { print "</li><li>a is POWLStore.";
	 }


//$queryString = 'SELECT ?x  WHERE (?x,  <http://www.w3.org/1999/02/22-rdf-syntax-ns#type>, <http://www.w3.org/2000/01/rdf-schema#Class>)';
/* $queryString = 'SELECT ?label , ?class
WHERE  
  (?class,  <http://www.w3.org/1999/02/22-rdf-syntax-ns#type>, <http://www.w3.org/2000/01/rdf-schema#Class>)
  (?class  rdfs:label ?label)
AND  ?label =~ "/er/"
USING  rdfs FOR <http://www.w3.org/2000/01/rdf-schema>';
*/   // TODO the above query only gets direct instances... see for indirect should it matter, ?with checked box?.
$queryString = 'SELECT ?label, ?ins WHERE 

(?ins, rdf:type, <http://127.0.0.1/middleware/menfpSubjects.rdf#113803054853708>) 
(?ins, <http://www.w3.org/1999/02/22-rdf-syntax-ns#type>, <http://127.0.0.1/middleware/menfpSubjects.rdf#113803054853708>) 
(?ins, <http://www.w3.org/2000/01/rdf-schema#label>, ?label) 

USING rdf FOR <http://www.w3.org/1999/02/22-rdf-syntax-ns> 
rdfs FOR <http://www.w3.org/2000/01/rdf-schema> 
xsd FOR <http://www.w3.org/2001/XMLSchema> 
gen FOR <http://www.tao.lu/Ontologies/generis.rdf> 
wid FOR <http://www.tao.lu/datatypes/WidgetDefinitions.rdf>  ';
//the above selects the direct instances of Teachers

print "</li><li>list of available models: ";
$list = $a->listModels();
print_r($list);
/* print "<ul>";
// Show the database contents
foreach ($list as $model) {    print "<li>userModelURI: " .$model['userModelURI'] ." -- baseURI : " .$model['baseURI'] ."</li>"; }
print "</ul>"; */ 
if (!($mod=$a->getModel($userModelURI))){ 
   print "</li><li>ERROR: I don't know model $userModelURI!";
} else {
    $qres=$mod->rdqlQuery($queryString, FALSE);
    print "</li><li>query results: ";
    print_r($qres);
}

error_reporting(E_ALL);
set_time_limit(500);
//$a->loadModel("http://www.tao.lu/datatypes/WidgetDefinitions.rdf#","http://www.tao.lu/datatypes/WidgetDefinitions.rdf");





//=========================================================================
// serverModule.php consultation function.

//get list of (indirect) instances of #113707105956542 (Demand) or  http://www.w3.org/2000/01/rdf-schema#Class
print "</li><li>target instances: "; 
$insts = getInstances($pSession,array("#113706627424288"),$pVoid,false,true); //last true to include indirect instances
print_r($insts);

// get list of properties of #113706627424288
print "</li><li>AllProperties: KO?"; 
$props = getAllProperties($pSession,"#113706627424288",$pVoid); // TODO KO?
print_r($props);

//TODO will getResDesc only look for subClasses of generis_resource? (would prefer not)
print "</li><li>ResourceProperties: "; 
$props = getRessourceDescription($pSession,"#113706627424288","");
print "<ol>";
foreach ($props["relatedproperties"] as $prD)
	print '<li id="'.$prD["PropertyKey"].'">'.$prD["PropertyLabel"].' <span class="range informal">(points to a '.$prD["PropertyRange"].')</span></li>';
print "</ol>";
print '</li><li>ResourceDescription: <span style="font-size:60%">';
print_r($props);

print '</span></li><li>ClassDescription: <span style="font-size:60%">'; 
$props = getClassDescription($pSession,array("#113706627424288"),$pVoid);
print_r($props);
print "</span></li>";

print '</span></li><li>indirect subClasses: <span style="font-size:60%">'; 
$props = getIndirectsubClasses($pSession,"#113706627424288",$pVoid);
print_r($props);
print "</span></li>";

print '</span></li><li>Session: <span style="font-size:60%">'; 
print_r($_SESSION);
print "</span></li>";

print"</ul>";






?>
</body>
</html>
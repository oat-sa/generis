<?php 
/**
This backend provides all necessary methods for running RDQL queries on user module.
It detects the parameter's name in $_POST and behaves accordingly.

author: BGr
date: 20060117
version: 0.9
*/

require("../../../taoAPI.php");

define('RDFAPI_INCLUDE_DIR', $_SERVER['DOCUMENT_ROOT'].'/generis/generis/RDFLayer/rdfapi-php/api/');	
define('RDFSAPI_INCLUDE_DIR', $_SERVER['DOCUMENT_ROOT'].'/generis/generis/RDFSLayer/rdfsapi/');	
define('OWLAPI_INCLUDE_DIR', $_SERVER['DOCUMENT_ROOT'].'/generis/generis/OWLLayer/owlapi/');	
			
include_once(RDFSAPI_INCLUDE_DIR."rdfsapi.php");
include_once(OWLAPI_INCLUDE_DIR."owlapi.php");
include_once(RDFAPI_INCLUDE_DIR."syntax/RdfSerializer.php");

unset($_SESSION["queryEnv"]);

if (!(isset($_SESSION))) {
	session_start();
	unset($_SESSION["queryEnv"]);
}

//globals
$pVoid = array("");
$pSession = $_SESSION["session"];
$modelURI = getNS($pSession);
if (!(isset($_SESSION["queryEnv"]))) {
	$store = new DBStore(ADODB_DB_DRIVER,ADODB_DB_HOST,$_SESSION["bd"],ADODB_DB_USER,ADODB_DB_PASSWORD);
	if (!($mod=$store->getModel($modelURI))){
		return "ERROR: I don't know model $modelURI!";
	} 
	$_SESSION["queryEnv"]="configured";
}

//detect _POST parameters
if (array_key_exists("op", $_POST)) {
		switch ($_POST["op"]) {
			case "build":
				print buildAndRunQuery($_POST["cURI"], $_POST["propURI"], $_POST["operTxt"], $_POST["valTxt"], $_POST["ulID"]);
				break;
			case "save":
				print saveQuery($_POST["cURI"], $_POST["propURI"], $_POST["operTxt"], $_POST["valTxt"], $_POST["label"]);
				break;
			case "dele":
				print removeQuery($_POST["uri"]);
				break;
			case "details":
				print detailQueryJSArray($_POST["uri"],$_POST["array"]);
				break;
			case "execute":
				print runQuery($_POST["query"]);
				break;
			case "test":
				print "running test case: <ul><li>finding instances of Demand with one 'feature' being 'service additionnel'<br />";
				print buildAndRunQuery("http://gorgonzola:82/middleware/businessmodel.rdf#113707105956542",
					"http://gorgonzola:82/middleware/businessmodel.rdf#113707119936032",
					"#is_ins",
					"http://gorgonzola:82/middleware/businessmodel.rdf#113707178760146");
				print "</li><li>finding instances of Demand with one 'label' matching 'dem'<br />";
				print buildAndRunQuery("http://gorgonzola:82/middleware/businessmodel.rdf#113707105956542",
					"rdfs:label",
					"#match_txt",
					"dem");
				print "</li><li>saving this latter query<br />";
				print saveQuery("http://gorgonzola:82/middleware/businessmodel.rdf#113707105956542",
					"rdfs:label",
					"#match_txt",
					"dem",
					"test query finding some Demand named 'dem'");
				print "</li></ul>";
				break;
			default:
				print "unknown operator.";
				break;
		}
} elseif (array_key_exists("query", $_POST)) {
	//$query = escapehtmlchars($_POST["query"]);  //TODO escape 'executable' code for safety reasons
	$query = $_POST["query"];
//	print($query);
	print runQuery($query);
} else {
	//runQuery('SELECT ?x  WHERE (?x,  <http://www.w3.org/1999/02/22-rdf-syntax-ns#type>, <http://www.w3.org/2000/01/rdf-schema#Class>)');
	print "no operator nor query found.";
}


/** execute RDQL query on the current user's model, returns (html ul with given ID) list of results or "found 0 results." if no result is found.
*/
function runQuery($q, $ulId = "reslist"){
	global $mod, $modelURI;
	//$query = escapehtmlchars($q);  //TODO escape for safety reasons
//  print ("looking in model: $modelURI for \n"); print_r($q);
	$qres=$mod->rdqlQuery($q, TRUE);
//    print_r($qres);

	if (!($qres && $qres[0]["?ins"])) 
		return("found 0 results.");
	$res = '<ul id="'.$ulId.'">';
	foreach ($qres as $ins) {
		$res .= 	'<li id="'.($ins["?ins"]->getURI()).'" class="instance">'.($ins["?label"]->getLabel())./*' - '.($ins["?ins"]->getURI()).*/'</li>';
	}
	$res .= "</ul>";
	return($res);
}

/** builds and rund an RDQL query that gets all instances of $classURI having 
a $propURI object such that $oper ($value) of this object holds.

returns html list of matching instances: <ul><li id="URI">LABEL</li></ul>

supported $oper values: http://www.tao.lu/Ontologies/queries.rdf#Operators instances URI, eg: http://www.tao.lu/Ontologies/queries.rdf#is_ins
*/
function buildAndRunQuery($classURI, $propURI , $oper, $value, $ulId = "reslist"){
	$classURI = standardizeURI($classURI);
	$propURI = standardizeURI($propURI);	
	$wheres = '(?ins,  rdf:type, <'.$classURI.'>) ';
	$and = '';

	switch ($oper) {
		case "http://www.tao.lu/Ontologies/queries.rdf#is_ins":
		case "#is_ins":
			$value = standardizeURI($value);
			$wheres .= '(?ins, <'.$propURI.'>, <'.$value.'>) ';
			break;
		//look wheter a instruction placed here is executed for any other case, than call here for escaping 'executable' code in $value.
		case "http://www.tao.lu/Ontologies/queries.rdf#is_txt":
		case "#is_txt":
			$wheres .= '(?ins, <'.$propURI.'>, ?value) ';
			$and = ' AND ?value EQ "'.$value.'" '; //TODO escape 'executable' code from $value, for security reasons, in all below cases.
			break;
		case "http://www.tao.lu/Ontologies/queries.rdf#isnot_txt":
		case "#isnot_txt":
			$wheres .= '(?ins, <'.$propURI.'>, ?value) ';
			$and = ' AND ?value NE "'.$value.'" ';
			break;
		case "http://www.tao.lu/Ontologies/queries.rdf#match_txt":
		case "#match_txt":
			$wheres .= '(?ins, <'.$propURI.'>, ?value) ';
			$and = ' AND ?value =~ "/'.$value.'/" '; //TODO handle spaces in value that crash matching. /try "ma d" e.g.
			break;
		case "http://www.tao.lu/Ontologies/queries.rdf#nomatch_txt":
		case "#nomatch_txt":
			$wheres .= '(?ins, <'.$propURI.'>, ?value) ';
			$and = ' AND ?value !~ "/'.$value.'/" ';
			break;
		case "http://www.tao.lu/Ontologies/queries.rdf#is_num":
		case "#is_num":
			$wheres .= '(?ins, <'.$propURI.'>, ?value) ';
			$and = ' AND ?value == "'.$value.'" ';
			break;
		case "http://www.tao.lu/Ontologies/queries.rdf#isnot_num":
		case "#isnot_num":
			$wheres .= '(?ins, <'.$propURI.'>, ?value) ';
			$and = ' AND ?value != "'.$value.'" ';
			break;
		case "http://www.tao.lu/Ontologies/queries.rdf#lt_num":
		case "#lt_num":
			$wheres .= '(?ins, <'.$propURI.'>, ?value) ';
			$and = ' AND ?value < "'.$value.'" ';
			break;
		case "http://www.tao.lu/Ontologies/queries.rdf#gt_num":
		case "#gt_num":
			$wheres .= '(?ins, <'.$propURI.'>, ?value) ';
			$and = ' AND ?value > "'.$value.'" ';
			break;
		default: break;
	}

	$wheres .= '(?ins, rdfs:label, ?label) ';

	$q = 'SELECT ?label, ?ins WHERE '.$wheres.' '.$and.' USING rdf FOR <http://www.w3.org/1999/02/22-rdf-syntax-ns> 	 rdfs FOR <http://www.w3.org/2000/01/rdf-schema>  ';
// umod FOR <http://gorgonzola:82/middleware/businessmodel.rdf> //TODO umod is KO when including a port number... find a way to escape this in RDQL!
//	print "running query: \n".$q."\n";
	return runQuery($q, $ulId);
}

/** if $URI is local, this returns the full URI with user-model NS */
function standardizeURI($URI){
	global $modelURI;
	return preg_replace("/^(#.+$)/",$modelURI."$1", $URI);
	//TODO escape 'executable' codes, for security reasons, and maybe also quotes.
}

/** saves a query in rdfs and stores it in user model 

returns the URI of saved query
*/
function saveQuery($classURI, $propURI, $operTxt, $valTxt, $label){
	global $pSession, $pVoid;

	$inst = setInstance($pSession,array($_SESSION["datalg"]),array($label),array("generis query"),array("http://www.tao.lu/Ontologies/queries.rdf#Query"));
	if (!$inst) 
		return("an error occured while saving query: could not create query");
	$inst = $inst["pOKorKO"]; //get ID

	$prop = editPropertyValuesforInstance($pSession,array($inst),array("http://www.tao.lu/Ontologies/queries.rdf#query_class"),$pVoid,array(standardizeURI($classURI)));
	if (!($prop&&$prop["pOKorKO"]>0)) 
		return("an error occured while saving query: could set #query_class");

	$prop = editPropertyValuesforInstance($pSession,array($inst),array("http://www.tao.lu/Ontologies/queries.rdf#query_property"),$pVoid,array(standardizeURI($propURI)));
	if (!($prop&&$prop["pOKorKO"]>0)) 
		return("an error occured while saving query: could set #query_property");

	$prop = editPropertyValuesforInstance($pSession,array($inst),array("http://www.tao.lu/Ontologies/queries.rdf#query_operator"),$pVoid,array($operTxt));
	if (!($prop&&$prop["pOKorKO"]>0)) 
		return("an error occured while saving query: could set #query_operator");

	$prop = editPropertyValuesforInstance($pSession,array($inst),array("http://www.tao.lu/Ontologies/queries.rdf#query_value"),$pVoid,array(standardizeURI($valTxt)));
	if (!($prop&&$prop["pOKorKO"]>0)) 
		return "an error occured while saving query: could set #query_value";
	
	return($inst);
}

// deletes query
function removeQuery($uri){
	global $pSession;
	$prop = removeInstance($pSession,array(standardizeURI($uri)));
	return($prop?1:-1);
}

// get Query details in JS $array
function detailQueryJSArray($uri,$array){
	$details = detailQuery($uri);
	$res = $array.' = new Array("'.
		trim($details["label"]).'","'.
		$details["class"]["uri"].'","'.trim($details["class"]["label"]).'","'.
		$details["prop"]["uri"].'","'.trim($details["prop"]["label"]).'","'.trim($details["prop"]["range"]).'","'.
		$details["oper"]["uri"].'","'.trim($details["oper"]["label"]).'","'.
		trim($details["value"]["uri"]).'","'.trim($details["value"]["label"]).'");';
	return $res;
}


// get Query details in array
function detailQuery($uri){
	global $pSession, $pVoid;
	
	$res["label"] = getResLabel($uri);
	$res["class"]["uri"] = getPropFirstValue($uri,"http://www.tao.lu/Ontologies/queries.rdf#query_class");
	$res["class"]["label"] = getResLabel($res["class"]["uri"]);
	$prop = getPropFirstValue($uri,"http://www.tao.lu/Ontologies/queries.rdf#query_property");
	$res["prop"]["uri"] = getPropFirstValue($uri,"http://www.tao.lu/Ontologies/queries.rdf#query_property");
	$res["prop"]["label"] = getResLabel($prop);
	$res["prop"]["range"] = getPropFirstValue($prop,"http://www.w3.org/2000/01/rdf-schema#range");
	$res["oper"]["uri"] = getPropFirstValue($uri,"http://www.tao.lu/Ontologies/queries.rdf#query_operator");
	$res["oper"]["label"] = getResLabel($res["oper"]["uri"]);
	$res["value"]["uri"] = getPropFirstValue($uri,"http://www.tao.lu/Ontologies/queries.rdf#query_value");
	$res["value"]["label"] = getResLabel($res["value"]["uri"]);
	if ($res["value"]["label"] == "") {$res["value"]["label"] = $res["value"]["uri"];}	
	return $res;
}

/* get the value of one property of a resource. */
function getPropFirstValue($resURI, $propURI){
	global $pSession, $pVoid;
	$props = GetInstancePropertyValues($pSession, array(standardizeURI($resURI)), array(standardizeURI($propURI)),$pVoid);
	return $props[0];
}

function getResLabel($resURI){
	return getPropFirstValue($resURI,"http://www.w3.org/2000/01/rdf-schema#label");
}

?>
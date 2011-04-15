<?php

if (!(isset($_SESSION))) {session_start();}

require("../../core/api/generisApiPhp.php");
/**
This backend provides all necessary input for creating/saving generis queries.
It detects the parameter's name in $_POST and behaves accordingly:

author: BGr
date: 20060117
version: 0.9
*/

//globals
$pGenNS = array("http://www.tao.lu/Ontologies/generis.rdf");
$bGenRes = "http://www.tao.lu/Ontologies/generis.rdf#generis_Ressource";
$pGenRes = array($bGenRes);
$pVoid = array("");
$bQuery = "http://www.tao.lu/Ontologies/queries.rdf#Query";
$pSession = $_SESSION["session"];
$userModelURI = getNS($pSession);
$bUserModelURI = array($userModelURI);


if (array_key_exists("listQueries", $_POST)) { // queries defined in user model
	print getHTMLQueries($_POST["listQueries"]);
} elseif (array_key_exists("classV", $_POST)) { // class name matching
	print getHTMLClassesFromLabel($_POST["classV"]);
} elseif (array_key_exists("classID", $_POST)) { // list of properties
	print getDOMPropertiesFromClassID($_POST["classID"],$_POST["select"]);
} elseif (array_key_exists("targetClassID", $_POST)) { // array of operators and values
	print getJSArrayOpsAndValsFromClassID($_POST["targetClassID"], $_POST["targetNS"], $_POST["valuesName"], $_POST["operatorsName"]);
} elseif (array_key_exists("delQuery", $_POST)) { // removeQuery
	print removeQuery($_POST["delQuery"]);
}



/** returns (html ul) list of the saved queries */
function getHTMLQueries($id){
	global $pSession, $bQuery, $pVoid;

	$queries = getInstances($pSession,array("http://www.tao.lu/Ontologies/queries.rdf#Query"),$pVoid,false,true); //last true to include indirect instances
	$res = '<ul id="'.$id.'" class="instances">'."\n";
	foreach ($queries["pDescription"] as $q=>$val) {
		$res .= '<li id="'.standardizeURI($val["InstanceKey"]).'" title="'.trim($val["InstanceComment"]).'" class="query">'.trim($val["InstanceLabel"])."</li>\n"; //todo escape content of $res from ", everywhere!
	}
	$res .= '</ul>';
	return $res;
}

//returns (html ul) list of classes which label contains $pattern, "*" $pattern returns all classes.
function getHTMLClassesFromLabel($pattern){

	$classes = getCachedClassesInScope();
	$res = "<ul>"; //."<li>pattern is: ".$pattern."</li>";
	foreach ($classes as $clD) {
		if (($pattern == "*") || (stristr($clD["PropertyLabel"],$pattern))) {
			$res .= '<li id="'.$clD["PropertyKey"].'">'.trim($clD["PropertyLabel"])./*' <span class="id informal" >('.$clD["PropertyKey"].')</span>*/'</li>';
		}
	}
	$res .= '<li><span class="informal">-- classes from extended models --</span></li>'; //TODO add classes from extended (non-user) models
	$res .= "</ul>";
	return $res;
}

//returns (cached) array of classes
function getCachedClassesInScope(){ //TODO faire ici liste des classes du modèle utilisateur (d'abord) puis des classes des modèles étendus (chargés) ?
	global $pSession, $pVoid, $bGenRes;

	$classes = getIndirectsubClasses($pSession,$bGenRes,$pVoid);
	return $classes;

	//TODO $_GLOBAL["classesUTD"] should be unset when class tree is changed... see in RDF model?
	//$_SESSION["classesUTD"]["requetes"]["classes"];
//	if (!(isset($_SESSION["classesUTD"]["requetes"]["classes"]))) {
		//KO following seems to return empty array result in pDescription ?
		//$classes = getsubClasses($pSession,$pGenRes,$pGenNS);
//		$_SESSION["classesUTD"]["requetes"]["classes"] = $classes;
//	}
}	

/** returns (html options) list of properties defines upon class $cID, 
	option id is property URI.
	option value is range (type) URI.
*/
function getDOMPropertiesFromClassID($cID,$select){
	global $pSession, $pVoid;

//	$props = getAllProperties($pSession,array($cID),$pVoid); //TODO this call would be better but seems not to work, see api_explore.
	$popt = "var sel=$(\"$select\"); removeChilds(sel);  \n";
	$i = 0;
	$resD = getRessourceDescription($pSession,$cID,"");
	foreach ($resD["relatedproperties"] as $prD) {
		$popt .= 'sel.options['.$i.'] = new Option("'.trim($prD["PropertyLabel"]).'", "'.standardizeURI($prD["PropertyRange"]).'"); sel.options['.$i.'].id="'.standardizeURI($prD["PropertyKey"]).'";'."\n";
		$i++;
	}
	return $popt;
}




//returns JS array of properties defines upon class $cID
function getJSArrayOpsAndValsFromClassID($cID,$ns,$valArName, $opArName) {
	global $pSession, $pVoid;
	$res = "/* target type is: $cID */\n";

	switch ($cID) {
		case "http://www.w3.org/2000/01/rdf-schema#Literal":
			$res .= $opArName.'.push(new Array("is","http://www.tao.lu/Ontologies/queries.rdf#is_txt")); '.
			$opArName.'.push(new Array("is not","http://www.tao.lu/Ontologies/queries.rdf#isnot_txt")); '.
			$opArName.'.push(new Array("contains","http://www.tao.lu/Ontologies/queries.rdf#match_txt")); '.
			$opArName.'.push(new Array("doesn\'t contains","http://www.tao.lu/Ontologies/queries.rdf#nomatch_txt")); '."\n";
			break;
		case "http://www.w3.org/1999/02/22-rdf-syntax-ns#Integer":
			$res .= $opArName.'.push(new Array("is","http://www.tao.lu/Ontologies/queries.rdf#is_num")); '.
			$opArName.'.push(new Array("is not","http://www.tao.lu/Ontologies/queries.rdf#isnot_num")); '.
			$opArName.'.push(new Array("more than","http://www.tao.lu/Ontologies/queries.rdf#gt_num")); '.
			$opArName.'.push(new Array("less than","http://www.tao.lu/Ontologies/queries.rdf#lt_num")); '."\n";
			break;
		default:
			$res .= $opArName.'.push(new Array("is","http://www.tao.lu/Ontologies/queries.rdf#is_ins")); '."\n";
			//list of existing instances is not provided for the above cases of text or int.
			$resD = getInstances($pSession,array($cID),$pVoid,false,true); //last true to include indirect instances
			foreach ($resD["pDescription"] as $iID => $iDesc) {
				$res .= $valArName.'.push(new Array("'.standardizeURI($iDesc["InstanceKey"]).'","'.trim($iDesc["InstanceLabel"]).'","'.trim($iDesc["InstanceComment"]).'"));'."\n";
			}
			break;
	}
	return escapeJS($res);
}



// removes ' and \n in JS commands.
function escapeJS($str){
	$search = array ('/\'/','/\n/'); // single quote and newline .
	$replace = array ('´');
	return preg_replace($search, $replace, $str);
}

/** if $URI is local, this returns the full URI with user-model NS */
function standardizeURI($URI){
	global $userModelURI;
	return preg_replace("/^(#.+$)/",$userModelURI."$1", $URI);
	//TODO escape 'executable' codes, for security reasons, and maybe also quotes.
}

?>
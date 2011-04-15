<?php

/**
* Class to implement authentication of subscribers and generation of XML RDF file
* @access public
* @package kernel
* @author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 1.1
*/
ini_set("allow_call_time_pass_reference",true);
//error_reporting(E_ALL);
include("./accesBD.php");
require_once("../../common/config.php");
include("./accesBDSubscriber.php");
require_once("./soap/nusoap.php");

/****************************************************************************************
*	
*				PART 1 : 
*		Constructor, Disconnection of database
*		Authentication, generation of xml rdf (for subsciber)
*		
*****************************************************************************************/
 
 $server = new nusoap_server();
 //$server->debug_flag=true;

 set_time_limit(3600);
 function getXMLRDF($login,$password,$modulename,$askull="0")
	{
	
	$data = new accesBDsubscriber();
    $data->connection($modulename);
	
	$ok=FALSE;
	$bol = "";
	$bol = $data->authenticatesubscriber($login,$password);
		if ($bol == "") {$ok=FALSE;} 
		else {$ok=TRUE;}
	
	if ($ok) 
		{
		
		$statements = getAllstatements($data->getNamespace(),$askull);
		
		return array(base64_encode(serialize($statements)),$data->getNamespace());
		 
		}
	else {return array("Authentication failed",$login,$password,$modulename);}
	$data->disconnection();
	}
 
 
 function getAllstatements($namespace,$askull="0")
	{

		$result = mysql_query("SELECT modelID FROM models where modelURI='".$namespace."'");
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
		{
	   
	  $ID =  $row[0];
		  
		}
		$append="";
		
		if ($askull=="1") {$append=" and (
		predicate='http://www.w3.org/1999/02/22-rdf-syntax-ns#type' OR predicate='http://www.w3.org/2000/01/rdf-schema#subClassOf' OR
		predicate='http://www.w3.org/2000/01/rdf-schema#label' OR
		predicate='http://www.w3.org/2000/01/rdf-schema#comment' 
		)";}

		if ($askull=="2") {$append=" and (
	
		predicate='http://www.w3.org/2000/01/rdf-schema#label' OR
		predicate='http://www.tao.lu/Ontologies/TAOSubject.rdf#Login' OR
		predicate='http://www.tao.lu/Ontologies/TAOSubject.rdf#Password' 
		
		)";}

		if ($askull=="3") {$append=" and (
		predicate='http://www.w3.org/1999/02/22-rdf-syntax-ns#type' OR predicate='http://www.w3.org/2000/01/rdf-schema#subClassOf' OR
		predicate='http://www.w3.org/2000/01/rdf-schema#label' OR
		predicate='http://www.tao.lu/Ontologies/TAOSubject.rdf#Login' OR
		predicate='http://www.tao.lu/Ontologies/TAOSubject.rdf#Password' OR
		predicate='http://www.tao.lu/Ontologies/TAOGroup.rdf#Members' OR
		predicate='http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent' OR
		predicate='http://www.tao.lu/Ontologies/TAOTest.rdf#RelatedItems' OR
		predicate='http://www.tao.lu/Ontologies/TAOItem.rdf#ItemContent' OR
		predicate='http://www.w3.org/2000/01/rdf-schema#comment' 
		)";}
		
		$query="select modelID,subject,predicate,object,l_language,l_datatype,subject_is,object_is from statements where modelID='".$ID."'".$append;

		
		$result = mysql_query($query);
		while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
		  {
				$statements[]= $row;
				
		   }

		return $statements;
	}


 function getXMLRDFasVirtualUser($login,$password,$modulename)
	{
	
	$data = new accesBD();
	
    $data->connection($modulename);
	
	$ok=FALSE;
	$bol = "";
	
	$bol = $data->authenticate($login,$password);
		if ($bol == "") {$ok=FALSE;} 
		else {$ok=TRUE;}
		
	
	if ($ok) 
		{
		$result= array($data->rewriteXMLRDFDataforuser($login,FALSE),$data->getNamespace());
		 
		}
	
	
	$data->disconnection();

		
	return $result;
	}



 $server->add_to_map("getXMLRDFasVirtualUser", array("string", "string","string"), array("array"));
 $server->add_to_map("getXMLRDF", array("string", "string","string"), array("array"));
 
$server->service($HTTP_RAW_POST_DATA);

//getXMLRDF("47072","e3d23b257cd19c27ca38fb7a8eeb9cd1","romItems");



?>
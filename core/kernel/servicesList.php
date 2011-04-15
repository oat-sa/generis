<?php
/*
	 

    
    
    
    

    
    
    

*/
/**
* Implements all available services/requests provided by the generis kernel 
* May be used by user interfaces or other applications (plugins, etc.) 
* 
* Manage semantic web resources, users, subscribers subscribees within a selected Module. A session mechanism is used to prevent unauthorized access or requests to the kernel.
* 
* All queries performed are redirected according to their purpose , if it is a knowledge consultation, query is redirected to rdql_DB Object implementing all consultation queries on actual knowledge, if its is a knowledge modification request, query is redirected to modelManager implementing such functions, rights access verifications plus administration functions (Users, subscribers, subscribees management)
*
* Session contains informations about connected entity its rights (u-mask), informations about
* the models he is consulting or modifying  and some other contextual
* informations
* This session is returned by the authentication service
*
* @package kernel
* @author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 1.1
*/
	
define("WEBSERVICE_KERNEL_ACTIVATED",true,true);
define("USERAPPLI", "user", true);
define("VIEWMODE", "View", true);
define("DATEDOC", "date", true);
define("SIZEDOC", "size", true);
function cvrtFields($string){	return strtolower($string);}

include_once(dirname (__FILE__)."/../../include/adodb5/adodb.inc.php");
include_once(dirname (__FILE__)."/accesBD.php");
include_once(dirname (__FILE__)."/modelManager.php");
include_once(dirname (__FILE__).'/../../../common/common.php');
include_once(dirname (__FILE__)."/model.php");
include_once(dirname (__FILE__)."/rdfmodel.php");
include_once(dirname (__FILE__)."/rdfsmodel.php");
require_once("./soap/nusoap.php");
	
	
	// Create the server instance
	$server = new nusoap_server();
	
	$server->debug_flag=true;
	// Initialize WSDL support
	$server->configureWSDL('kernel', 'urn:kernel');

function connection($currentModuleDatabase)
	{
		
		$con = NewADOConnection(SGBD_DRIVER);
	    $con->Connect(DATABASE_URL, DATABASE_LOGIN, DATABASE_PASS, $currentModuleDatabase);
        $con-> debug = false;
		
		return $con;
	}
/**
* Authenticate an user with specified Module : $pMd
*@access public
*@param String $pName Login 
*@param String $pPwd Password 
*@param String  $pFct authenticate as admin if allowed String:="0" as user String:="1" 
*@param  String $pMd moduleName the Name of database to use
*@return String session or "Authentication failed"
**/
function authenticate($pName,$pPwd,$pFct,$pMD)
	{	
		
	
		
		$modelManager = new modelManager($pMD);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
		$bool = $modelManager->authenticate($pName,$pPwd,$pFct);
		$type = $modelManager->getTypeModule();
		
		if ($bool)
		{
			$temp = serialize($modelManager);
			$temp2 = urlencode($temp);
			return $temp2;
		} 
		else
		{
			return "Authentication failed";
		}
	}
	// Register the method to expose
	$server->register('authenticate',                // method name
    	array('pName' => 'xsd:string','pPwd' => 'xsd:string','pFct' => 'xsd:string','pMD' => 'xsd:string'), // input parameters
    	array('return' => 'xsd:string'),      // output parameters
    	'urn:kernel',                      // namespace
    	'urn:kernel#authenticate',                // soapaction
    	'rpc',                                // style
    	'encoded',                            // use
    	'Retrieve triples whose object matches the keywords' // documentation
	);



/**
* sparql  
* @returns sparql xml messages
**/
function sparql($session,$sparql)
{
		$x = urldecode($session);
		$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
		return $modelManager->sparql($sparql);
}

$server->register('sparql',                // method name
    	array('session' => 'xsd:string','sparql' => 'xsd:string'), // input parameters
    	array('return' => 'xsd:string'),      // output parameters
    	'urn:kernel',                      // namespace
    	'urn:kernel#getInstances',                // soapaction
    	'rpc',                                // style
    	'encoded',                            // use
    	'sparql query' // documentation
	);

 /**
 * @param $keywordsArray A list of keywords in a array. 
 * @return array[][] Vector of triples describing matching resources.
 * @public
 */

function fullTextSearch($session,$keywordsArray)
{
	$x = urldecode($session);$modelManager = unserialize($x);
	$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
	return $modelManager->fullTextSearch($keywordsArray);

}

$server->wsdl->addComplexType(
	'ArrayOfstring',
	'complexType',
	'array',
	'',
	'SOAP-ENC:Array',
	array(),
	array(array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'string[]')),
	'xsd:string'
);


$server->register('fullTextSearch',                // method name
    	array('session' => 'xsd:string','keywordsArray' => 'tns:ArrayOfstring'), // input parameters
    	array('return' => 'xsd:string'),      // output parameters
    	'urn:kernel',                      // namespace
    	'urn:kernel#getInstances',                // soapaction
    	'rpc',                                // style
    	'encoded',                            // use
    	'full text search with keywords' // documentation
	);


/**
*Refresh and regenerates actual knowledge reachable by the connected entity
*retrieve rdf data, store it as triples in database and keep references into bm->rdf
*@param TAO:session TAO session returned by authenticate service
*@access public
*@return String xml/rdf for export
*/
function exportxmlRDF($pSession)
{
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($pSession);$modelManager = unserialize($x);
	$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
	$xmlrdf = $modelManager->getRDF();
	
	return $xmlrdf;
}

$server->register('exportxmlRDF',                // method name
    	array('session' => 'xsd:string'), // input parameters
    	array('return' => 'xsd:string'),      // output parameters
    	'urn:kernel',                      // namespace
    	'urn:kernel#getInstances',                // soapaction
    	'rpc',                                // style
    	'encoded',                            // use
    	'Retrieve triples whose object matches the keywords' // documentation
	);


/**
*Returns all instances of $idclass
*
*@param $pSession returned by authenticate service
*@access public
*@param String $idclass  uri
*@param boolean $indirect includes instances of subclasses recursively.
@return Array()
**/
function getInstances($session,$idclass,$indirect=false)
	{
		
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}

		
		$instances = $modelManager->getInstances(array($idclass),array(""),$indirect);
		$instances_str ="";
		foreach ($instances["pDescription"] as $instance) 
		{	
		$instances_str.="<".$instance["InstanceKey"]."> <http://www.w3.org/2000/01/rdf-schema#:label> literal(".trim($instance["InstanceLabel"]).")
		";
		$instances_str.="<".$instance["InstanceKey"]."> <http://www.w3.org/2000/01/rdf-schema#:comment> literal(".trim($instance["InstanceComment"]).")
		";
		
		}
		return $instances_str;
	}


	$server->register('getInstances',                // method name
    	array('session' => 'xsd:string','idclass' => 'xsd:string','indirect' => 'xsd:string'), // input parameters
    	array('return' => 'xsd:string'),      // output parameters
    	'urn:kernel',                      // namespace
    	'urn:kernel#getInstances',                // soapaction
    	'rpc',                                // style
    	'encoded',                            // use
    	'Retrieve triples whose object matches the keywords' // documentation
	);
	if (WEBSERVICE_KERNEL_ACTIVATED)
{
$server->service($HTTP_RAW_POST_DATA);
}
?>

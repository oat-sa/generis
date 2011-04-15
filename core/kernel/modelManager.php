<?php


/**
* Implements all knowledge modifications requests, rights access Management and administration requests (TAO:User, TAO:subscriber and TAO:subscribee management )
* @package kernel
* @author Plichart Patrick  <patrick.plichart@tudor.lu>
* @version 1.1
*/
class modelManager
{
	/**
	* Name of knowledge base the kernel is connected to
	*/
  var $currentModuleDatabase="";
	/**
	* AccesBD Object connected to the knowledge base (this objkect allows knowledge modification requests and administration requests)
	*/
  var $bd; //AccesBD Object
    /**
	* RDQL_DB Object connected to the knowledge base (this objkect allows knowledge consultation requests)
	*/
   var $db; //RDQL_DB Object;
  /**
	* Login of user connected to the module
	*/
  var $user=""; //Current User
  var $usergroup=""; //Current User's group
  /**
	* Mask of connected user
	*/
  var $umask=""; //Mask of current user
  /**
	* (User is admin)
	*/
  var $admin=FALSE; //isAdmin currentuser
  /** $rdf is an array
  [0] Url of file (dockey of rdfs document in knowledge base of the rdf file filtered regarding current language)
  [1] Namespace used in this file
  [2] Url of file (dockey in database of the rdfs multilingual file )
  */
  var $RDF=array();

  var $model;

  var $modelURI="";
  var $modelID="";
  var $con;
  var $lg;//User selected language (all literals will be returned in the selected lg)
  var $deflg;


/**
* Retrun type of a ressource (e.g. a meta class for a class)
*@access public
*@param TAO:session $session returned by authenticate service
*@param String $URI Uri of resource
*@return Array of type
**/
function typeOf($URI){
  $this->model->con = $this->bd->con;
  return $this->model->getType($URI);
}

/**
*Returns all instances of $idclass
*@param Array([0] => String) $idclass : array(14) (without #c) id of class
*@param boolean $indirect includes instances of subclasses recursively
*@return Array()
**/
function getIdInstances($idclass,$indirect=false){
  $this->model->con = $this->bd->con;
  return $this->model->getIdInstances($idclass,$indirect);
}

/**
*Returns the number of instances of $idclass
*@param Array([0] => String) $idclass : array(14) (without #c) id of class
*@param boolean $indirect includes instances of subclasses recursively.
@return Array()
**/
function getNbInstances($idclass,$indirect=false){
  $this->model->con = $this->bd->con;
  return $this->model->getNbInstances($idclass,$indirect);
}


  var $remoteRDF=array(); /*An array analog to $rdf containing knowledge
  described on remote connected Modules*/
/****************************************************************************************
*
*				PART 1 :
*		Constructor, Disconnection of database
*		Authentication, generation of xml rdf (for loca admin or local user)
*		Get XML RDF from remote module
*		(Authentification of a subscriber and building xmlrdf for this subscriber (rights access) is implemente
*		into businessModuleforSubscribers)
*****************************************************************************************/
  function modelManager($curModule ="")
  {
	$this->currentModuleDatabase = $curModule;
   $this->bd = new accesBD();

  }
 /**
  * returns module type
  *
  * @access public
  * @return string module type
  */
function getTypeModule()
	{
	 $result = $this->bd->getTypeModule();
	 return $result;
	}

	function getRDF()
	{
		//redefiniton of rdf api for php constants becaus of side effects in rap code

//		error_reporting(0);
		define('RDFAPI_INCLUDE_DIR', dirname (__FILE__).'/../../include/powl/RDFLayer/rdfapi-php/api/');
		define('RDFSAPI_INCLUDE_DIR', dirname (__FILE__).'/../../include/powl/RDFSLayer/rdfsapi/');
		define('OWLAPI_INCLUDE_DIR', dirname (__FILE__).'/../../include/powl/OWLLayer/owlapi/');
		

		$GLOBALS['dbConf']['type'] = SGBD_DRIVER;
		$GLOBALS['dbConf']['host'] = DATABASE_URL;
		$GLOBALS['dbConf']['database'] = $this->currentModuleDatabase;
		$GLOBALS['dbConf']['user'] = DATABASE_LOGIN;
		$GLOBALS['dbConf']['password'] = DATABASE_PASS;

		error_reporting(0);
		define('RDF_NAMESPACE_URI','http://www.w3.org/1999/02/22-rdf-syntax-ns#' );
		define('RDF_NAMESPACE_PREFIX','rdf' );
		define('RDF_RDF','rdf:RDF');
		define('RDF_DESCRIPTION','rdf:Description');
		define('RDF_ID','rdf:ID');
		define('RDF_ABOUT','rdf:about');
		error_reporting(E_ALL);
		
		include_once(RDFSAPI_INCLUDE_DIR."rdfsapi.php");
		include_once(OWLAPI_INCLUDE_DIR."owlapi.php");
		include_once(RDFAPI_INCLUDE_DIR."syntax/RdfSerializer.php");

		ob_start();

		$pOWLStore = new POWLStore(ADODB_DB_DRIVER,ADODB_DB_HOST,ADODB_DB_NAME,ADODB_DB_USER,ADODB_DB_PASSWORD);
		
		$mymodel = $pOWLStore->getModel($this->bd->getNamespace());

		error_reporting(0);
		$MemModel=& $mymodel->getMemModel();
		


		$ser = new RDFSerializer();
		

		$xml =$ser->serialize($MemModel);

		/* PPL 10/06 prefix problems in xml rdf serialisation were fixed in constants.php 
		(rdfapi-php) from pOWL, still some problems occurs with prefic of rdf:type and rdf:value, 
		maybe the file RdfSerializer.php should be fixed or updated*/

		$xml = str_replace("<rdf:RDF",'<rdf:RDF
		xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
		',$xml);
		$xml= str_replace('<![CDATA[]]>','',$xml);
		$xml= str_replace(':11',':i11',$xml);
		$xml= str_replace('ID="11','ID="i11',$xml);
		$xml= str_replace('NULL','',$xml);
		$xml= str_replace('<value xml:lang=','<rdf:value xml:lang=',$xml);
		$xml= str_replace('</value>','</rdf:value>',$xml);
		$xml= str_replace(" <type",'<rdf:type',$xml);
		ob_clean();
		error_reporting(E_ALL);
		unset($GLOBALS['dbConf']);
		return trim($xml);
	}
	/*
	*Extract xml rdf from all databases of tao v1.1
	*/

	function getOldrdf()
	{

		$query = "SELECT rdf FROM class";
		$classes= $this->bd->con->Execute($query);
		$query = "SELECT rdf FROM property";
		$properties = $this->bd->con->Execute($query);
		$query = "SELECT rdf, Id FROM instance";
		$instances = $this->bd->con->Execute($query);

		$text = "<?xml version='1.0' encoding='ISO-8859-1'?>
		<!DOCTYPE rdf:RDF [
			<!ENTITY rdf 'http://www.w3.org/1999/02/22-rdf-syntax-ns#'>
			<!ENTITY rdfs 'http://www.w3.org/TR/1999/PR-rdf-schema-19990303#'>
			<!ENTITY Type 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#'>
			]>
		<rdf:RDF
			xmlns:rdf=\"&rdf;\"
			xmlns:Type=\"&Type;\"
			xmlns:rdfs=\"&rdfs;\"
			";

		$text .= 'xmlns:module="' . $this->bd->getNamespace().'#">
		';

		while (!$classes->EOF)
			{$text .= $classes->fields[0]."
			"; $classes->MoveNext();
			}

		while (!$properties->EOF)
				{$text .= $properties->fields[0]."
						"; $properties-> MoveNext();
				}


	$query = "SELECT distinct Id FROM property ,".USERAPPLI."";

				$rows =$this->bd->con->Execute($query);
				$okprop="";
				$Listofproperties=array();

				while (!$rows->EOF)
				{$okprop.= "'".$rows->fields[0]."',"; $Listofproperties[]=$rows->fields[0];
					$rows-> MoveNext();}

				if (strlen($okprop) >2) {$okprop =substr($okprop,0,strlen($okprop)-1);}

$i=0;
   while (!$instances->EOF)
		{    	/*Retrieve instance description before values*/
				$text .= str_replace("�","e",str_replace("�","e",str_replace("�","u",str_replace("�","a",str_replace("�","o",str_replace("�"," ",$instances->fields[0]))))));
				//print_r($instances);
				if (!($okprop==""))
				{
					$i++;
					//echo "Debut requete ".$i." ".microtime_float()."<br>";

					foreach ($Listofproperties as $key=>$val)
					{
					$query = "SELECT rdf FROM classinstance
					WHERE
					classinstance.IdInstance  = '".$instances->fields[1]."'
					AND
					classinstance.IdProperty = '".$val."'";

					$rdfthatcanbeseen= $this->bd->con->Execute($query);

							while (!$rdfthatcanbeseen->EOF)
							{

								$text .= $rdfthatcanbeseen->fields[0]."

							";
							$rdfthatcanbeseen-> MoveNext();
							}
					}

				}

				$text .= "</rdf:Description>";

		 $instances-> MoveNext();
           }

	 $text.="</rdf:RDF>";
	$template="BLOBI(.)*ENDTAO";
	$occurences=array();
	$x="";

	preg_match_all("#$template#",$text,$occurences) ;

	foreach ($occurences[0] as $key=>$val)
			{
				if ($val != "1" )
				{

				$longObject=$this->getLongObject("TAO-".$val."-BLOB");

				$text =$this->str_replace_one("TAO-".$val."-BLOB","

				".$longObject."

				",$text);

				}
				//echo "TAO-".$val."-BLOB";
			}
			//echo $text;


	 return $text;
	}
/*private*/
function str_replace_one($find,$replace,$subject)
{
   $subjectnew = $subject;
   $pos = strpos($subject,$find);
   if ($pos !== FALSE)
   {

         $temp = substr($subjectnew,$pos+strlen($find));
         $subjectnew = substr($subjectnew,0,$pos) . $replace . $temp;


   } // closes the if
   return $subjectnew;
}
/*private*/
function getLongObject($blobID)
	{
	$querye="select Object from longObjects WHERE IDObject='".$blobID."'";

	$result=$this->bd->con->Execute($querye);
	while (!$result->EOF)
		{
	     $xml=$result->fields[0];

		 return $xml;
		}
	}

/**
* getRDFfromaremotemodule retrieves rdfs file of athe subscribee (generis module), parse it create related model (new model) and returns updated session and subscribee's namespace
*
*

* @id Array(String) Id of subscribee to connect to
* @return  array(0 => xml/rdf,1 => NAMESPACE) , keep namespace  in order to make queries using adequate namespace as $remotedockey parameter for further requests in servermodule

*@access public
*/
	function getRDFfromaremotemodule($idsubscribee,$cache=false,$askull="0")
	{


		$url = $this->bd->getSubscribeeURL($idsubscribee);
		$login = $this->bd->getSubscribeeLogin($idsubscribee);
		$password = $this->bd->getSubscribeePassword($idsubscribee);
		$modulename = $this->bd->getSubscribeemodulename($idsubscribee);
		$serverpath =str_replace("/middleware/","/generis/core/kernel/",$url);
		$serverpath =str_replace("businessmodulesubscriber.php","businessModuleSubscriber.php",$serverpath);
		$result="";

		$client = new nusoapclient($serverpath);

		 set_time_limit(300);
		$client->debug_flag=true;

		if($err = $client->getError())
			{

			return "<b>soap client error1: $err</b><br>";}
			else
				{
				//print_r(array($serverpath,$login,$password,$modulename,$askull));
				$result = $client->call('getXMLRDF',array($login,$password,$modulename,$askull));

				if ($result=="") {
					echo "<b>Connection to module failed, check login, password and url for ".$modulename."</b><br />";
					return "Error 00 : connection to module failed";
					}
				}


		if($err = $client->getError())
			{

			return "<b>soap client error1: $err</b><br>";
			}

		if ($result[0]!="Authentication failed")
				{

				$xml = unserialize(base64_decode($result[0]));

				$y = $this->model;

				$y->con = $this->bd->con;
				if ($cache) {$result[1]='cache_'.time().' '.rand(0,65536);}
				$result[1] =$y->insertAllstatements($xml,$result[1]);
				error_reporting(0);
				$y->loadModel($result[1]);
				$temp = serialize($this);
				$temp2 = urlencode($temp);
				error_reporting(E_ALL);
				return array("pSession"=> array($temp2,$result[1]));
				}
				else {return "ERROR";}


	}
/*
* import rdfs parses $file (path tom the file http: or file:) and create the model (new one) locally with respect of namespaces and id in this file but if $hardimport is set to true (resources are copied in the local model)
*/

function importrdfs($urimodel,$file,$hardimport=false)
	{


				error_reporting(0);
				define('RDFAPI_INCLUDE_DIR', dirname (__FILE__).'/../../include/powl/RDFLayer/rdfapi-php/api/');
				define('RDFSAPI_INCLUDE_DIR', dirname (__FILE__).'/../../include/powl/RDFSLayer/rdfsapi/');
				define('OWLAPI_INCLUDE_DIR', dirname (__FILE__).'/../../include/powl/OWLLayer/owlapi/');
				
				$GLOBALS['dbConf']['type'] = SGBD_DRIVER;
				$GLOBALS['dbConf']['host'] = DATABASE_URL;
				$GLOBALS['dbConf']['database'] = $this->currentModuleDatabase;
				$GLOBALS['dbConf']['user'] = DATABASE_LOGIN;
				$GLOBALS['dbConf']['password'] = DATABASE_PASS;
				
				define('RDF_NAMESPACE_URI','http://www.w3.org/1999/02/22-rdf-syntax-ns#' );
				define('RDF_NAMESPACE_PREFIX','rdf' );
				define('RDF_RDF','RDF');
				define('RDF_DESCRIPTION','Description');
				define('RDF_ID','ID');
				define('RDF_ABOUT','about');
				error_reporting(E_ALL);

				include_once(RDFSAPI_INCLUDE_DIR."rdfsapi.php");
				include_once(OWLAPI_INCLUDE_DIR."owlapi.php");
				include_once(RDFAPI_INCLUDE_DIR."syntax/RdfSerializer.php");

				//include('w:/www/config/config.php');
				error_reporting(E_ALL);
				$a = new POWLStore(ADODB_DB_DRIVER,ADODB_DB_HOST,$this->currentModuleDatabase,ADODB_DB_USER,ADODB_DB_PASSWORD);


				error_reporting(E_ALL);
				$y = $this->model;

				$y->con = $this->bd->con;
				echo "<!--";

				if ($y->issetModel($urimodel))
				{
					if (!($hardimport))
					{

					$y->loadModel($urimodel);

					}
				 $temp = serialize($this);
				$temp2 = urlencode($temp);
				echo "--!>";
				unset($GLOBALS['dbConf']);
				return array("pSession"=> array(0 => $temp2,1 =>$urimodel));
				//$a->deleteModel($urimodel);
				}

				error_reporting(0);
				if (!($hardimport))
					{

					$a->loadModel($urimodel, $file);

					}
					else
					{
						$a->loadModel($urimodel, $file,false,false,$this->model->modelURI);
					}

					if (!($hardimport))
					{
					// $urimodel = str_replace("http://www.tao.lu/","http://".$_SERVER["HTTP_HOST"]."/generis/",$urimodel);
					//error_reporting(E_ALL);

					$y->loadModel($urimodel);

echo __LINE__;
					}
				echo __LINE__;

                $temp = serialize($this);
				$temp2 = urlencode($temp);

				echo "--!>";
				unset($GLOBALS['dbConf']);
				error_reporting(E_ALL);
				return array("pSession"=> array(0 => $temp2,1 =>$urimodel));

	}



  /**
* Authenticate an user with the specified Module : $pMd
*@access public
*@param String $pName login
*@param String $pPwd password
*@param String  $pFct authenticate as admin if allowed String:="0" as user String:="1" ArrayofString
*@param  String $pMd moduleName the Name of database to use
*@return boolean
**/
    function authenticate($login,$password,$aslocaluser)
	{

	   //$start = serveurmicrotime_float();

	   $query="Select password from ".USERAPPLI." where login='".$login."'";
	   $result =  $this->bd->con->Execute($query);

	   if ($result == false)
		{
			return false;
		}
           while (!$result->EOF)
           {
                 if   (md5($password) == $result->fields[cvrtFields('PASSWORD')])
                      {$bol=$login;} else {$bol="";}
                 $result-> MoveNext();
           }

		error_reporting("^E_NOTICE");

		if ($login=="Anonymous") {$bol="Anonymous";}
		if ($bol == "") {return FALSE;}
		else {


				$x = new generisrdfsmodel();

				$x->con =$this->bd->con;

				//Update database structure to the new format including privileges informations
				//$x->updateIfneededModelofDatabase();

				$this->umask = $this->bd->getUmask($bol);

				$this->admin = ($this->bd->isAdmin($bol) and (($aslocaluser=="1") or ($aslocaluser=="2") ));
				$this->user = $bol;

				$temp = array();

				$this->RDF=$temp;

				$modelURI = $this->bd->getNamespace();

				$x = new generisrdfsmodel();

				$x->con =$this->bd->con;

				//$cache = new rdfs_cache($x);
				//$x->cache=$cache;


				$x->setModelURI($modelURI);//by default the model browsed is the user model

				/*
				$x->loadModel("https://bscw.ercim.org/bscw/bscw.cgi/d204590/collaboration.rdfs");
				$x->loadModel("https://bscw.ercim.org/bscw/bscw.cgi/d204606/process-activity.rdfs");
				$x->loadModel("https://bscw.ercim.org/bscw/bscw.cgi/d204594/competency.rdfs");
				$x->loadModel("https://bscw.ercim.org/bscw/bscw.cgi/d204598/learner.rdfs");
				$x->loadModel("https://bscw.ercim.org/bscw/bscw.cgi/d204602/lessonsLearnt.rdfs");
				*/

				//try to load hyperclass model if available
				$x->loadModel("../ontology/hyperclass.rdfs");

				//try to load process model if available
				$x->loadModel("../ontology/taoqual.rdfs");

				//try to load rules model if available
				$x->loadModel("../ontology/rules.rdfs");

				//try to load process model if available
				$x->loadModel("../ontology/rules.rdfs");

				$x->loadModel("http://www.w3.org/1999/02/22-rdf-syntax-ns#");//Load rdf
				$x->loadModel("http://www.w3.org/2000/01/rdf-schema#");//Load rdfs
				$x->loadModel("http://www.tao.lu/datatypes/WidgetDefinitions.rdf#");//Load Widgets
				error_reporting(E_ALL);
				include_once(dirname (__FILE__)."/loadModels.php");
				error_reporting(0);
				foreach ($DEFMODELS as $key=>$val)
					{

						if ($key==$this->currentModuleDatabase)
							{
								$x->loadModel($val);

							}
					}
				error_reporting(E_ALL);
				//Load Etesting
				if ((true) or ($aslocaluser=="2"))
				{
				$x->loadModel("http://www.tao.lu/Ontologies/generis.rdf#");//Load generis Model
				}

				$extensionManager = common_ext_ExtensionsManager::singleton();
				
				foreach ($extensionManager->getModelsToLoad() as $model){
					$x->loadModel($model);
				}
				
				
//				$x->loadModel("http://www.tao.lu/Ontologies/TAO.rdf#");
//				$x->loadModel($this->bd->getTypeModule());
				//Load Etesting::Tests
				//$x->loadModel("tladefinitions#");//Load Etesting::Tests
				$this->usergroup=$this->bd->getGroup($login);
				$lg = $this->getModuleDeflg();
				$this->model=$x;

				$this->lg = $lg;

				$this->deflg = $lg;
				$x->modelManager=$this;

				//Update database structure to the new format including privileges informations
				//$x->updateIfneededModelofDatabase();

				$x->getFilter("read");
				$x->getFilter("edit");
				$x->getFilter("delete");



				//$x->getrightsSQLrestriction();
				//$x->getwrightsSQLrestriction();
				$this->model=$x;

				error_reporting(E_ALL);
				return TRUE;
			 }
	}

function isClass($resource)
	{
	  $this->model->con = $this->bd->con;
	  return $this->model->isClass($resource);
	}

function cloneit($resource)
	{
	  $this->model->con = $this->bd->con;
	  return $this->model->cloneit($resource);
	}

   /**
    * example of sparql query
    * SELECT ?resource
    * WHERE (?resource info:age ?age)
    * AND ?age >= 24
    * USING info FOR <http://example.org/peopleInfo#>
    * @param string $sparqlquery An rdql or sparql query.
    * @return array
    * @public
    */

function sparql($query) {

				define('RDFAPI_INCLUDE_DIR', dirname (__FILE__).'/../../../include/powl/RDFLayer/rdfapi-php/api/');
				define('RDFSAPI_INCLUDE_DIR', dirname (__FILE__).'/../../../include/powl/RDFSLayer/rdfsapi/');
				define('OWLAPI_INCLUDE_DIR', dirname (__FILE__).'/../../../include/powl/OWLLayer/owlapi/');
				define('RDF_NAMESPACE_URI','http://www.w3.org/1999/02/22-rdf-syntax-ns#' );
				define('RDF_NAMESPACE_PREFIX','rdf' );
				define('RDF_RDF','RDF');
				define('RDF_DESCRIPTION','Description');
				define('RDF_ID','ID');
				define('RDF_ABOUT','about');
				
				$GLOBALS['dbConf']['type'] = SGBD_DRIVER;
				$GLOBALS['dbConf']['host'] = DATABASE_URL;
				$GLOBALS['dbConf']['database'] = $this->currentModuleDatabase;
				$GLOBALS['dbConf']['user'] = DATABASE_LOGIN;
				$GLOBALS['dbConf']['password'] = DATABASE_PASS;
				
				include_once(RDFSAPI_INCLUDE_DIR."rdfsapi.php");
				include_once(OWLAPI_INCLUDE_DIR."owlapi.php");
				include_once(RDFAPI_INCLUDE_DIR."syntax/RdfSerializer.php");

//include('w:/www/config/config.php');
		error_reporting(E_ALL);
		ob_start();
		$pOWLStore = new POWLStore(ADODB_DB_DRIVER,ADODB_DB_HOST,$this->currentModuleDatabase,ADODB_DB_USER,ADODB_DB_PASSWORD);
     //logVar($pOWLStore, 'pOWLStore object');
      error_reporting(E_ALL);

      $mymodel = $pOWLStore->getModel($this->bd->getNamespace());
      //logVar($mymodel, 'mymodel object');
      error_reporting(E_ALL);
	  echo $query;

      $result = $mymodel->sparqlQuery($query,"xml");
	  ob_clean();
      error_reporting(E_ALL);
      // TODO: when the query refers to an unknown URI, there is a non handled FATAL error...

	unset($GLOBALS['dbConf']);
	 return $result;
   }

 /**
   * @param $keywordsArray A list of keywords in a array.
   * @return array[][] Vector of triples describing matching resources.
   * @public
   */

function fullTextSearch($keywordsArray) {
      $sqlwhereclause = $this->genSQL($keywordsArray);
      $rows = $this->execSQL($sqlwhereclause, array(""));
      $tripleArray = array();
      for ($i = 0; $i < count($rows); $i++) {
         $arow = $rows[$i];
         $triple = array("");
         foreach ($arow as $key=>$val) {
            if ($key == 'subject') {
               $triple['subject'] = $val;
            } else if ($key == 'predicate') {
               $triple['predicate'] = $val;
            } else if ($key == 'object') {
               $triple['object'] = $val;
            }
         }
         $tripleArray[$i] = $triple;
      }
	  $xmlrdf='<?xml version="1.0" encoding="UTF-8"?>
      <rdf:RDF
		   xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
	  ';
      foreach ($tripleArray as $triple)
		{
$xmlrdf.='<rdf:Statement rdf:ID="st'.rand(0,165535).'">
<rdf:subject rdf:resource="'.$triple["subject"].'"/>
<rdf:predicate rdf:resource="'.$triple["predicate"].'"/>
<rdf:object>'.$triple["object"].'</rdf:object>
</rdf:Statement>
';
		}

	  return $xmlrdf."</rdf:RDF>";
   }

/**
 * This function takes an array of (string) keywords and returns the associated sql where clause
 * @private
 */

function genSQL($POST_CRITERIA) {
      $sqlwhereclause="";
      //returns namespace of knowledge base
      $moduleURI = $this->bd->getNamespace();
      error_reporting(E_ALL);
      $needimbricatedqueries=false;
      if (sizeOf($POST_CRITERIA)>1) {
         $needimbricatedqueries = true;
      }

      foreach ($POST_CRITERIA as $key=>$val) {
         /*prevents any html markups automatically generated by some funny web browser in input fields*/
         $val = trim(strip_tags($val));

         /*!= &nbsp; thanks to the same funny browser*/
         if (($val!="") and ($val!="NULL") and  ($val!="&nbsp;")) {
            //Check if a uri was set as value, if yes the namespace is added
            if (strpos(urldecode($val),"#")===0) {
               $val =$moduleURI.$val;
            }
            if ($needimbricatedqueries) {
               $sqlwhereclause.=" AND subject in (select subject from statements where object LIKE '%".urldecode($val)."%')";
            } else {
               $sqlwhereclause.=" AND object LIKE '%".urldecode($val)."%'";
            }
         }
      }
      return $sqlwhereclause;
   }

/**
* Returns true is connected user is admin, false otherwise
**/
function isAdmin()
	{
		return $this->admin;
	}
	/*
*Return the author of a resource
*@param @resource short rdf id example #i4 or #c48, etc.
*@return array(login, crypted password, umask, admin, usergroup, lastname,firstname,E_mail,Company,Deflg,enabled,IDRDF,rdf,mask,author,enabled,protected)
*/
function getAuthor($resource)
	{

		$model = $this->model;
		$model->con = $this->bd->con;

				return 	$model->getAuthor($resource);

	}
function getNamespace()
	{
		return $this->bd->getNamespace();
	}
/****************************************************************************************
*
*				PART 2 :
*		Business
*		Class, Property and Instance Management, affect rights to objects, check permissions when set or edit
*
*****************************************************************************************/
  /**
  * Creates a Class and insert it into database
  *
  * @param array $labels Labels
  * @param array $comments Comments
  * @param array $domain Domaine
  * @param string $user Author
  * @return boolean
  * @access public
  */

function setClass($lg, $labels,$comments,$domain)
{
  $this->model->con = $this->bd->con;
  return $this->model->setClass($lg,$labels,$comments,$domain,$this->user,$this->bd->getUmask($this->user));
  //return $this->bd->setClass($lg,$labels,$comments,$domain,$this->user,$this->bd->getUmask($this->user));
}


function setSequence($sequence)

	{
	  $this->model->con = $this->bd->con;
	  return $this->model->setSequence($sequence,$this->user,$this->bd->getUmask($this->user));
      //return $this->bd->setClass($lg,$labels,$comments,$domain,$this->user,$this->bd->getUmask($this->user));
	}

   /**
  * Check if user is allowed to write(edit and remove) this class
  * @param string $id
  * @return boolean
  * @access private
  */

function isAllowedtowriteClass($idclass)
	{
		$IsAllowed=FALSE;
	  $mask = $this->bd->getClassMask($idclass);
	  $author = $this->bd->getAuthor($idclass);
	  $authorGroup = $this->bd->getGroup($author);

	  $userGroup = $this->bd->getGroup($this->user);

	  if ((
		  ((substr($mask,1,1) == "1") OR (substr($mask,1,1) == "2"))
		  AND ($this->user == $author)
		 )
	  OR
		 (
		  ((substr($mask,3,1) == "1") OR (substr($mask,3,1) == "2"))
		  AND ($authorGroup == $userGroup)
		 )

	  OR
		 (
		  (substr($mask,5,1) == "1") OR (substr($mask,5,1) == "2")
		 )

	  OR ($this->admin))



		{$IsAllowed=TRUE;} else {$isAllowed=FALSE;}
		return $IsAllowed;

	}

  /**
  * Removes a Class only if user is allowed or admin
  * Removes all informations linked to this class, access rights, links to properties
  * But not properties and instances even these properties, instances had as Domain/ only this class (MAY be
  * changed)
  * @param string $id
  * @return boolean
  * @access public
  */

function removeClass($idclass)
	{
		$this->model->con = $this->bd->con;
		$this->model->removeRDFSResource($idclass,"c");


	}
/**
*Returns direct subclasses of $idclass
*@param TAO:session $pSession returned by authenticate service
*@access public
*@param Array([0] => String) $idclass : array(14) (without #c) id of class
*@param Array([0] => String) $remotedocKey : is optional. It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule"
*@return Array()

**/

function getsubClasses($idclass,$remotedockey)
	{


		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->getsubClasses($idclass);
	}

/**
*performs search among models

**/

function search($pCriteria,$remotedockey,$exact)
	{


		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->search($pCriteria,$exact);
	}
function searchInstances($pCriteria,$remotedockey,$exact)
	{


		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->searchInstances($pCriteria,$exact);
	}
/**
*Returns value (litteral or ressource) of a property about an instance
*@param TAO:session $pSession returned by authenticate service
*@param Array([0] => String) $instance : 14 (without #i) id of instance
*@param Array([0] => String) $propertyName : 14 (without #i) id of property
*@param Array([0] => String) $remotedocKey : is optional. It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule"
*@access public
**/
function GetInstancePropertyValues($instance, $propertyName,$remotedockey)
	{


		$model = $this->model;
		$model->con = $this->bd->con;

		return 	$model->GetInstancePropertyValues($instance, $propertyName);
	}
function GetInstancePropertyLgs($instance, $property,$remotedockey)
	{


		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->GetInstancePropertyLgs($instance, $property);

	}
function GetLgs()
	{


		$model = $this->model;
		$model->con = $this->bd->con;
				return 	$model->GetLgs();

	}
function execSQL($WHERE_CLAUSE,$remotedockey)
	{



		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->execSQL($WHERE_CLAUSE);

	}
/**
*Returns Description of a class (label comment, subclassof, properties, etc.)
*@param TAO:session $pSession returned by authenticate service
*@access public
*@param Array([0] => String) $idcass : array(14) (without #i) id of class
*@param Array([0] => String) $remotedocKey : is optional. It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule"
*@return Array()
**/
function getClassDescription($idclass,$remotedockey)
	{
		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->getClassDescription($idclass);
	}
function getRessourceDescription($Ressource,$remotedockey)
	{
		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->getRessourceDescription($Ressource);
	}

function getrdfStatements($Ressource,$remotedockey)
	{
		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->getrdfStatements($Ressource);
	}
 function getPrivileges($uri,$remotedockey)
	{
		 $model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->getPrivileges($uri);
	}
function getMethods($tripleId,$remotedockey)
	{
		 $model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->getMethods($tripleId);
	}

function check_SetStatement($subject, $predicate, $object,$remotedockey)
	{
		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->check_SetStatement($subject, $predicate, $object);
	}
	 function setPrivileges($uri,$method,$privs,$user,$remotedockey)
	{
		 $model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->setPrivileges($uri,$method,$privs,$user) ;
	}
function setPrivilegesonStatement($statement,$privilege)
	{
		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->setPrivilegesonStatement($statement,$privilege);
	}



function getLabelComment($Ressource,$remotedockey)
	{



		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->getLabelComment($Ressource);

	}

function isKnownModel($uri,$remotedockey)
	{


		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->isKnownModel($uri);
	}

 /**
*@return Array Returns instance description
*@param Array([0] => String) $idinstance : array(14) (without #i)
*@param TAO:session $pSession returned by authenticate service
*@access public
*
**/
function getInstanceDescription($idinstance,$remotedockey,$onlylabelcomment=false)
	{



		$model = $this->model;
		$model->con = $this->bd->con;

							return 	$model->getInstanceDescription($idinstance,$onlylabelcomment);

	}

 /**
*remove statement from model
*
**/
function removeSubjectPredicate($subject,$predicate)
	{

		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->removeSubjectPredicate($subject,$predicate);

	}
 /**
*remove statement from model
*
**/
function removeStatement($statement)
	{

		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->removeStatement($statement);

	}
function removeSubjectPredicateValue($subject,$predicate,$value)
	{



		$model = $this->model;
		$model->con = $this->bd->con;

				return 	$model->removeSubjectPredicateValue($subject,$predicate,$value);

	}
function removeSubject($subject)
	{



		$model = $this->model;
		$model->con = $this->bd->con;

				return 	$model->removeSubject($subject);

	}

/*************************************************************************************

Rdf model



**************************************************************************************/
/*
*	Adds statement to the knowledge base
*/
function setStatement($subject, $predicate, $object, $object_is,$lg, $l_datatype, $subject_is,$mask="")
	{



		$model = $this->model;
		$model->con = $this->bd->con;

		return $model->setStatement($subject, $predicate, $object, $this->user,$mask,$object_is,$lg, $l_datatype, $subject_is);

		return  array("ok","ok","ok");


	}
/**************************************************************************************/
/*
*	edit the statement $tripleid  with provided params
**************************************************************************************/
function editStatement($tripleid, $object, $object_is,$lg, $l_datatype, $subject_is)
	{



		$model = $this->model;
		$model->con = $this->bd->con;

		return $model->editStatement($tripleid,  $object, $object_is,$lg, $l_datatype, $subject_is);

		return  array("ok","ok","ok");


	}
/**
*Returns Description of a property (label comment, domain, widget,range)
*@param TAO:session $pSession returned by authenticate service
*@access public
*@param Array([0] => String) $idproperty : array(14) (without #p) id of property
*@param Array([0] => String) $remotedocKey : is optional. It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule"
*@return Array()

**/
function getPropertyDescription($idproperty,$remotedockey)
{



		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->getPropertyDescription($idproperty);

	}
/**
*Returns all instances of $idclass
*
*@param TAO:session $pSession returned by authenticate service
*@access public
*@param Array([0] => String) $idclass : array(14) (without #c) id of class
*@param boolean $indirect includes instances of subclasses recursively.
*@param Array([0] => String) $remotedocKey : is optional. It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule"
@return Array()

**/

function getInstances($idclass,$remotedockey,$indirect=false)
	{



		$model = $this->model;
		$model->con = $this->bd->con;

		return 	$model->getInstances($idclass,$indirect);

	}
function getTopClasses($remotedockey)
	{



		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->getTopClasses();

	}
function getTopMetaClasses($remotedockey)
	{


		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->getTopMetaClasses();

	}
function getIndirectsubClasses($idclass,$remotedockey)
	{



		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->getIndirectsubClasses($idclass);
	}
/**
*Returns all subclasses of $idclass
*
*@param TAO:session $pSession returned by authenticate service
*@access public
*@param Array([0] => String) $idclass : array(14) (without #c) id of class
*@param Array([0] => String) $remotedocKey : is optional. It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule"
@return Array()

**/
function getAllClasses($idclass,$remotedockey)
	{



		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->getAllClasses($idclass);
	}

function getindirectSuperClasses($URIClass,$remotedockey)
	{



		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->getindirectSuperClasses($URIClass);
	}

function GetClassPath($element,$remotedockey=array(""))

{



		$model = $this->model;
		$model->con = $this->bd->con;



		$URIClass = $model->modelURI.$element;
		$ClassPath=$this->getindirectSuperClasses($URIClass,$remotedockey);
		//FIXME PPL Do not understand what is the goal of that ?
		foreach ($ClassPath as $key=>$val)
		{$ClassPath[$key]=$val;
		}
		return $ClassPath;
}

function getProperties($idclass,$remotedockey)
	{


		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->getProperties($idclass);
	}
function getsubProperties($idproperty,$remotedockey)
	{


		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->getsubProperties($idproperty);
	}
 function getAllProperties($idclass,$remotedockey)
	{



		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->getAllProperties($idclass);

	}

  /**
  * Edit a Class only if user is allowed
  * Author and rights aren't modified with data of editor
  * labels and comments are vectors which contains edited values, lg specify used languages,
		domain contains ALL parent Classes
  * @param string $id
  * @param array $labels Labels
  * @param array $comments Comments
  * @param array $domain Domaine
  * @return boolean
  * @access public
  */


  function editClass($idclass,$lg, $labels,$comments,$domain)
	{

	    $this->model->con = $this->bd->con;

	   return $this->model->editClass($idclass,$lg,$labels,$comments,$domain,$this->user,$this->bd->getUmask($this->user));


	}

  /**
  * Local Rights managements for a Class and User
  *getLocalRightsClass
  * @param string $id id of Class
  * @return boolean
  * @access public
  */
	function getLocalRightsClass($idclass)
	{
      $mask = $this->bd->getClassMask($idclass);
	  return $mask;
	}
 /**
  * Local Rights managements for a Class only if user is allowed
  * editLocalRightsClass
  * @param string $id id of Class
  * @param string $localrights id of Class
  * @param string $user Active user
  * @return boolean
  * @access public

	function editMaskofClass($idclass,$localrights)
	{
      if (! ($this->bd->isClassProtected($idclass)))
		{
			if ($this->isAllowedtowriteClass($idclass))
			{return $this->bd->editLocalRightsClass($idclass,$localrights);}
			else {return "You are not allowed to do this";}
		} else return "Class is protected";

	}
  */

  /**
  * Remote Rights managements for a Class
  *	Get Groups Rights for a Class
  * @param string $id id of Class
  * @param string $id id of group of subscribers
  * @return string

	function getGroupsRightsClass($idclass,$idgroup)
	{


		return $this->bd->getGroupsRightsClass($idclass,$idgroup);

	}
*/
  /**
  * Remote Rights managements for a Class
  *	Get Groups Rights for a Class
  * @param string $id id of Class
  * @param string $id id of group
  * @param string $value value
  * @return boolean

	function editGroupsRightsClass($idclass,$idgroup,$value)
	{

      if (! ($this->bd->isClassProtected($idclass)))
		{
			if ($this->isAllowedtowriteClass($idclass))
			{$this->bd->editGroupsRightsClass($idclass,$idgroup,$value);}
			else {return "You are not allowed to do this";}
		} else return "Class is protected";


	}
	*/
  //Section Property Management

  /**
  * Creates a Property and insert it into knowledge base
  * If creation of properties like "Combobox, radio, etc." :
  * Creates a Class (A label, a domain) : see creation of a class
  * Creates as much as instances as values for this property
  * When creating a property, his range is this class
  * Creation of this property
  * @param array $labels Labels
  * @param array $comments Comments
  * @param array $domain Domaine (Classes described by this property)
  * @param string $range Sample : "rdfs:Literal" in case of a literal , "http://URL/trucs.rdf#c1"
  * @param string $widget Sample : "Combobox", "RadioBox", etc.
  * @return boolean
  * @access public
  */

  function setProperty($lg, $labels,$comments,$domain,$range, $widget)
	{
		  $this->model->con = $this->bd->con;
		return $this->model->setProperty($lg,$labels,$comments,$range,$domain, $widget, $this->user,$this->bd->getUmask($this->user));
    }


  /*
  function isAllowedtowriteProperty($idproperty)
	{

	  $IsAllowed=FALSE;
	  $mask = $this->bd->getPropertyMask($idproperty);
	  $author = $this->bd->getAuthorP($idproperty);
	  $authorGroup = $this->bd->getGroup($author);
	  $userGroup = $this->bd->getGroup($this->user);
	  if ((
		  ((substr($mask,1,1) == "1") OR (substr($mask,1,1) == "2"))
		  AND ($this->user == $author)
		 )
	  OR
		 (
		  ((substr($mask,3,1) == "1") OR (substr($mask,3,1) == "2"))
		  AND ($authorGroup == $userGroup)
		 )

	  OR
		 (
		  (substr($mask,5,1) == "1") OR (substr($mask,5,1) == "2")
		 )

	  OR ($this->admin))
		{$IsAllowed=TRUE;} else {$isAllowed=FALSE;}
		return $IsAllowed;
}
	*/
  /**
  * Removes a Property only if user is allowed
  *
  * @param string $user user
  * @param string $range datatype
  * @return boolean
  * @access public
  */
  function removeProperty($idproperty)
		{
		$this->model->con = $this->bd->con;
		$this->model->removeRDFSResource($idproperty,"p");
		}


  /**
  * Edit a Property only if user is allowed
  *labels and comments are vectors which contains edited values, lg specify used languages,
		domain contains ALL  Classes described, range and widget : one string
 * @return string
  * @access public
  */
  function editProperty($idproperty, $lg, $labels,$comments,$domain,$range,$widget)

	{
		$this->model->con = $this->bd->con;
		return $this->model->editProperty($idproperty,$lg, $labels,$comments,$domain,$range, $widget,$this->user,$this->bd->getUmask($this->user));

	}


  /**
  * Local Rights managements for a Property and User
  *getLocalRightsProperty
  * @param string $id id of property
  * @return boolean
  * @access public
  */
	function getLocalRightsProperty($idproperty)
	{
		$mask = $this->bd->getPropertyMask($idproperty);

	    return $mask;

	}
 /**
  * Local Rights managements for a Property only if user is allowed
  * editLocalRightsProperty
  * @param string $id id of property
  * @param string $localrights id of property
  * @param string $user Active user
  * @return boolean
  * @access public
  */
	function editLocalRightsProperty($idproperty,$localrights)


		{
			if (! ($this->bd->isPropertyProtected($idproperty)))
			{

		if ($this->isAllowedtowriteProperty($idProperty))
		{$this->bd->editLocalRightsProperty($idproperty,$localrights);
		} else {return "You are not allowed to do this";}
			} else {return "Property protected";}

		}


  /**
  * Remote Rights managements for a Property only for administrator
  *	Get Groups Rights for a property
  * @param string $id id of property
  * @param string $id id of group
  * @return string
  */
	function getGroupsRightsProperty($idproperty,$idgroup)
	{
	return $this->bd->getGroupsRightsProperty($idproperty,$idgroup);
	}
  /**
  * Remote Rights managements for a Property only for administrator
  *	Get Groups Rights for a property
  * @param string $id id of property
  * @param string $id id of group
  * @param string $value value
  */
	function editGroupsRightsProperty($idproperty,$idgroup,$value)
	{

      if (! ($this->bd->isPropertyProtected($idproperty)))
		{
			if ($this->isAllowedtowriteProperty($idproperty))
			{$this->bd->editGroupsRightsProperty($idproperty,$idgroup,$value);}
			else {return "You are not allowed to do this";}
		} else return "Property is protected";


	}


  /**
  * Creates an Instance and insert it into database
  *
  * @param array $labels Labels
  * @param array $comments Comments
  * @param String $type id of class
  * @param array $lg array of language
  * @return boolean
  * @access public
  */
function setInstance($lg, $labels,$comments,$type)
	{
		$this->model->con = $this->bd->con;
		return $this->model->setInstance($lg,$labels,$comments,$type, $this->user,$this->bd->getUmask($this->user));
		return $idinstance;
	}


 function isAllowedtowriteInstance($idinstance)
	{
	 $IsAllowed=FALSE;
	  $mask = $this->bd->getInstanceMask($idinstance);

	  $author = $this->bd->getAuthorI($idinstance);


	  $authorGroup = $this->bd->getGroup($author);
	  $userGroup = $this->bd->getGroup($this->user);
	  if ((
		  ((substr($mask,1,1) == "1") OR (substr($mask,1,1) == "2"))
		  AND ($this->user == $author)
		 )
	  OR
		 (
		  ((substr($mask,3,1) == "1") OR (substr($mask,3,1) == "2"))
		  AND ($authorGroup == $userGroup)
		 )

	  OR
		 (
		  (substr($mask,5,1) == "1") OR (substr($mask,5,1) == "2")
		 )

	  OR ($this->admin))

		{$IsAllowed=TRUE;} else {$isAllowed=FALSE;}
		return $IsAllowed;
	}

	/**
  * Creates values for a propert for an instance
  *
  * @param array $values values in different languages
  * @param string  $idproperty
  * @param String $type id of class
  * @param array $lg array of language
  * @return boolean
  * @access public
  */
function setPropertyValuesforInstance($idinstance,$Idproperty, $lg,$values)
	{$this->model->con = $this->bd->con;
		return $this->model->setPropertyValuesforInstance($idinstance,$Idproperty, $lg,$values,$this->bd->getUmask($this->user));

	}
function affiliate($idinstance,$idproperty, $memberlist)
	{
	$this->model->con = $this->bd->con;
		return $this->model->affiliate($idinstance,$idproperty, $memberlist);

	}
function unaffiliate($idinstance,$idproperty, $memberlist)
	  {
		$this->model->con = $this->bd->con;
		return $this->model->unaffiliate($idinstance,$idproperty, $memberlist);

	}



function removePropertyValuesforInstance($idinstance,$Idproperty)
	{


			$this->model->con = $this->bd->con;
		return $this->model->removePropertyValues($idinstance,$Idproperty);
	}

  /**
  * Removes an Instance only if user is allowed
  *
  * @param string $id ID of Instance
  * @return boolean
  * @access public
  */
  function removeInstance($idInstance)
	{
		$this->model->con = $this->bd->con;
		$this->model->removeRDFSResource($idInstance,"i");
	}


  /**
  * Edit an Instance only if user is allowed
  *labels and comments are vectors which contains edited values, lg specify used languages,
		type :one class
* @param string $id ID of Instance
  * @param array $labels Labels
  * @param array $comments Comments
  * @param array $domain Domaine
  * @return boolean
  * @access public
  */

  function editInstance($idInstance,$lg, $labels,$comments,$type)

      {
		 $this->model->con = $this->bd->con;
		return $this->model->editInstance($idInstance, $lg, $labels,$comments,$type,$this->user,$this->bd->getUmask($this->user));

	}


 function isSubClassOf($pClass, $pSubClass)

      {
		 $this->model->con = $this->bd->con;
		return $this->model->isSubClassOf($pClass, $pSubClass);

		}




// Only if a property may take only one value for a language, in case of affiliation, first remove property value pair, then setpropertyvalue for this pair

	function editPropertyValuesforInstance($idInstance,$idProperty,$lg,$values)
	  {

		$this->model->con = $this->bd->con;

		return $this->model->editPropertyValuesforInstance($idInstance,$idProperty,$lg,$values,$this->bd->getUmask($this->user));

	}






  /**
  * Local Rights managements for an Instance and User
  *getLocalRightsProperty
  * @param string $id id of property
  * @return boolean
  * @access public
  */
	function getLocalRightsInstance($idInstance)
	{
      $mask = $this->bd->getInstanceMask($idInstance);

	    return $mask;
	}
 /**
  * Local Rights managements for a Instance only if user is allowed
  * editLocalRightsInstance
  * @param string $id id of Instance
  * @param string $localrights id of Instance
  * @param string $user Active user
  * @return boolean
  * @access public
  */
	function editLocalRightsInstance($idInstance,$localrights)
	{
      $IsAllowed=FALSE;
	  $mask = $this->bd->getInstanceMask($idInstance);
	  $author = $this->bd->getAuthor($idInstance);
	  $authorGroup = $this->bd->getGroup($author);
	  $userGroup = $this->bd->getGroup($this->user);
		 if ((
		  ((substr($mask,1,1) == "1") OR (substr($mask,1,1) == "2"))
		  AND ($this->user == $author)
		 )
	  OR
		 (
		  ((substr($mask,3,1) == "1") OR (substr($mask,3,1) == "2"))
		  AND ($authorGroup == $userGroup)
		 )

	  OR
		 (
		  (substr($mask,5,1) == "1") OR (substr($mask,5,1) == "2")
		 )

	  OR ($this->admin))
		{$IsAllowed=TRUE;} else {$isAllowed=FALSE;}
		if ($IsAllowed)
		{$this->bd->editLocalRightsInstance($idInstance,$localrights);}
		else {return "You are not allowed to do this";}
	}



  /**
  * Remote Rights managements for an Instance only for administrator
  *	Get Groups Rights for an Instance
  * @param string $id id of Instance
  * @param string $id id of group
  * @return string
  */
	function getGroupsRightsInstance($idinstance,$idgroup)
	{
	return $this->bd->getGroupsRightsInstance($idinstance,$idgroup);
	}
 /**
  * Remote Rights managements for a Property only for administrator
  *	Get Groups Rights for an Instance
  * @param string $id id of Instance
  * @param string $id id of group
  * @param string $value value
  * @return boolean
  */
	function editGroupsRightsInstance($idinstance,$idgroup,$value)
	{

      if (! ($this->bd->isInstanceProtected($idinstance)))
		{
			if ($this->isAllowedtowriteInstance($idinstance))
			{$this->bd->editGroupsRightsInstance($idinstance,$idgroup,$value);}
			else {return "You are not allowed to do this";}
		} else return "Instance is protected";


	}

/****************************************************************************************
*
*				PART 3 :
*		Business
*		Subscribee Management only for administrator
*
*****************************************************************************************/


	function addSubscribee($url,$login,$password,$type,$md)
	{
		if ($this->admin) {return $this->bd->addSubscribee($url,$login,$password,$type,$md);}
		else {return "Only admin"; }
	}
	function editSubscribee($idsubscribee,$url,$login,$password,$type,$md)
	{
		if ($this->admin) {$this->bd->editSubscribee($idsubscribee,$url,$login,$password,$type,$md);}
		else {return "Only admin"; }
	}
	function removeSubscribee($idsubscribee)
	{
		if ($this->admin) {$this->bd->removeSubscribee($idsubscribee);}
		else {return "Only admin"; }
	}
	function getSubscribee()
	{

		if ($this->admin) {return $this->bd->getSubscribee();}
		else {return "Only admin"; }
	}

	function getSubscribeeaslist()
		{

		if ($this->admin) {return $this->bd->getSubscribeeaslist();}
		else {return "Only admin"; }
	}

	function getSubscribeeasUser()
	{
		return $this->bd->getSubscribeeasUser();
	}

function getSubscribeesurl($type,$url)
			{
		return $this->bd->getSubscribeesurl($type,$url);
			}
/****************************************************************************************
*
*				PART 4 :
*		Business
*		User Management only for administrator
*
*****************************************************************************************/

	  /**
  * GetInstanceDescription
   * @login login of created user
  * @param password PASSWORD is already crypted with md5
  * @umask default u-mask for this user
  * @admin string[1] if 1 admin else 0
  * @return $bol
  */
	function addUser($login,$password,$umask,$admin,$lastname,$firstname,$email,$company,$deflg,$enabled,$usergroup)
	{
		if ($this->admin) {$this->bd->addUser($login,$password,$umask,$admin,$lastname,$firstname,$email,$company,$deflg,$enabled,$usergroup);}
		else {return "You are not allowed to do this";}
	}

	/**
	* checkIfLoginalreadyexists($login)
	* @login login
	* @return string 1 = exists, 0 not exists
	*/

	function checkIfLoginalreadyexists($login)
	{
		if ($this->admin) {return $this->bd->checkIfLoginalreadyexists($login);}
		else {return "You are not allowed to do this";}
	}

	function editUser($login,$password,$umask,$admin,$lastname,$firstname,$email,$company,$deflg,$enabled,$usergroup)
	{
		if (($this->admin) OR ($login==$this->user)) {$this->bd->editUser($login,$password,$umask,$admin,$lastname,$firstname,$email,$company,$deflg,$enabled,$usergroup);}
		else {return "You are not allowed to do this";}
	}

	/**
	* add a new pattern to the mask of the connected user, with the scope $scope. Mask may be modified using the edituser (...$mask...)
	* service.
	**/
	function addPattern($scope,$method="-")
	{
		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$this->bd->addPattern($scope,$this->user,$method);
	}

	function removeUser($login)
	{
		if ($this->admin) {$this->bd->removeUser($login);}
		else {return "You are not allowed to do this";}
	}
	function affiliateUserGroup($login,$idGroup)
	{

		if ($this->admin) {$this->bd->affiliateUserGroup($login,$idGroup);}
		else {return "You are not allowed to do this";}

	}
	/*returns an array of array (Group, members Description)*/
	function getgroupsmembers()
	{	if ($this->admin) {return $this->bd->getgroupsandusers();}
		else {return "You are not allowed to do this";}
	}
function getgroups()
	{	if ($this->admin) {return $this->bd->getgroups();}
		else {return "You are not allowed to do this";}
	}
	function getUserdescription($user)
	{	if (($this->admin) OR ($user==$this->user)) {return $this->bd->getUserdescription($user);}
		else {return "You are not allowed to do this";}
	}


	function addGroup($name)
	{
		if ($this->admin)
			{$this->bd->addGroup($name);}
		else {return "You are not allowed to do this";}
	}
	function editGroup($name,$newname)
	{
		if ($this->admin)
			{$this->bd->editGroup($name);}
		else {return "You are not allowed to do this";}
	}
	function removeGroup($name)
	{
		if ($this->admin)
			{$this->bd->removeGroup($name);}
		else {return "You are not allowed to do this";}
	}

/****************************************************************************************
*
*				PART 5 :
*		Business
*		Subscriber Management only for administrator
*
*****************************************************************************************/

    function addSubscriber($login,$password,$enabled)
	{
		if ($this->admin)
			{
			return $this->bd->addSubscriber($login,$password,$enabled);
			}
		else {return "You are not allowed to do this";}


	}

	function editSubscriber($idsubscriber,$login,$password,$enabled)
	{
		if ($this->admin)
			{
			$this->bd->editSubscriber($idsubscriber, $login,$password,$enabled);
			}
		else {return "You are not allowed to do this";}
	}

	function removeSubscriber($idsubscriber)
	{
		if ($this->admin)
			{
			$this->bd->removeSubscriber($idsubscriber);
			}
		else {return "You are not allowed to do this";}
	}
	/*
	*Affiliate a subscriber to a group of subscriber
	**/
	function affiliateSubscriberGroup($idsubscriber,$idGroup)
	{
		if ($this->admin)
			{
			$this->bd->affiliateSubscriberGroup($idsubscriber,$idGroup);
			}
		else {return "You are not allowed to do this";}

	}

	/*
	*Affiliate a group of subscriber to another 1 Group of subscriber is affiliated to 1 group of subscriber
	**/

	function affiliateGroupGroup($idgroupFather,$idGroupSon)
	{
		if ($this->admin)
			{
			$this->bd->affiliateGroupGroup($idgroupFather,$idGroupSon);
			}
		else {return "You are not allowed to do this";}

	}


	function addSubscriberGroup($name)
	{
		if ($this->admin)
			{
			return $this->bd->addSubscriberGroup($name);
			}
		else {return "You are not allowed to do this";}
	}

	function editSubscriberGroup($idGroup,$name)
	{
		if ($this->admin)
			{
			$this->bd->editSubscriberGroup($idGroup,$name);
			}
		else {return "You are not allowed to do this";}
	}

	function removeSubscriberGroup($idGroup)
	{
		if ($this->admin)
			{
			$this->bd->removeSubscriberGroup($idGroup);
			}
		else {return "You are not allowed to do this";}
	}

	function getSubscriberDescription($idsubscriber)
	{
		if ($this->admin)
			{
			return $this->bd->getSubscriberDescription($idsubscriber);
			}
		else {return "You are not allowed to do this";}
	}



	function getAllMembersofSubscribergroup($idGroup)
	{
		return $this->bd->getAllMembersofSubscribergroup($idGroup);

	}



	/*Renvoie un array javascript r�cursif id, nom du groupes, ensemble des mebres, ensemble des sous groupes[...]*/
	function getGroupsSubscribersMembers($onlygroups)
	{
		return $this->bd->getGroupsSubscribersMembers("0",$onlygroups);



	}

		/*Renvoie un array PHP r�cursif id, nom du groupes, ensemble des mebres, ensemble des sous groupes[...]*/
	function getGroupsSubscribersRightsonResource($ressource)
	{

		return $this->bd->getGroupsSubscribersRightsonResource("0",$ressource);

	}

	function getRecursivesubgroups($idgroup)
	{
		if ($this->admin)
			{
			return $this->bd->getRecursivesubgroups($idgroup);
			}
		else {return "You are not allowed to do this";}
	}

/****************************************************************************************
*
*				PART 5 :
*		Parameters
*		Timeout : only administrator
*		Def lg : for a user : see user management
*				 global : only administrator
*
*****************************************************************************************/

function setTimeout($timeout)
	{	 if ($this->admin)
		{
		return $this->bd->setTimeout($timeout);
		}
	}

function getTimeout()
	{
		return $this->bd->getTimeout();
	}

function getModuleDeflg()
	{
		return $this->bd->getModuleDeflg();
	}
function setModuleDeflg()
	{
		return $this->bd->setModuleDeflg();
	}



function getMyDeflg()
	{
		return $this->bd->getMyDeflg($this->user);
	}

function setMyDeflg($lg)
	{
		if ($this->admin)
		{
			return $this->bd->setMyDeflg($this->user,$lg);
		}
	}




/*Backup



		$model = $this->model;
		$model->con = $this->bd->con;
		return 	$model->getClassDescription($idclass);


*/
}
?>
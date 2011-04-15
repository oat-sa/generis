<?php
/**
* Implements all available services/requests provided by the generis kernel 
* May be used by user interfaces or other applications (plugins, etc.) 
* OO api
* Manage semantic web resources, users, subscribers subscribees within a selected Module. A session mechanism is
* used to prevent unauthorized access or requests to the kernel.
* Session contains informations about connected entity its rights (u-mask), informations about
* the models he is consulting or modifying  and some other contextual
* informations
* This session is returned by the authentication service
* @package kernel
* @author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 2.0
*/
class API_impl
{
//Connection to the database AdoDb object
private $con;
//Models manager reference modelManager Object
private $modelManager;
/**
* Returns error messages according to $status
*/
public function getErrorMessage($errorCode)
	{
		return $errorCode;
	}
/** 
*	connects to the database and returns $con
*	@param $currentModuleDatabase name of the module you want to connect to
**/
private function connection($currentModuleDatabase)
	{	
		$this->con = NewADOConnection(SGBD_DRIVER);
	    //creates the connection to the database using parameters defined in the config.php file
		$this->con->Connect(DATABASE_URL, DATABASE_LOGIN, DATABASE_PASS, $currentModuleDatabase);
        $this->con->debug = false;
		return $this->con;
	}
/**
* Authenticate an user or a third application with specified Module in $moduleName
*@access public
*@param String $login login 
*@param String $password password 
*@param String  $role authenticate as admin if allowed String:="0" as user String:="1" 
*@param  String moduleName the Name of module on which to connect to
*@return String status code
**/
public function authenticate($login,$password,$role,$moduleName)
	{	
		$modelManager = new modelManager($moduleName);
		//creates the initial connection to the database of the selected Module
		$modelManager->bd->con=$this->connection($modelManager->currentModuleDatabase);
		$this->modelManager = $modelManager;
		$bool = $this->modelManager->authenticate($login,$password,$role);
		$this->modelManager->getTypeModule();
		if ($bool)	{return "0";} else { return "1";}
	}
/**
*Returns all instances of $idclass
*@access public
*@param String uriClass $if of class may be short uri (without namespace , local namespace is implicit)
*@param boolean $indirect includes or not instances of subclasses recursively (may be cpu consumming if set, depends of the model complexity).
@return Array([instanceUri] => array("InstanceKey" => Uri of instance,["InstanceLabel"] => label of Instance,
            [InstanceComment] => comment of instance ))
@example 
	$api->getInstances("http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject");
	Array
	(
    [http://127.0.0.1/middleware/demoSubjects.rdf#i113567184225066] => Array
        (
            [InstanceKey] => #i113567184225066
            [InstanceLabel] => demo
            [InstanceComment] => demo
        )
    [http://127.0.0.1/middleware/demoSubjects.rdf#i115028953619450] => Array
        (
            [InstanceKey] => #i115028953619450
            [InstanceLabel] => Marc Bench

            [InstanceComment] => &nbsp;
        )
	)
**/

public function getInstances($uriClass,$indirect=false)
	{$instances=$this->modelManager->getInstances(array($uriClass),"",$indirect);return $instances["pDescription"];}


/*
*	returns complete description of a resource ($Ressource) according to its model
* @access public
* @param String $ressource uri of the ressource, may be short uri (implicit namespace)
* @return
* @example 
$resourceDecription = $api->getRessourceDescription($uriResource);
Array
(
    [type] => Array
        (
            [0] => http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject
        )
    [properties] => Array
        (
            [0] => Array
                (
                    [PropertyKey] => http://www.tao.lu/Ontologies/TAOSubject.rdf#Login
                    [PropertyLabel] => Login
                    [PropertyComment] => Login
                    [PropertyRange] => http://www.w3.org/2000/01/rdf-schema#Literal
                    [PropertyWidget] => http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox
                    [range] => http://www.w3.org/2000/01/rdf-schema#Literal
                    [widget] => http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox
                    [PropertyValue] => bench
                    [TripleID] => 7781
                )
*/
public function getRessourceDescription($ressource)
	{return $this->modelManager->getRessourceDescription($ressource,"");}	
/**
* sparql  
* @returns sparql xml messages
**/
function sparql($session,$sparql)
{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->sparql($sparql);
}

/**
* 
* @returns if $resource is class
**/
function isClass($session,$resource)
{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->isClass($resource);
}


 /**
 * @param $keywordsArray A list of keywords in a array. 
 * @return array[][] Vector of triples describing matching resources.
 * @public
 */

function fullTextSearch($session,$keywordsArray)
{
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
	$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
	return $modelManager->fullTextSearch($keywordsArray);

}


/**
*check if connected user is admin
*@param TAO:session $session returned by authenticate service
*@access public
*@return boolean returns TRUE | FALSE
**/
function isAdmin($session)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->isadmin();
}
/**
*Type of Module is some information related to the module specialisation, for instance in the e-testing context modules were specialized to subjects, groups, tests or items management purpose
*@param TAO:session TAO session returned by authenticate service
*@access public
*@return string 
**/
function getTypeModule($session)
{
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}

	 return $modelManager->getTypeModule();
}

/**
*Refresh and regenerates actual knowledge reachable by the connected entity
*retrieve rdf data, store it as triples in database and keep references into bm->rdf
*@param TAO:session TAO session returned by authenticate service
*@access public
*@return String xml/rdf for export
*/
function exportxmlRDF($pSession)
{
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($pSession[0]);$modelManager = unserialize($x);
	$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
	$xmlrdf = $modelManager->getRDF();
	
	return $xmlrdf;
}
function getOldrdf($pSession)
{
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($pSession[0]);$modelManager = unserialize($x);
	$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
	$xmlrdf = $modelManager->getOldrdf();
	
	return $xmlrdf;
}
/**
* Change language used for all returned litteral for further requests
* The contextual session being changed, update your session with the one returned
*/
function setLG($pSession,$pLg)
	{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($pSession[0]);$modelManager = unserialize($x);
		}
		$modelManager->lg=$pLg;
		//$modelManager->model->modelManager=$pLg;
		
		$temp = serialize($modelManager);
		$temp2 = urlencode($temp);
		//error_reporting(0);
		return array("pSession"=> array(0 => $temp2, 1 => $pSession[1]));
		
	}


/*
* This function is deprecated, but still present for compatibility with taov1.1 nodes of the generis network. 
* But the signature has changed : The contextual session being changed, update your session with the one returned, when used to change current language. PLease refer to setLg().
*/

function refreshXMLRDFDataforuser($pSession,$pLg,$ml=false)
	{
		
		return $pSession;
	}

/**
*
*
*      SECTION -- Relations between remote resources
*
*/

/**
* getRDFfromaremotemodule Allows connection to a remote TAO module.
*
*
* This function allows to connect to a remote module and to retriece xml/rdfs according to acces * rights between modules in order to browse, perform further queries implemented in this file on * remote knowledge instead of local knowledge
*
*
* @param Array([0] => String) $pLG deprecated, kept for compatibility
* @id Array([0] => String) Id of subscribee to connect to
* @return  array("pSession"=> array(0 => TAO:)sessionupdated,1 => NAMESPACE)) , keep namespace  in order to make queries using adequate namespace as $query_on_model parameter for further requests
*@param TAO:session $pSession returned by authenticate service
*@access public
*/		
function getRDFfromaremotemodule($pSession,$pLg="deprecated", $id,$cache=false,$askull="0")
{		
		set_time_limit (120);
		$x = urldecode($pSession[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
		return $modelManager->getRDFfromaremotemodule($id[0],$cache,$askull);
				
}

/*Anaxagora service*/
function isSubClassOf($pSession,$pClass, $pSubClass)
	{
		$x = urldecode($pSession[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
		$ret = $modelManager->isSubClassOf($pClass[0], $pSubClass[0]);
		return $ret;
	}		
function importrdfs($pSession,$urimodel,$file,$hardimport=false,$forceload=false)
{
		set_time_limit (120);
		$x = urldecode($pSession[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
		return $modelManager->importrdfs($urimodel,$file,$hardimport,$forceload);
}
function rdqlquery($pSession,$query,$like=false)
{
		
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($pSession[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		$db = $modelManager->getDB();
		
		if (is_null($query_on_model[0]) OR $query_on_model[0]=="")
			{
		$temp = $modelManager->getRDF();
		
		$query=str_replace("LOCAL",$temp[0],$query);
		
		$queries = $db->get_query_interface($temp[0]);
		
		$queries->namespace = $temp[1];
			}
		else {
		$temp2 = $modelManager->getremoteRDF();
		
		$temp=$temp2[$query_on_model[0]];
		$queries = $db->get_query_interface($temp[0]);
		$queries->namespace = $temp[1];
			 }
		
		return $queries->rdqlquery($query,$like);
		
}

/**
* deprecated
* use instead getRessourceDescription
*@return Array Returns instance description
*@param Array([0] => String) $idinstance : array(14) (without #i)
*@param TAO:session $pSession returned by authenticate service
*@access public
*
**/
function getInstanceDescription($pSession,$idinstance,$query_on_model,$modelManager=false,$onlylabelcomment=false)
	{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($pSession[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->getInstanceDescription($idinstance,$query_on_model,$onlylabelcomment=false);
	}

/**
*getXmldescription
*
**/
function getXMLDescription($pSession,$idinstance)
	{
		$x = urldecode($pSession[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
		$phpdescr =  $modelManager->getInstanceDescription(array($idinstance),array(""),false);

		 $xmlrdf='<?xml version="1.0" encoding="UTF-8"?>
		  <rdf:RDF
			   xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
		  ';
		  foreach ($phpdescr["pDescription"]["PropertiesValues"] as $triple)
			{
				$xmlrdf.='<rdf:Statement rdf:ID="st'.rand(0,165535).'">
				<rdf:subject rdf:resource="'.$idinstance.'"/>
				<rdf:predicate rdf:resource="'.$triple["PropertyKey"].'"/>
				<rdf:object>'.str_replace('&nbsp;',' ',$triple["PropertyValue"]).'</rdf:object>
				</rdf:Statement>
				';
			}

	  return $xmlrdf."</rdf:RDF>";

	}
function getNs($pSession)
	{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($pSession[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->model->modelURI;
	}
/**
*Returns value (litteral or ressource) of a property about an instance
*@param TAO:session $pSession returned by authenticate service
*@param Array([0] => String) $instance : 14 (without #i) id of instance
*@param Array([0] => String) $propertyName : 14 (without #i) id of property
*@param Array([0] => String) $query_on_model : is optional. It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule" 
*@access public
**/
function GetInstancePropertyValues($pSession,$instance, $propertyName,$query_on_model,$modelManager=false)
	{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($pSession[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		
		return $modelManager->GetInstancePropertyValues($instance, $propertyName,$query_on_model);
	}
/**
*
**/
function GetInstancePropertyLgs($pSession,$instance, $property,$query_on_model,$modelManager=false)
	{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($pSession[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->GetInstancePropertyLgs($instance, $property,$query_on_model);
	}
function GetLgs($pSession,$modelManager=false)
	{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($pSession[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->GetLgs();
	}
/**
*get Path to a class (Top-Class, sub-class, etc... , the class)
*@param TAO:session $pSession returned by authenticate service
*@param Array([0] => String) $element : a TAO resource with #c, i or p
*@return array[] : id of resources "#cX" 
*@access public
**/
function GetClassPath($pSession,$element,$query_on_model=array(""))

{
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($pSession[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
	return  $modelManager->GetClassPath($element,$query_on_model);
}

function getindirectSuperClasses($session,$URIClass,$query_on_model,$modelManager=false)
{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->getindirectSuperClasses($URIClass,$query_on_model);
}

function getIndirectsubClasses($session,$idclass,$query_on_model,$modelManager=false)
{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->getIndirectsubClasses($idclass,$query_on_model);
}

/*
* returns all generis statements about an uri, rdf level (there is no triple inferred by the rdfs level), restricted according to rights priviliges set on statements
*/
 function getrdfStatements($session,$Ressource,$query_on_model,$modelManager=false)
	{	
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
		}
		return $modelManager->getrdfStatements($Ressource,$query_on_model);
	}
 function getPrivileges($session,$uri,$query_on_model,$modelManager=false)
	{	
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
		}
		return $modelManager->getPrivileges($uri,$query_on_model);
	}
 function getMethods($session,$tripleId,$query_on_model,$modelManager=false)
	{	
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
		}
		return $modelManager->getMethods($tripleId,$query_on_model);
	}

function check_SetStatement($session,$subject, $predicate, $object,$query_on_model,$modelManager=false)
	{	
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
		}
		return $modelManager->check_SetStatement($subject, $predicate, $object,$query_on_model);
	}

/**
set privileges for a specific resource (string:$uri) about the access to the method $method
**/
function setPrivileges($session,$uri,$method,$privs,$user,$query_on_model,$modelManager=false)
	{	
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
		}
		return $modelManager->setPrivileges($uri,$method,$privs,$user,$query_on_model) ;
	}


/*
* updates privileges set on statement, id of statment is given,(string:$statment). Privileges modifications is granted if the connected user may delete the statement.
*/
 function setPrivilegesonStatement($session,$statement,$privilege,$modelManager=false)
	{	
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
		}
		return $modelManager->setPrivilegesonStatement($statement,$privilege);
	}
/**
* add a new pattern to the mask of the connected user, with the scope $scope. Mask may be modified using the edituser (...$mask...)
* service.
**/
function addPattern($session, $scope,$method="-")
	{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
		}
		
		return $modelManager->addPattern($scope,$method);
	}


 function getLabelComment($session,$Ressource,$query_on_model,$modelManager=false)
	{	
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
		}
		return $modelManager->getLabelComment($Ressource,$query_on_model);
	}	
/**
* Returns true if model where is described $uri has already been loaded in the model $query_on_model
*/
 function isKnownModel($session,$uri,$query_on_model)
	{	
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
		}
		return $modelManager->isKnownModel($uri,$query_on_model);
	}	
	
function execSQL($session,$WHERE_CLAUSE,$query_on_model,$modelManager=false)
{	
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
		}
		return $modelManager->execSQL($WHERE_CLAUSE,$query_on_model);
	}	


function setSequence($session,$sequence)
	{	
		
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
			$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
		}
		
		
		return $modelManager->setSequence($sequence);
	}	


/**
* deprecated
* use instead getRessourceDescription
*Returns Description of a class (label comment, subclassof, properties, etc.)
*@param TAO:session $pSession returned by authenticate service
*@access public
*@param Array([0] => String) $idcass : array(14) (with or without #i(deprecated)) id of class
*@param Array([0] => String) $query_on_model : is optional. It defines the model, set of data to use.  This namespace is local or got using service "getrdffromaremotemodule" 
*@return Array()
**/

function getClassDescription($session,$idclass,$query_on_model,$modelManager=false)
	{	
		
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
			$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
		}
		
		
		return $modelManager->getClassDescription($idclass,$query_on_model);
	}	

/**
* deprecated
* use instead getRessourceDescription
*Returns Description of a property (label comment, domain, widget,range)
*@param TAO:session $pSession returned by authenticate service
*@access public
*@param Array([0] => String) $idproperty : array(14) (without #p) id of property
*@param Array([0] => String) $query_on_model : is optional. It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule" 
*@return Array()

**/
function getPropertyDescription($session,$idproperty,$query_on_model,$modelManager=false)
	{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->getPropertyDescription($idproperty,$query_on_model);
	}
	

/**
*Returns direct subclasses of $idclass
*@param TAO:session $pSession returned by authenticate service
*@access public
*@param Array([0] => String) $idclass : array(14) (without #c) id of class
*@param Array([0] => String) $query_on_model : is optional. It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule" 
*@return Array()

**/

function getsubClasses($session,$idclass,$query_on_model,$modelManager=false)
	{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		
		return $modelManager->getsubClasses($idclass,$query_on_model);
	}

/**
*Returns direct properties of $idclass
*@param TAO:session $pSession returned by authenticate service
*@access public
*@param Array([0] => String) $idclass : array(14) (without #c) id of class
*@param Array([0] => String) $query_on_model : is optional. It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule" 
*@return Array()

**/

function getProperties($session,$idclass,$query_on_model,$modelManager=false)
	
	
	{
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->getProperties($idclass,$query_on_model);
	}
/**
*
**/

function getsubProperties($session,$idproperty,$query_on_model,$modelManager=false)
	{
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->getsubProperties($idproperty,$query_on_model);
	}
/**
*Returns direct and indirect properties of $idclass
*@param TAO:session $pSession returned by authenticate service
*@access public
*@param Array([0] => String) $idclass : array(14) (without #c) id of class
*@param Array([0] => String) $query_on_model : is optional. It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule" 
*@return Array()


**/
function getAllProperties($session,$idclass,$query_on_model,$modelManager=false)
	{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		$modelManager->getAllProperties($idclass,$query_on_model);

	}
/**
*Returns all subclasses of $idclass
*
*@param TAO:session $pSession returned by authenticate service
*@access public
*@param Array([0] => String) $idclass : array(14) (without #c) id of class
*@param Array([0] => String) $query_on_model : is optional. It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule" 
@return Array()

**/
function getAllClasses($session,$idclass,$query_on_model,$modelManager=false)
	{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->getAllClasses($idclass,$query_on_model);
	}



function cloneit($session,$uri)

	{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->cloneit($uri);
	}

/**
* returns all instances of metaclasses that are not subclass of another thing ("root" classes)
*/
function getTopClasses($session,$query_on_model) 
{
if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = 					unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->getTopClasses($query_on_model);
		
}


function getTopMetaClasses($session,$query_on_model) 
{
if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = 					unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->getTopMetaClasses($query_on_model);
		
}

/**
* Performs a search on local knowledge or remote if $query_on_model specified
*$exact == (exact match value) else (substring) : TRUE|FALSE
*@param TAO:session $pSession returned by authenticate service
*@access public
*@param Array([0] => array(criteria1,value1, etc.) )$pCriteria : array with alternatively predicates and values required  *array(criteria1,value1 related to criteria 1,criteria2 , value2, etc....)
criteria are #p15 or #label #range, etc.
*@param Array([0] => String) $query_on_model : is optional. It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule" 
*@param Boolean $exact exact match or may be a substring of
*@return Array
*/
function search($session,$pCriteria,$query_on_model,$exact)
	
	
	{error_reporting(E_ALL);
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->search($pCriteria,$query_on_model,$exact);
	}

function searchInstances($session,$pCriteria,$query_on_model,$exact)
	
	
	{error_reporting(E_ALL);
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->searchInstances($pCriteria,$query_on_model,$exact);
	}

	/*
	$class="";
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($pSession[0]);$modelManager = unserialize($x);
	$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
	$db = $modelManager->getDB();
	
	if (is_null($query_on_model[0]) OR $query_on_model[0]=="")
			{
		$temp = $modelManager->getRDF();
		$queries = $db->get_query_interface($temp[2]);
		$queries->namespace = $temp[1];
		//print_r($temp);
			}
		else {
		$temp2 = $modelManager->getremoteRDF();
		$temp=$temp2[$query_on_model[0]];
		$queries = $db->get_query_interface($temp[2]);
		$queries->namespace = $temp[1];
			 }
		
		$query_properties = array();
		$i=0;
		$lg = sizeof($pCriteria);
		//print_r($pcriteria);
		while ($i<=$lg-1)
			{
				$ns = $temp[1];
				if (($pCriteria[$i]=="#label") OR ($pCriteria[$i]=="#comment") OR ($pCriteria[$i]=="#range"))
					{$ns = "http://www.w3.org/TR/1999/PR-rdf-schema-19990303" ;}
				if (($pCriteria[$i]=="#widget"))
					{$ns = "http://www.tao.lu/WidgetDefinitions.rdf" ;}
				
				
				if (($pCriteria[$i]=="#type"))
					{$ns = "http://www.w3.org/1999/02/22-rdf-syntax-ns" ;
					$class = $pCriteria[$i+1];
					}
				else
				{
				array_push($query_properties,
				new RdfProperty(null, $ns.$pCriteria[$i], $pCriteria[$i+1], 1));
				}
				$i = $i+2;
			}
		
		if ($exact) {$queries->exact=true;} 
		//if (sizeof($query_properties)>1)//Just criteria on type has been specified
		//{		
		$result_by_properties = $queries->SearchrecursiveByProperties($query_properties,$class);
		//$result_by_properties = $queries->SearchByProperties($query_properties);
		//}
		//else
		//{}
		return array("pResult" => array_unique($result_by_properties));
		unset($queries->exact);
		*/
	



/**
* returns a javascript array structure describing all resources or a subGraph structure described in xml if $asGraphXML is set to true,  related to $ns set of knowledge specified (may be remote knowledge)
*@param TAO:session $pSession returned by authenticate service
*@access public
*@param Array([0] => String) $v_key the class returned classes are subClassOf 
*@param Array([0] => String) $pType deprecated
*@param String $ns is optional (if none use empty sting). It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule" 
*@param Boolean $asGraphXML if true returns a javascript structure else a graph structure in graphml
*@param Array([0] => String) $pClassId resource root selected
*@return string
*/

function getHTMLTree($pSession,$pType,$ns,$asGraphXML=false,$pClassId = "")    {

	include_once("UItreePlain.php");
	
	return getTree($pSession,$pType,$ns,$asGraphXML,$pClassId); 
}

/**
**************************************************************************************
*
*				PART 2 :
*		Business
*		Class, Property and Instance Management, affect rights to objects, check permissions when set or edit
*
*****************************************************************************************/
 
 /**
  * Creates a Class and insert it into the knowledge base
  *
  *@param Array $Plg All languages in which lebels and comments are given 
  * @param Array $labels Labels Each labels in each $pLg
  * @param Array $comments Comments Each Comments in each $pLg
  * @param Array $domain Domaine
   * @return Array with informations on success or error state
  *@param TAO:session $pSession returned by authenticate service
  *@access public
  */

  function setClass($pSession,$pLg, $pLabels,$pComments,$pDomain)
	{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($pSession[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return 
			array("pOKorKO" => $modelManager->setClass($pLg, $pLabels,$pComments,$pDomain));
	}
/**
* Removes a Class only if user is allowed or admin
* Removes all informations linked to this class, access rights
* instances being a typeOf this class become instances of $idclass parentClass 
* changed)
* @param Array([0] => String) $idclass TAO short Id of class to remove (without #c)
* @return Array with informations on success or error state
*@param TAO:session $session returned by authenticate service
*@access public
  */
function removeClass($session, $idclass)
	{
		
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		

		/*Removes subClassOf Values of all sub Classes of the removed Class*/
		$sclasses = getsubClasses($session,$idclass,array(""));
		$sousclasses = $sclasses["pDescription"];
		
		foreach ($sousclasses as $asubclass)
				{	
					$parentClasses = getParentClasses($session, $asubclass["ClassKey"]);
					$newparentclasses = array();
					$newparentclasses = array_diff($parentClasses,array("#c".$idclass[0]));
					
					$class=substr($asubclass["ClassKey"],2);
					
					
					
					$newparentclasses = array_map("sub",$newparentclasses);
					if (sizeOf($newparentclasses)!=0)
					{$modelManager->editClass($class,array(),array(),array(),$newparentclasses);}
					else {$modelManager->editClass($class,array(),array(),array(),array(""));}
					
				}
		
		/*Removes domain Values of all properties describing this Class */
		
		$properties = getProperties($session,$idclass,array(""));
		$directproperties = $properties["pDescription"];
		foreach ($directproperties as $property)
				{	
					$propertydomain = getDomain($session, $property["PropertyKey"]);
					$newpropertydomain = array();
					$newpropertydomain = array_diff($propertydomain,array("#c".$idclass[0]));
					
					
					$aproperty=substr($property["PropertyKey"],2);
					
					$newpropertydomain = array_map("sub",$newpropertydomain);
					if (sizeOf($newpropertydomain)!=0)
					{$modelManager->editProperty($aproperty,array(),array(),array(),$newpropertydomain,"","");}
					else {$modelManager->editProperty($aproperty,array(),array(),array(),array(""),"","");}
					
				}


		/*Remove Class*/
		return array("pOKorKO" =>  $modelManager->removeClass($idclass[0]));
	}

/**
* return parent classes of $classkey, $classkey is a subClassOf
* @param Array([0] => String) $classkey TAO short Id of class (without #c)
* @return Array 
*@param TAO:session $session returned by authenticate service
*@access public
*/

function getParentClasses($session, $classkey)

	{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		$db = $modelManager->getDB();
		$temp = $modelManager->getRDF();
		$queries->namespace = $temp[1];
	    $queries = $db->get_query_interface($temp[0]);
		$queries->namespace = $temp[1];
		
		$scof = $queries->GetObjects($classkey, "rdfs:subClassOf");
				
		return $scof;
		

	}
/**
* return domain of a property $classkey, $classes that are described by this property
* @param Array([0] => String) $propertykey TAO short Id of property (without #p)
* @return Array
*@param TAO:session $session returned by authenticate service
*@access public
*/
function getDomain($session, $propertykey)

	{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		$db = $modelManager->getDB();
		$temp = $modelManager->getRDF();
		$queries->namespace = $temp[1];
	    $queries = $db->get_query_interface($temp[0]);
		$queries->namespace = $temp[1];
		
		$scof = $queries->GetObjects($propertykey, "rdfs:domain");
				
		return $scof;
		

	}

/**
  * Edit a Class only if user is allowed
  * Author and rights aren't modified with editor informations
  * labels and comments are vectors which contains edited values, lg specify used languages,
	$subClassOf contains ALL parent Classes
  * @param Array([0] => String) $idclass
  * @param Array $labels Labels
  * @param Array $comments Comments
  * @param Array $subClassOf parent Classes 
   *@param Array $lg All languages in which lebels and comments are given 
  * @return Array with informations on success or error state
  *@param TAO:session $pSession returned by authenticate service
*@access public
  */
function editClass($session,$idclass,$lg,$labels,$comments,$subClassOf)
	{
		
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		
		return 
			array("pOKorKO" =>  $modelManager->editClass($idclass[0],$lg,$labels,$comments,$subClassOf));

	}

function removeSubjectPredicate($session,$subject,$predicate)
	{
		
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		
		return 
			array("pOKorKO" =>  $modelManager->removeSubjectPredicate($subject,$predicate));

	}
function removeStatement($session,$statement)
	{
		
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		
		return 
			array("pOKorKO" =>  $modelManager->removeStatement($statement));

	}
function removeSubjectPredicateValue($session,$subject,$predicate,$value)
	{
		
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		
		return 
			array("pOKorKO" =>  $modelManager->removeSubjectPredicateValue($subject,$predicate,$value));

	}
function removeSubject($session,$subject)
	{
		
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		
		return 
			array("pOKorKO" =>  $modelManager->removeSubject($subject));

	}
function setStatement($session,$subject, $predicate, $object, $object_is,$lg, $l_datatype, $subject_is,$mask="")
		{
		
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		
		return 
			$modelManager->setStatement($subject, $predicate, $object, $object_is,$lg, $l_datatype, $subject_is,$mask);

		}

function editStatement($session,$tripleid, $object, $object_is,$lg, $l_datatype, $subject_is)
		{
		
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		
		return 
			$modelManager->editStatement($tripleid, $object, $object_is,$lg, $l_datatype, $subject_is);

		}
 /**
  * Local Rights managements for a Class : returns mask applied to a class
  *getLocalRightsClass
  *@param TAO:session $pSession returned by authenticate service
  * @param Array([0] => String) $idclass short TAO id of class without #c
  * @return Array with informations on success or error state
  * @access public
  */
function getLocalRightsClass($session,$idclass)
	{
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return 
			array("pDescription" =>  $modelManager->getLocalRightsClass($idclass[0]));
	
	}

 /**
  * Local Rights managements for a Class only if user is allowed : edit mask applied to a class
  * editLocalRightsClass
  * @param Array([0] => String) $idclass id of class to update without #c
  * @param Array([0] => String) $newmask new mask to apply
  * @return Array with informations on success or error state
  *@param TAO:session $pSession returned by authenticate service
 *@access public
  */

 function editMaskofClass($session,$idclass,$newmask)
	{
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		
		return  
			array("pOKorKO" => $modelManager->editMaskofClass($idclass[0],$newmask[0]));
		
	
	}  


/**
  * return rights for subscribers groups to a Class  (O: NONE, 1 : OVERVIEW, 2 :READ)
  *	Get Groups Rights for a Class
  * @param Array([0] => String) $pClassid id of Class
  * @param Array([0] => String) $pGroupId id of group of subscribers
    * @return Array
	*@param TAO:session $pSession returned by authenticate service
	*@access public
  */

function getGroupsRightsClass($pSession,$pClassid,$pGroupId)
	{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($pSession[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pDescription" => $modelManager->getGroupsRightsClass($pClassid[0],$pGroupId[0]));
	
	}

/**
  * Edit rights allowed to a group for a Class, only if allowed
  *	Get Groups Rights for a Class
  * @param Array([0] => String) $pClassid id of Class
  * @param Array([0] => String) $pGroupId id of group
  * @param Array([0] => String) $value (O: NONE, 1 : OVERVIEW, 2 :READ)
  *@param TAO:session $pSession returned by authenticate service
  *@access public
  * @return Array with informations on success or error state
  */

function editGroupsRightsClass($pSession,$pClassid,$pGroupId,$value)
	{
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($pSession[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->editGroupsRightsClass($pClassid[0],$pGroupId[0],$value[0]));
	
	}


 /**
  * Creates a Property and insert it into the knowledge base
  *
  * For creation of properties like "Combobox, radio, etc." with non unique range:
  * First create a Class : see creation of a class
  * Create as much as instances as values for this property
  * Create the property with 
  * RANGE : Previously created class
  * WIDGET : "http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextArea", etc.
  * @param Array $labels Labels
  * @param Array $comments Comments
  * @param Array $domain Domaine (Classes described by this property)
  * @param Array $range for instance : "http://www.w3.org/TR/1999/PR-rdf-schema-19990303#literal" in case of a literal , "http://URL/trucs.rdf#c1"
  * @param Array $widget for instance : "http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextArea", etc.
  *@param Array $Plg All languages in which lebels and comments are given 
  * @return Array with informations on success or error state
  *@param TAO:session $session returned by authenticate service
 *@access public
  */


function setProperty($session,$lg, $labels,$comments,$domain,$range, $widget)
	{
		if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->setProperty($lg, $labels,$comments,$domain,$range[0], $widget[0]));
	
	}
/**
* Removes a property only if user is allowed or admin
* Removes all informations linked to this property, access rights
* instances having values for this properties are updated 
* @param Array([0] => String) $idproperty TAO short Id of property to remove (without #p)
* @return Array with informations on success or error state
*@param TAO:session $session returned by authenticate service
*@access public
 */
function removeProperty($session,$idproperty)
	{
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->removeProperty($idproperty[0]));
	
	}
  /**
  * Edit a Property only if user is allowed
  * Author and rights aren't modified with editor informations
  * labels and comments are vectors which contains edited values, lg specify used languages,
	$subClassOf contains ALL parent Classes
  * @param Array([0] => String) idproperty
  * @param Array $labels Labels
  * @param Array $comments Comments
  * @param Array $domain Classes described by this property
   *@param Array $lg All languages in which lebels and comments are given 
  * @param Array $range for instance : "http://www.w3.org/TR/1999/PR-rdf-schema-19990303#literal" in case of a literal , "http://URL/trucs.rdf#c1"
  * @param Array $widget for instance : "http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextArea", etc.
  * @return Array with informations on success or error state
  *@param TAO:session $pSession returned by authenticate service
  *@access public
  */

function editProperty($session,$idproperty, $lg, $labels,$comments,$domain,$range,$widget)
	{
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}

		
		return array("pOKorKO" => $modelManager->editProperty($idproperty[0], $lg, $labels,$comments,$domain,$range[0],$widget[0]));
	}

/**
  * Local Rights managements for a property  : returns mask applied to the property
  *getLocalRightsClass
  *@param TAO:session $session returned by authenticate service
  * @param Array([0] => String) $idproperty short TAO id of property without #p
  * @return Array with informations on success or error state
  * @access public
  */

function getLocalRightsProperty($session,$idproperty)
	{
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pDescription" => $modelManager->getLocalRightsProperty($idproperty[0]));
	
	}
/**
  * Local Rights managements for a Property only if user is allowed : edit mask applied to a property
  * editLocalRightsClass
  * @param Array([0] => String) $idproperty id of class to update without #p
  * @param Array([0] => String) $newmask new mask to apply
  * @return Array with informations on success or error state
  *@param TAO:session $pSession returned by authenticate service
 *@access public
  */
function editLocalRightsProperty($session,$idproperty,$localrights)
	{
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		
		return array("pOKorKO" => $modelManager->editLocalRightsProperty($idproperty[0],$localrights[0]));
	}

/**
  * Remote Rights managements for a property : return  Groups Rights for a property (0:None, 1:Overview,2,READ)
  * @param Array([0] => String) $idproperty id of property
  * @param Array([0] => String) $idgroup id of group of subscribers
  *@param TAO:session $session returned by authenticate service
 *@access public
  */


function getGroupsRightsProperty($session,$idproperty,$idgroup)
	{
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pDescription" => $modelManager->getGroupsRightsProperty($idproperty[0],$idgroup[0]));
	}
/**
  * Edit rights allowed to a group for a Property, only if allowed
  *	Get Groups Rights for a Property
  * @param Array([0] => String) $idproperty id of property
  * @param Array([0] => String) $pGroupId id of group
  * @param Array([0] => String) $value (O: NONE, 1 : OVERVIEW, 2 :READ)
  *@param TAO:session $pSession returned by authenticate service
  *@access public
  * @return Array with informations on success or error state
  */
function editGroupsRightsProperty($session,$idproperty,$idgroup,$value)
	{
	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->editGroupsRightsProperty($idproperty[0],$idgroup[0],$value[0]));
	}




/**
  * Creates an instance and insert it into the knowledge base
  *
  *@param Array $lg All languages in which labels and comments are given 
  * @param Array $labels Labels Each labels in each $pLg
  * @param Array $comments Comments Each Comments in each $pLg
  * @param Array $type type of instances, this is an array of classes
    * @return Array with informations on success or error state
  *@param TAO:session $session returned by authenticate service
  *@access public
  */
function setInstance($session,$lg, $labels,$comments,$type)
	{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->setInstance($lg, $labels,$comments,$type[0]));

	}


/**
  * Add values for an instance related to a property but independant of a language (different of setpropertyvaluesforinstance), Th functions overload values if called several times 
  *
   * @param Array([0] => String)  $idinstance the instance tom update
  * @param Array([0] => String) $idproperty id of property  
  * @param Array $memberlist array of values to add
   * @return Array with informations on success or error state
  *@param TAO:session $session returned by authenticate service
 *@access public
  */
function affiliate($session,$idinstance,$idproperty, $memberlist)
	{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->affiliate($idinstance[0],$idproperty[0], $memberlist));}
/**
  * Removes specified values for an instance related to a property 
  *
   * @param Array([0] => String)  $idinstance the instance to update
  * @param Array([0] => String) $idproperty id of property  
  * @param Array $memberlist array of values to remove
   * @return Array with informations on success or error state
  *@param TAO:session $session returned by authenticate service
 *@access public
  */
function unaffiliate($session,$idinstance,$idproperty, $memberlist)
	{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->unaffiliate($idinstance[0],$idproperty[0], $memberlist));}
/**
  * creates values for an instance related to a property, values are dependant of a language (otherwise use affiliate),  
  *
   * @param Array([0] => String)  $idinstance the instance to update
  * @param Array([0] => String) $Idproperty id of property  
 * @param Array $lg array of languages realted to values (exactly the same order as values)
  * @param Array $memberlist array of values to add
   * @return Array with informations on success or error state
  *@param TAO:session $session returned by authenticate service
 *@access public
  */

function setPropertyValuesforInstance($session,$idinstance,$Idproperty, $lg,$values)
	{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => "$Idproperty[0]".$modelManager->setPropertyValuesforInstance($idinstance[0],$Idproperty[0], $lg,$values));}


/**
  * edit values for an instance related to a property, values are dependant of a language (otherwise use affiliate). If, for a specified language, a value is already found, the value will be update, otherwise the valeu is created.
  *
   * @param Array([0] => String)  $idinstance the instance to update
  * @param Array([0] => String) $idProperty id of property  
 * @param Array $lg array of languages realted to values (exactly the same order as values)
  * @param Array $values array of values 
  * @return Array with informations on success or error state
  *@param TAO:session $session returned by authenticate service
 *@access public
  */

function editPropertyValuesforInstance($session,$idInstance,$idProperty,$lg,$values)
	{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->editPropertyValuesforInstance($idInstance[0],$idProperty[0],$lg,$values));}
/**
  * removes all values for an instance related to a property
  *
   * @param Array([0] => String)  $idinstance the instance to update
  * @param Array([0] => String) $Idproperty id of property  
 
 * @return Array with informations on success or error state
  *@param TAO:session $session returned by authenticate service
 *@access public
  */
function RemovePropertyValuesforInstance($session,$idinstance,$Idproperty)
	{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->RemovePropertyValuesforInstance($idinstance[0],$Idproperty[0]));
		
		}


/**
  * removes an instance
  *
  * This function removes the instance and all values defined for each properties
   * @param Array([0] => String)  $idInstance the instance to remove
   * @return Array with informations on success or error state
  *@param TAO:session $session returned by authenticate service
 *@access public
  */
function removeInstance($session,$idInstance)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->removeInstance($idInstance[0]));}

/**
  * edit labels, comments and type of an instance
  *
  * This function edit the instance with new labels, comments and type specified
   * @param Array([0] => String)  $idInstance the instance to update
   * @param Array  $labels new labels of instance
   * @param Array  $comments new comments of instance
   * @param Array  $lg list of languages used for labels and comments
     * @param Array([0] => String)  $type the type of instance
   * @return Array with informations on success or error state
  *@param TAO:session $session returned by authenticate service
 *@access public
  */
function editInstance($session,$idInstance,$lg, $labels,$comments,$type)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->editInstance($idInstance[0],$lg, $labels,$comments,$type[0]));}
/**
  * Local Rights managements for an instance : returns mask applied to the instance
  *getLocalRightsInstance
  *@param TAO:session $session returned by authenticate service
  * @param Array([0] => String) $idInstance short TAO id of instance without #i
  * @return Array with informations on success or error state
  * @access public
  */
function getLocalRightsInstance($session,$idInstance)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		
		return array("pDescription" => $modelManager->getLocalRightsInstance($idInstance[0]));
}


/**
  * Local Rights managements for an instance only if user is allowed
  * editLocalRightsInstance
  * @param Array([0] => String) $idInstance id of an instance
  * @param Array([0] => String) $localrights mask
 * @return Array with informations on success or error state
   *@param TAO:session $session returned by authenticate service
 *@access public
  */
function editLocalRightsInstance($session,$idInstance,$localrights)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->editLocalRightsInstance($idInstance[0],$localrights[0]));}
/**
  * Remote Rights managements for an instance
  *	Get Groups Rights for an instance
  * @param Array([0] => String) $idinstance id of instance
  * @param Array([0] => String) $id id of group of subscribers
  * @return Array([0] => String) (0:None, 1:Overview,2,READ)
   *@param TAO:session $session returned by authenticate service
 *@access public
  */
function getGroupsRightsInstance($session,$idinstance,$idgroup)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->getGroupsRightsInstance($idinstance[0],$idgroup[0]);}
/**
  * Remote Rights managements for an instance only if allowed
  *	Get Groups Rights for a property
  * @param Array([0] => String) $idinstance id of instance
  * @param Array([0] => String) $idgroup id of group
  * @param Array([0] => String) $value value
  * @return Array with informations on success or error state
   *@param TAO:session $session returned by authenticate service
 *@access public
  */


function editGroupsRightsInstance($session,$idinstance,$idgroup,$value)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->editGroupsRightsInstance($idinstance[0],$idgroup[0],$value[0]));}
		



/**
*Add a subscribee in database (url, login, password, its type,name of the module)
*@param Array([0] => String) $url url of module
*@param Array([0] => String) $login login of module
*@param Array([0] => String) $password password of module
*@param Array([0] => String) $type type of module
*@param Array([0] => String) $md module name 
 *@param TAO:session $session returned by authenticate service
* @return Array with informations on success or error state
*/


function addSubscribee($session,$url,$login,$password,$type,$md)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->addSubscribee($url[0],$login[0],$password[0],$type[0],$md[0]));}
/**
*Edit the subscribee $idsubscribee in database (url, login, password, its type,name of the module)
*@param Array([0] => String) $idsubscribee Id of subscribee
*@param Array([0] => String) $url url of module
*@param Array([0] => String) $login login of module
*@param Array([0] => String) $password password of module
*@param Array([0] => String) $type type of module
*@param Array([0] => String) $md module name 
 *@param TAO:session $session returned by authenticate service
* @return Array with informations on success or error state
*/
		
function editSubscribee($session,$idsubscribee,$url,$login,$password,$type,$md)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		
		return array("pOKorKO" => $modelManager->editSubscribee($idsubscribee[0],$url[0],$login[0],$password[0],$type[0],$md[0]));}
/**
*Removes the subscribee $idsubscribee 
*@param Array([0] => String) $idsubscribee Id of subscribee to remove, only admin iss allowed to remove subscribees
 *@param TAO:session $session returned by authenticate service
* @return Array with informations on success or error state
*/
		
function removeSubscribee($session,$idsubscribee)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->removeSubscribee($idsubscribee[0]));}
		
/**
*Returns all informations about subscribees in knowledge base (Idsub, login, password,url, type) only administrators are allowed to call this service
 *@param TAO:session $session returned by authenticate service
* @return Array with informations on success or error state
*/
function getSubscribee($session)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pDescription" => $modelManager->getSubscribee());}
/**
*Returns all informations about subscribees in knowledge base (Idsub, login, password,url, type) but as javascript array
 *@param TAO:session $session returned by authenticate service
* @return Array with informations on success or error state
*/
function getSubscribeeaslist($session)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pDescription" => $modelManager->getSubscribeeaslist());}
/**
*Returns all informations about subscribees in knowledge base (Idsub, login, url, type) but as user (the password is not provided)
 *@param TAO:session $session returned by authenticate service
* @return Array with informations on success or error state
*/
function getSubscribeeasUser($session)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);
$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pDescription" => $modelManager->getSubscribeeasUser());}
/**
* Returns all informations about subscribees in knowledge base (Idsub, login, password,url, type) whose url is like $url, only administrators are allowed to call this service. 
 *@param TAO:session $session returned by authenticate service
 *@access public
* @param Array([0] => String) $type = Type of module, for instance in the e-teting specialisation : (Subject | Group | Test | Item | Result)
* @param Array([0] => String) $url identifer of a TAO resource
* @return array
* Array ( 
	[0] => 8 [1] => LOG [2] => 7a95bf926a0333f57705aeac07a362a2 [3] => http://158.64.44.236/middleware/businessModuleSubscriber.php [4] => moduledetest 
		)
*/
function getSubscribeesurl($session,$type,$url)
			{
				
				if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);
$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		//error_reporting(0);
		
		return $modelManager->getSubscribeesurl($type[0],$url[0]);
			}

/**
* Add a user of the module, only for administrators
 *@param TAO:session $session returned by authenticate service
*@param Array([0] => String) $login
*@param Array([0] => String) $password
*@param Array([0] => String) $umask
*@param Array([0] => String) $admin
*@param Array([0] => String) $lastname
*@param Array([0] => String) $firstname
*@param Array([0] => String) $email
*@param Array([0] => String) $company
*@param Array([0] => String) $deflg
*@param Array([0] => String) $enabled
*@param Array([0] => String) $usergroup
*/

function addUser($session,$login,$password,$umask,$admin,$lastname,$firstname,$email,$company,$deflg,$enabled,
$usergroup)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" =>  $modelManager->addUser($login[0],$password[0],$umask[0],$admin[0],$lastname[0],$firstname[0],$email[0],$company[0],$deflg[0],$enabled[0],$usergroup[0]));}

	/**
* Edit a user of the module, only for administrators
 *@param TAO:session $session returned by authenticate service
*@param Array([0] => String) $login
*@param Array([0] => String) $password
*@param Array([0] => String) $umask
*@param Array([0] => String) $admin
*@param Array([0] => String) $lastname
*@param Array([0] => String) $firstname
*@param Array([0] => String) $email
*@param Array([0] => String) $company
*@param Array([0] => String) $deflg
*@param Array([0] => String) $enabled
*@param Array([0] => String) $usergroup
*/
function editUser($session,$login,$password,$umask,$admin,$lastname,$firstname,$email,$company,$deflg,$enabled,$usergroup)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		
		return array("pOKorKO" =>  $modelManager->editUser($login[0],$password[0],$umask[0],$admin[0],$lastname[0],$firstname[0],$email[0],$company[0],$deflg[0],$enabled[0],$usergroup[0]));}
/**
* Removes a user of the module, only for administrators
 *@param TAO:session $session returned by authenticate service
*@param Array([0] => String) $login
*/		
function removeUser($session,$login)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->removeUser($login[0]));}
/**
* Returns all groups of users descibed in the local tao module only for administrators
 *@param TAO:session $session returned by authenticate service
 
* @return array of all groups of users

*/
function getgroups($session)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pDescription" => $modelManager->getgroups());}
/**
* Returns javascript array structure describing all groups of users and related members only for administrators
 *@param TAO:session $session returned by authenticate service

* @return string javascript array structure

*/	
function getgroupsmembers($session)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pDescription" => $modelManager->getgroupsmembers());}

/**
* Returns description of user $user (login, password, email, company, etc.) only for administrator or the user $user
 *@param TAO:session $session returned by authenticate service
  *@param Array([0] => String) $user 
* @return Array with informations about user

*/	

function getUserdescription($session,$user)	
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pDescription" => $modelManager->getUserdescription($user[0]));}
	
/**
* Returns true iff $login is a valid user login only for administrator
 *@param TAO:session $session returned by authenticate service
  *@param Array([0] => String) $login
* @return Array with informations on success or error state

*/		
function checkIfLoginalreadyexists($session,$login)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->checkIfLoginalreadyexists($login[0]));}
/**
* Affiliate the user $login with the group $idgroup only for administrator
 *@param TAO:session $session returned by authenticate service
 
* @return Array with informations on success or error state

*/			
function affiliateUserGroup($session,$login,$idGroup)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->affiliateUserGroup($login[0],$idGroup[0]));}
/**
* Add a group of user only for administrator
*@param TAO:session $session returned by authenticate service
 *@param Array([0] => String) $name new name of group
 
* @return Array with informations on success or error state

*/			
function addGroup($session,$name)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->addGroup($name[0]));}
/**
* Change name of a group only for administrator
*@param TAO:session $session returned by authenticate service
 *@param Array([0] => String) $name old name
  *@param Array([0] => String) $newname new one
 
* @return Array with informations on success or error state

*/			
function editGroup($session,$name,$newname)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->editGroup($name[0],$newname[0]));}
/**
* Remove a group of user only for administrator
 *@param TAO:session $session returned by authenticate service
 *@param Array([0] => String) $name name of group to remove
  
* @return Array with informations on success or error state

*/	
		
function removeGroup($session,$name)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->removeGroup($name[0]));}

		
/**
* Add a subscriber in local module only for administrator
 *@param TAO:session $session returned by authenticate service
 *@param Array([0] => String) $login login of subscriber
  *@param Array([0] => String) $password password of subscriber
   *@param Array([0] => Boolean) $enabled Is enabled or not
 * @return Array with informations on success or error state

*/	
function addSubscriber($session,$login,$password,$enabled)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->addSubscriber($login[0],$password[0],$enabled[0]));}
/**
* Edit a subscriber in local module only for administrator
 *@param TAO:session $session returned by authenticate service
 *@param Array([0] => String) $idsubscriber Id of subscriber
 *@param Array([0] => String) $login login of subscriber
  *@param Array([0] => String) $password password of subscriber
   *@param Array([0] => Boolean) $enabled Is enabled or not
 * @return Array with informations on success or error state

*/			
function editSubscriber($session,$idsubscriber,$login,$password,$enabled)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->editSubscriber($idsubscriber[0],$login[0],$password[0],$enabled[0]));}
	
/**
* Returns subscriber description (login, password, etc.) only for administrator
*@param TAO:session $session returned by authenticate service
*@param Array([0] => String) $idsubscriber Id of subscriber
* @return Array with informations on success or error state
*/			
function getSubscriberDescription($session,$idsubscriber)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->getSubscriberDescription($idsubscriber[0]));}
	
		
/**
* Removes subscriber description only for administrator
*@param TAO:session $session returned by authenticate service
*@param Array([0] => String) $idsubscriber Id of subscriber
* @return Array with informations on success or error state
*/	
		
function removeSubscriber($session,$idsubscriber)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->removeSubscriber($idsubscriber[0]));}
		

/**
* Affiliate a subscriber to a group of subscribers
*@param TAO:session $session returned by authenticate service
*@param Array([0] => String) $idsubscriber Id of subscriber
*@param Array([0] => String) $idGroup Id of subscribers group
* @return Array with informations on success or error state
*/	
	
function affiliateSubscriberGroup($session,$idsubscriber,$idGroup)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->affiliateSubscriberGroup($idsubscriber[0],$idGroup[0]));}

/**
* Affiliate a group of subscriber to another one
*@param TAO:session $session returned by authenticate service
*@param Array([0] => String) $idgroupFather Id of adoptive group 
*@param Array([0] => String) $idGroupSon Id of adopted subscribers group
* @return Array with informations on success or error state
*/	
function affiliateGroupGroup($session,$idgroupFather,$idGroupSon)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->affiliateGroupGroup($idgroupFather[0],$idGroupSon[0]));}

/**
* Add a subscribers group
*@param TAO:session $session returned by authenticate service
*@param Array([0] => String) $name name of subscribers group
* @return Array with informations on success or error state
*/	
function addSubscriberGroup($session,$name)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->addSubscriberGroup($name[0]));}
/**
* Edit a subscribers group
*@param TAO:session $session returned by authenticate service
*@param Array([0] => String) $idGroup Id of subscribers group
*@param Array([0] => String) $name name of subscribers group
* @return Array with informations on success or error state
*/		
function editSubscriberGroup($session,$idGroup,$name)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->editSubscriberGroup($idGroup[0],$name[0]));}
/**
* Removes a subscribers group
*@param TAO:session $session returned by authenticate service
*@param Array([0] => String) $idGroup Id of subscribers group
* @return Array with informations on success or error state
*/			
function removeSubscriberGroup($session,$idGroup)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->removeSubscriberGroup($idGroup[0]));}
/**
*Returns string containing javascript array structure describing groups and members structure
 *@param TAO:session $session returned by authenticate service
 *@access public
*@param boolean $onlygroups : describes only groups of subscribers
*/		
function getGroupsSubscribersMembers($session,$onlygroups=false)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pDescription" => $modelManager->getGroupsSubscribersMembers($onlygroups));}

/**
*Returns string containing javascript array structure describing groups and their rights on $ressource
 *@param TAO:session $session returned by authenticate service
 *@access public
*@param String $ressource : rdf ID of TAO resource !"#c2"!
*/		
function getGroupsSubscribersRightsonResource($session,$ressource)
{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		
		return array("pDescription" => $modelManager->getGroupsSubscribersRightsonResource($ressource));}


		
/**
*Returns default language of User
 *@param TAO:session $session returned by authenticate service
* @return Array with informations on success or error state
*@access public
*/	
function getMyDeflg($session)
	{	if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->getMyDeflg());}
		
/**
* Edit default language of User
 *@param TAO:session $session returned by authenticate service
*@param Array([0] => String) $lg new language
*@access public
* @return Array with informations on success or error state
*/
function setMyDeflg($session,$lg)
	{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->setMyDeflg($lg[0]));}
		
/**

function getTimeout($session)
	{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pDescription" => $modelManager->getTimeout());}
		
function setTimeout($session,$timeout)
	{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pOKorKO" => $modelManager->setTimeout($timeout[0]));}
		
*/

/**
* Get default language of Module
 *@param TAO:session $session returned by authenticate service
 *@access public
* @return Array with informations on success or error state
*/
function getModuleDeflg($session)
	{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}

		
		return array("pDescription" => $modelManager->getModuleDeflg());}
		
/**
* Set default language of Module (not implemented yet)
 *@param TAO:session $session returned by authenticate service
*@param Array([0] => String) $lg new language
*@access public
* @return Array with informations on success or error state
**/
function setModuleDeflg($session)
	{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return array("pDescription" => $modelManager->setModuleDeflg());}
/**
* Returns namespace of local module
 *@param TAO:session $session returned by authenticate service
 *@access public
* @return Array with informations on success or error state
**/
function getNamespace($session)
	{if ((!(isset($modelManager))) or (!($modelManager))) {$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);}
		return $modelManager->getNamespace(); 
	}

/**
* Returns ($haystack is in  string $needle) senseitive or not

 *@access private
* @return Array with informations on success or error state
**/
function in_string($needle, $haystack, $insensitive = 0) {
   if ($insensitive) {
       return (false !== stristr($haystack, $needle)) ? true : false;
   } else {
       return (false !== strpos($haystack, $needle))  ? true : false;
   }
} 
/*
*Return the author of a resource 
*@param @resource short rdf id example #i4 or #c48, etc.
*@return array(login, crypted password, umask, admin, usergroup, lastname,firstname,E_mail,Company,Deflg,enabled,IDRDF,rdf,mask,author,enabled,protected)
 *@param TAO:session $session returned by authenticate service
 *@access public
*/
function getAuthor($session,$resource)
{$x = urldecode($session[0]);$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
		return $modelManager->getAuthor($resource);

}

 function unhtmlspecialchars( $string )
   {
      
      // $string = str_replace ( '&#039;', '\'', $string );
       $string = str_replace ( '&quot;', '\"', $string );
       $string = str_replace ( '&lt;', '<', $string );
       $string = str_replace ( '&gt;', '>', $string );
        $string = str_replace ( '&amp;', '&', $string );
       return $string;
   }

}

?>

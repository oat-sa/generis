<?php
/**
* 
* @package generis
* @author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 1.2
*/

class generisrdfmodel extends generisModel
{
  /**
  * Constructor
  */
  function rdfmodel()
  {
  }
  /*
  * Adds a statement in the knowledge base 
  * 
  */
  function setStatement($subject, $predicate, $object, $user,$mask="2222220012",$object_is="r",$lg="", $l_datatype="", $subject_is="" )
	{
	//Check if conencted user may add such statement (checks statment's semantics and match with privileges defined on rdfs resources)
	$isallowed =$this->check_SetStatement($subject, $predicate, $object);	
	
	if (!($isallowed[0]=="ok")) {
		return array("error","rights","<script>alert('This rdfs operation is not allowed');</script>",__FILE__.__LINE__);
	}

	
	//Namespace are made explicit...
	$subject =$this->longURI($subject);
	$predicate = $this->longURI($predicate);
	$object = $this->longURI($object);

	$object = mysql_real_escape_string($object);
	//Prevents erasing user defined uri. This kind of uri is not controlled in the scope of this module
	if (strpos($subject,"absoluteURI")===0)	{
		$subject=str_replace("absoluteURI","",$subject);
	}

	//The user may define specific privileges when creating statements, by default his mask will be used
	if (is_array($mask)) {	
		$rmask=$mask["read"];
		$emask=$mask["edit"];
		$dmask=$mask["delete"];
	}
	else
	{		
		$rmask=$this->modelManager->umask["read"];
		$emask=$this->modelManager->umask["edit"];
		$dmask=$this->modelManager->umask["delete"];
	}
		
	$query = "INSERT into statements VALUES  ('".$this->getUserModelId()."','".$subject."','".$this->longURI($predicate)."','".$this->longURI($object)."','".$lg."', '', '".$this->modelManager->user."','".$rmask."','".$emask."','".$dmask."' , CURRENT_TIMESTAMP   );";
//	$this->con->debug=true;
	$this->con->Execute($query);
//	$this->con->debug=false;
	return  array("ok","ok","ok");
	}

  /*
  * Check if the statement may be inserted in the knowledge base accordig to privilegs defied on resources "privilegs_classe"
  * returns boolean
  */

  function check_SetStatement($subject, $predicate, $object)
	{
		//while refactoring for tao_trsft , rigths are disabled (met dome problems for piaac 19/08/2008)
		return array("ok","ok","ok");
		$allowed = true;
		switch ($predicate)
		{
			case "http://www.w3.org/1999/02/22-rdf-syntax-ns#type" :
					{
						$allowed = ($allowed && ($this->filterMethod($object, "setInstance")));
						break;
					}
			
			case "http://www.w3.org/2000/01/rdf-schema#subClassOf":
				
				{
						//check that creation of class is allowed 
						$allowed = ($allowed && ($this->filterMethod("http://www.w3.org/2000/01/rdf-schema#Class", "setInstance")));
						$bool =$this->filterMethod($object, "setSubClass");
						 
						//check that specialization of this class in particualt is allowed
						$allowed = ($allowed && $bool);	
						
					}
			case "http://www.w3.org/2000/01/rdf-schema#domain":
				
				{
						//check that creation of class is allowed 
						$allowed = ($allowed && ($this->filterMethod("http://www.w3.org/1999/02/22-rdf-syntax-ns#Property", "setInstance")));
						$bool =$this->filterMethod($object, "setProperty");
						//check that specialization of this class in particualt is allowed
						$allowed = ($allowed && $bool);	
					}
			
		}
		if (!($allowed)) 
			{
				return array("error","rights","<script>alert('This rdfs operation is not allowed');</script>",__FILE__.__LINE__); 
			}

	return  array("ok","ok","ok");
	
	}
 /*
  * Edits a statement in the knowledge base 
  * @param integer tripleid 
  */
  function editStatement($tripleid,  $object, $object_is="r",$lg="", $l_datatype="", $subject_is="r" )
	{
		
	//Namespace are made explicit...
	$object = $this->longURI($object);
	$query = "select l_language,subject,predicate from statements where id='".$tripleid."'".$this->filter["edit"];
	$l_languageAdoDb = $this->con->Execute($query);
	while (!$l_languageAdoDb-> EOF)
           {
			if ($l_languageAdoDb->fields[0]== $lg)
			   {
			$query = "update statements set object ='".$this->longURI($object)."', l_language='".$lg." ' where id='".$tripleid."'".$this->filter["edit"];
			$this->con->Execute($query);
			return  array("ok","ok","ok");
			   }
			   else
			   {
				   $this->setStatement($l_languageAdoDb->fields[1], $l_languageAdoDb->fields[2], $object, $this->modelManager->user,"2222220012","r",$lg, "", "r" );
				   return  array("ok","ok","ok");
			   }
			
		   }
	
	trigger_error("The statement you tried to edit does not exist anymore ID:".$tripleid);
	

	
	}

  function getrdfStatements($uri)
	{
		$query="select * from statements where subject='".$this->URI($uri)."' and
		modelID in (".$this->modelID.")".$this->filter["read"];
//		$this->con->debug = true;
		$triplesadodb = $this->con->Execute($query);
		$triples=array();
		while (!$triplesadodb-> EOF)
           {
			$triples[$triplesadodb->fields['id']]=$triplesadodb->fields;
			$triplesadodb-> MoveNext();
		   }

		return $triples;
	}

function cloneit($uri)
	{
		$query="select * from statements where subject='".$this->URI($uri)."'";
		//$this->con->debug=true;
		$triplesadodb = $this->con->Execute($query);
		$triples=array();
			$localID = $this->microtime_float();
			$subject =  $this->modelURI."#i".$localID;
		
		while (!$triplesadodb-> EOF)
           {
			if (($triplesadodb->fields[2])=="http://www.w3.org/2000/01/rdf-schema#label")
			    {
					$object = $triplesadodb->fields[3]."(CLONE)";
				}
				else 
			   {
					$object = mysql_real_escape_string($triplesadodb->fields[3]);
			   }

			$query="insert into statements values('".$triplesadodb->fields['modelID']."','".$subject."','".$triplesadodb->fields['predicate']."','".$object."','".$triplesadodb->fields['l_language']."','".$triplesadodb->fields['id']."','".$triplesadodb->fields['author']."','".$triplesadodb->fields['stread']."','','".$triplesadodb->fields['stedit']."','".$triplesadodb->fields['stdelete']."' , CURRENT_TIMESTAMP );";
			
			$this->con->Execute($query);
			$triplesadodb-> MoveNext();
		   }
		   return $subject;
		 //$this->con->debug=false;
	}

 function setPrivilegesonStatement($statement,$privilege)
	{
	 // $this->con->debug=true;
		$query="update statements set stread='".$privilege[0]."' where id ='".$statement."' and
		modelID in (".$this->modelID.")".$this->filter["delete"];
		$this->con->Execute($query);

		$query="update statements set stedit='".$privilege[1]."' where id ='".$statement."' and
		modelID in (".$this->modelID.")".$this->filter["delete"];
		$this->con->Execute($query);

		$query="update statements set stdelete='".$privilege[2]."' where id ='".$statement."' and
		modelID in (".$this->modelID.")".$this->filter["delete"];
		$this->con->Execute($query);
		$this->con->debug=false;
	}

	function removeStatement($statement)
	{
	 	$query="delete from statements where id='".$statement."' and
		modelID='".$this->getUserModelId()."'".$this->filter["delete"];
		$this->con->Execute($query);
	}

  function removeSubject($subject)
	{
	 
		$query="delete from statements where subject='".$this->URI($subject)."' and
		modelID='".$this->getUserModelId()."'".$this->filter["delete"];
		$this->con->Execute($query);

	}

 function removeSubjectPredicate($subject,$predicate,$lgIndependant = false)
	{
		if ($lgIndependant) {
		$lgFilter = '';
		}
		else
		{
		$lgFilter = "and (l_language='".$this->modelManager->lg."' or l_language='')";
		}
		$query="delete from statements where subject='".$this->longURI($subject)."' and predicate='".$this->longURI($predicate)."'".$lgFilter."
		and modelID='".$this->getUserModelId()."'";
		//$this->con->debug=true;
		
		$this->con->Execute($query);
	}
 function removeSubjectPredicateValue($subject,$predicate,$value,$lg='')
	{
		$URIValue=$value;
		if (strpos($value,"#")===0)
		{
			$URIValue =$this->modelURI.$value;
		}
		else
		{	
			if (strpos($value,"http://")===0)
			{
			$URIValue=$value;
			}

		}
		$URISubject=$this->URI($subject);
		$URIPredicate=$this->URI($predicate);
		
		$query="delete from statements where subject='".$URISubject."' and predicate='".$URIPredicate."' and object='".$URIValue."'
		and (l_language='".$this->modelManager->lg."' or l_language = '".$lg."') and modelID='".$this->getUserModelId()."'".$this->filter["delete"];
		
		//		$this->con->debug=true;
		$this->con->Execute($query);
//		$this->con->debug=false;
		return true;
	}
/*
* transforms any uri into an explicit uri, if it is a literal, it s returned 
*/
function longURI($ressource)
	{
		if (strpos($ressource,"#")===0) 
			{return $this->modelURI.$ressource;} else {return $ressource;}
	}
/*
* transforms any uri into an explicit uri (with namespace), do not work with literals, works with generis v1.0 client id system
*/
function URI($uri,$type="")
	{
		if (strpos($uri,"#")===0)
		{
			$longURI =$this->modelURI.$uri;
		}
		else
		{	
			if (strpos($uri,"http://")===0)
			{
			$longURI=$uri;
			}
			else
			{
			$longURI =$this->modelURI."#".$type.$uri;
			$longURI =$uri;
			}
		}
		return $longURI;
	}
  function getAuthor($subject)
	{
		$query="select author from statements where subject='".$this->longURI($subject)."' and
		modelID='".$this->getUserModelId()."'";
		
		$sqlresult = $this->con->Execute($query);
		error_reporting(0);
		return array($sqlresult->fields[0],"email");
		 		
	}
	
	function insertAllstatements($statements,$namespace,$cache=false)
	{
		
		$x =time().rand(0,65535);
		$x=substr($x,6);
		
		$query="select modelID from models where modelURI ='".$namespace."' ";
		$sqlresult = $this->con->Execute($query);
		
		
		$query="delete from statements where modelID ='".$sqlresult->fields[0]."' ";
		
		
		$sqlresult = $this->con->Execute($query);
		$query="delete from models where modelURI ='".$namespace."' ";
		
		$sqlresult = $this->con->Execute($query);
		
		
		
		$query="insert into models values ('".$x."','".$namespace."','".$namespace."#')";
		
		$sqlresult = $this->con->Execute($query);
		
		foreach ($statements as $key=>$val)
		{
			$query="insert into statements values ('".$x."','".$val['subject']."','".$val['predicate']."','".str_replace("'","\'",$val['object'])."','".$val['l_language']."','','".$this->modelManager->user."','yyy[A,authors,viewers,administrators]','yyy[A,authors,viewers,administrators]','yyy[A,authors,viewers,administrators]' , '')";
			
			$sqlresult = $this->con->Execute($query);
		}
		return $namespace;
	}













/**
*	returns sql part to filter statements according to a specific method (read, edit, delete)
**/
function getFilter($method)
	{	
		$groupsrestriction="";
		foreach ($this->modelManager->usergroup as $k => $memberofgroup) 
			{
				$groupsrestriction .= "OR(st".$method." LIKE '%[%".$memberofgroup."%]')";
			}
		$sql = "AND((st".$method." LIKE 'y%' and author='".$this->modelManager->user."')OR(st".$method." LIKE '_y_%')OR(st".$method." LIKE '__y%'	)".$groupsrestriction."	)";
		 
		
		$this->filter[$method]=$sql;
		//$this->readrights="";
	}
/**
*	returns infos about editing or deleting a tripleID for the conencted user
**/
function getMethods($tripleId)
	{
		
		$edittriple=false;
		$groupsrestriction="";
		//print_r($this->modelManager->usergroup);
		foreach ($this->modelManager->usergroup as $k => $memberofgroup) 
			{
				$groupsrestriction .= "OR(stedit LIKE '%[%".$memberofgroup."%]')";
			}
		
		$sql  = "select stedit from statements where id ='".$tripleId."' and ((	stedit LIKE 'y%' and author='".$this->modelManager->user."')	OR	(stedit LIKE '_y_%' )OR	(stedit LIKE '__y%' )".$groupsrestriction."	)";
		//$this->con->debug=true;
			$sqlresult = $this->con->Execute($sql);
		
		while ((!($sqlresult->EOF)))
			{
			$edittriple=true;
			$sqlresult->MoveNext();
			}

			
		
		$deletetriple=false;
		$groupsrestriction="";
		foreach ($this->modelManager->usergroup as $k => $memberofgroup) 
			{
				$groupsrestriction .= " OR(	stdelete LIKE '%[%".$memberofgroup."%]'	) ";
			}
		
		$sql  = "select stdelete from statements where id ='".$tripleId."' and 	((	stdelete LIKE 'y%' and author='".$this->modelManager->user."')	OR (stdelete LIKE '_y_%' ) OR (	stdelete LIKE '__y%' )".$groupsrestriction.")
	";
		//$this->con->debug=true;
			$sqlresult = $this->con->Execute($sql);
			
		//$this->con->debug=false;
		while ((!($sqlresult->EOF)))
			{
			$deletetriple=true;
			$sqlresult->MoveNext();
			}
		return array("edit"=>$edittriple,"delete"=>$deletetriple);
	}
/**
	*Check is the method $calledmethod (setSubClass, setInstance ) may be called on $object by the connected user
	* @return Boolean
	*/
	function filterMethod($object, $calledmethod)
	{

		//while not tested
		//return true;

		error_reporting(0);
		$sqlresult =$this->con->execute("SELECT MethodAccessibilityPrivileges FROM `_privileges_classes` where `Scope` LIKE  '".$this->URI($object)."'");
		//echo "<br>SELECT MethodAccessibilityPrivileges FROM `_privileges_classes` where `Scope` LIKE  '".$this->URI($object)."' <br > <br >";
		if ($sqlresult->EOF)
						{
							
							return true;//no rights are defined
						}		
		//some rights have been defined
		
		$groupsrestriction="";
		
		
		foreach ($this->modelManager->usergroup as $k => $memberofgroup) 
			{
				$groupsrestriction .= "OR(MethodAccessibilityPrivileges LIKE '%[%".$memberofgroup."%]')";
			}
				
		$ssql = 'AND((MethodAccessibilityPrivileges LIKE \'y%\' and user=\''.$this->modelManager->user.'\')
				OR	(MethodAccessibilityPrivileges LIKE \'_y_%\' )OR(MethodAccessibilityPrivileges LIKE \'__y%\')
				'.$groupsrestriction.')';

		$sqlquery ="SELECT MethodAccessibilityPrivileges FROM `_privileges_classes`	where `Scope` LIKE '".$this->URI($object)."' and Method='".$calledmethod."'".$ssql;
		$sqlresult =$this->con->execute($sqlquery);
		if ($sqlresult->EOF) {return false;} else {return true;}

	}

	 function getPrivileges($uri)
	{
		
		$query="select * from _privileges_classes where Scope='".$this->URI($uri)."' or Scope ='".$this->URI($uri)."'";
		
		$privsadodb = $this->con->Execute($query);
		
		
		$privs=array();
		while (($privsadodb!="") && (!($privsadodb->EOF)))
           {
			if ($privsadodb->fields[0] == $this->modelManager->user) $bool=true; else $bool=false;
			$privs[$privsadodb->fields[2]]=array($bool,$privsadodb->fields);
			$privsadodb->MoveNext();
		   }

		return $privs;
	}
function setPrivileges($uri, $method,$privileges,$user)
	{
	
		//update si not used, because the line may be inexisting
		//$this->con->debug=true;
		$query="delete from _privileges_classes where Scope='".$this->URI($uri)."' and method LIKE '%".$method."%'";
		
		$privsadodb = $this->con->Execute($query);

		$query="insert into _privileges_classes values('".$user."','".$this->URI($uri)."','".$method."','".$privileges."','','') ";
		$privsadodb = $this->con->Execute($query);
		//$privsadodb = $this->con->debug=false;
		$this->con->debug=false;
	}

/*
*	This function is in charge of updating models in the kwledge base tu fully support rights v2
*	
*/
function updateIfneededModelofDatabase()
	{

$sqlResult = $this->con->Execute("	
	ALTER TABLE `statements`
  DROP `UsersOthers`,
  DROP `UsersMyGroup`,
  DROP `UsersMe`,
  DROP `Anonymous`,
  DROP `SubscribersOthers`,
  DROP `SubscribersMyGroup`,
  DROP `authorgroup`");
$sqlResult = $this->con->Execute("ALTER TABLE `statements` ADD `stread` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'yyy[]',
ADD `stedit` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'yy-[]',
ADD `stdelete`  VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'y--[Administrators]'");

$sqlResult = $this->con->Execute("CREATE TABLE IF NOT EXISTS `_mask` (
  `user` varchar(255) NOT NULL default '',
  `Scope` varchar(255) NOT NULL default '',
  `Method` varchar(255) NOT NULL default '',
  `onAssertPrivileges` longtext NOT NULL,
  `_comment` varchar(255) NOT NULL default '',
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;");

$sqlResult = $this->con->Execute("
INSERT INTO `_mask` VALUES ('pisa', 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Student', '', '[\r\nhttp://www.tao.lu/Ontologies/TAOSubject.rdf#adress ?o\r\n-> rwr-r[][],\r\nhttp://www.w3.org/2000/01/rdf-schema#Label ?o \r\n-> rwr-r[][]\r\nhttp://www.w3.org/2000/01/rdf-schema#comment ?o \r\n-> rwr-r[][]\r\nhttp://www.w3.org/1999/02/22-rdf-syntax-ns#type?o \r\n-> rwr-r[][]\r\n]\r\n', '', 1)");
$sqlResult = $this->con->Execute("INSERT INTO `_mask` VALUES ('pisa', 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Student', '', '[\r\nhttp://www.tao.lu/Ontologies/TAOSubject.rdf#adress ?o\r\n-> rwr-r[][],\r\nhttp://www.w3.org/2000/01/rdf-schema#Label ?o \r\n-> rwr-r[][]\r\nhttp://www.w3.org/2000/01/rdf-schema#comment ?o \r\n-> rwr-r[][]\r\nhttp://www.w3.org/1999/02/22-rdf-syntax-ns#type?o \r\n-> rwr-r[][]\r\n]\r\n', '', 2)");
$sqlResult = $this->con->Execute("INSERT INTO `_mask` VALUES ('pisa', 'http://www.w3.org/2000/01/rdf-schema#Class', 'setInstance', 'yy-[]\r\n', 'Privileges enabling other user to instanciate my classes', 3)");
$sqlResult = $this->con->Execute("INSERT INTO `_mask` VALUES ('pisa', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement', 'read', 'yyy[A,administrators,authors,g1,g2,viewers]', 'Default privileges to apply on teacher''s statement for read method', 4)");
$sqlResult = $this->con->Execute("INSERT INTO `_mask` VALUES ('pisa', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement', 'edit', 'yy-[A,administrators,authors,g1,g2,viewers]', 'Default privileges to apply on teacher''s statement for edit method', 5)");
$sqlResult = $this->con->Execute("INSERT INTO `_mask` VALUES ('pisa', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement', 'delete', '-y-[A,administrators,authors]', 'Default privileges to apply on teacher''s statement for delete method', 6)");
$sqlResult = $this->con->Execute("INSERT INTO `_mask` VALUES ('pisa', 'http://www.w3.org/2000/01/rdf-schema#Class', 'setInstance', 'yy-[]\r\n', 'Privileges enabling other user to instanciate my classes', 7)");
$sqlResult = $this->con->Execute("CREATE TABLE IF NOT EXISTS `_privileges_classes` ( `user` varchar(255) NOT NULL default '',  `Scope` varchar(255) NOT NULL default '',  `Method` varchar(255) NOT NULL default '',  `MethodAccessibilityPrivileges` longtext NOT NULL,  `_comment` varchar(255) NOT NULL default '',  `ID` int(11) NOT NULL auto_increment,  PRIMARY KEY  (`ID`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=6");
$sqlResult = $this->con->Execute("INSERT INTO `_privileges_classes` VALUES ('generis', 'http://www.w3.org/2000/01/rdf-schema#Class', 'setInstance', 'yy-[]', 'Privileges defining which users may create new classes , ie to acces setinstance of the Class Class', 1)");
$sqlResult = $this->con->Execute("INSERT INTO `_privileges_classes` VALUES ('generis', 'http://www.w3.org/2000/01/rdf-schema#Class', 'setSubClass', 'yy-[]', 'Privileges defining  which users may create new metaclasses , ie to acces setSubClass of the Class Class', 2)");
$sqlResult = $this->con->Execute("INSERT INTO `_privileges_classes` VALUES ('pisa', 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Student', 'setInstance', 'yy-[Teachers,FriendsofTom]', 'Privileges defining who may instanciate this class, ie creates a new Student', 3)");
$sqlResult = $this->con->Execute("INSERT INTO `_privileges_classes` VALUES ('pisa', 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Student', 'setsubClass', 'yy-[Teachers,FriendsofTom]', 'Privileges defining who may specialize this class, ie creates a new subClass', 4)");
$sqlResult = $this->con->Execute("INSERT INTO `_privileges_classes` VALUES ('generis', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#property', 'setInstance', 'yy-[]', '', 5)");
$sqlResult = $this->con->Execute("UPDATE statements SET stread = 'yyy[A,authors,viewers,administrators]' WHERE stread = ''");
$sqlResult = $this->con->Execute("UPDATE statements SET stedit = 'yyy[A,authors,viewers,administrators]' WHERE stedit = ''");
$sqlResult = $this->con->Execute("UPDATE statements SET stdelete = 'yyy[A,authors,viewers,administrators]' WHERE stdelete = ''");
$sqlResult = $this->con->Execute("ALTER TABLE `user` CHANGE `umask` `umask` VARCHAR( 128 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ");
$sqlResult = $this->con->Execute("update user set umask='rwrwr[A,authors,viewers,administrators][authors]' where umask='2222220012'");
$sqlResult = $this->con->Execute("ALTER TABLE `user` CHANGE `umask` `umask` VARCHAR( 128 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'rwrwr[A,authors,viewers,administrators][authors]'");
$sqlResult = $this->con->Execute("INSERT INTO `grouplocaluser` ( `Name` )
VALUES (
'authors'
), (
'viewers'
)");
$sqlResult = $this->con->Execute("INSERT INTO `grouplocaluser` ( `Name` )
VALUES (
'administrators'
)");
$sqlResult = $this->con->Execute("INSERT INTO `grouplocaluser` ( `Name` )
VALUES (
'g1'
), (
'g2'
)");

	}
} //end of class
?>

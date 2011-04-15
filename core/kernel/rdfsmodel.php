<?php
/*














*/
/**
*
* @package generis
* @author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 1.1
*/

class generisrdfsmodel extends generisrdfmodel
{

	/**
	*Returns all instances of $idclass
	*@param Array([0] => String) $idclass : array(14) (without #c) id of class
	*@param boolean $indirect includes instances of subclasses recursively.
	@return Array()
	**/
	function getIdInstances($idclass, $indirect = false) {
		$URIClass = $this->URI($idclass[0], "c");
		$dockey = $this->modelID;
		$instancesList = array ();

		$sqlresult = $this->con->execute("select subject from statements
						where `predicate` = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type'
						AND `modelID` in (" . $dockey . ")
						and object = '" . $URIClass . "' " . $this->filter["read"]);

		while (!$sqlresult->EOF) {
			$id = $sqlresult->fields[0];
			$instancesList[] = $id;

			/*In case of a meta class, subclasses of instances may be returned*/

			if ($URIClass == "http://www.w3.org/2000/01/rdf-schema#Class" and $id == "http://www.w3.org/2000/01/rdf-schema#Resource") {
				$subClassesid = $this->getindirectsubClassesId($id);
				foreach ($subClassesid as $key => $val) {
					$instancesList[] = $this->URI($val);
				}
			}
			$sqlresult->MoveNext();
		}

		if ($indirect) {
			$sqlresult = $this->con->execute("select subject FROM statements where `predicate` = 'http://www.w3.org/2000/01/rdf-schema#subClassOf' AND `modelID` in (" . $dockey . ") and object = '" . $URIClass . "'");
			while (!$sqlresult->EOF) {
				$subInstances = $this->getIdInstances(array (
					$sqlresult->fields[0]
				), true);
				$instancesList = array_merge($subInstances, $instancesList);
				$sqlresult->MoveNext();
			}
		}
		return $instancesList;
	}

	/**
	*Returns the number of instances of $idclass
	*@param Array([0] => String) $idclass : array(14) (without #c) id of class
	*@param boolean $indirect includes instances of subclasses recursively.
	*@return Array()
	**/
	function getNbInstances($idclass, $indirect = false) {

		$URIClass = $this->URI($idclass[0], "c");
		$dockey = $this->modelID;
		$res = 0;

		$sqlresult = $this->con->execute("select subject from statements
						where `predicate` = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type'
						AND `modelID` in (" . $dockey . ")
						and object = '" . $URIClass . "' " . $this->filter["read"]);

		while (!$sqlresult->EOF) {
			$id = $sqlresult->fields[0];
			$res++;

			/*In case of a meta class, subclasses of instances may be returned*/

			if ($id == "http://www.w3.org/2000/01/rdf-schema#Resource" and $URIClass == "http://www.w3.org/2000/01/rdf-schema#Class") {
				$subClassesid = $this->getindirectsubClassesId($id);
				foreach ($subClassesid as $key => $val) {
					$res++;
				}
			}

			$sqlresult->MoveNext();
		}

		if ($indirect) {
			$sqlresult = $this->con->execute("select subject FROM statements where `predicate` = 'http://www.w3.org/2000/01/rdf-schema#subClassOf' AND `modelID` in (" . $dockey . ") and object = '" . $URIClass . "'");
			while (!$sqlresult->EOF) {
				$res += $this->getNbInstances(array (
					$sqlresult->fields[0]
				), true);
				$sqlresult->MoveNext();
			}
		}
		return $res;
	}


  /**
  * Constructor
  */
 // var $cache;

  function rdfsmodel()
  {
  }
   function removePropertyValues($subject,$predicate)
	{
	$subject =  $this->URI($subject,"i");
	$predicate =  $this->URI($predicate,"p");
	$this->removeSubjectPredicate($subject,$predicate);
	}
  function setClass($lg,$labels,$comments,$domain,$user,$mask)
	{

	$localID = $this->microtime_float();
	$subject =  $this->modelURI."#c".$localID;
	$this->setStatement($subject, "http://www.w3.org/1999/02/22-rdf-syntax-ns#type", "http://www.w3.org/2000/01/rdf-schema#Class",$user,$mask);
	foreach ($domain as $key=>$val)
		{
			$this->setStatement($subject, "http://www.w3.org/2000/01/rdf-schema#subClassOf", $this->modelURI."#c".$val,$user);
		}
	$this->setStatement($subject, "http://www.w3.org/2000/01/rdf-schema#label", $labels[0],$user,$mask,"l",$lg[0]);
	$this->setStatement($subject, "http://www.w3.org/2000/01/rdf-schema#comment", $comments[0],$user,$mask,"l",$lg[0]);
	return $localID;

	}
	function editClass($idclass,$lg,$labels,$comments,$domain,$user,$mask)
	{

	$subject =  $this->modelURI."#c".$idclass;
	$query="delete from statements where
	(
	subject='".$subject."'
	and
	predicate='http://www.w3.org/2000/01/rdf-schema#subClassOf'
	and
	modelID='".$this->modelID."'
	)
	or
	(
	subject='".$subject."'
	and
	predicate='http://www.w3.org/2000/01/rdf-schema#label'
	and
	l_language='".$lg[0]."'
	and
	modelID='".$this->modelID."'
	)
	or
	(
	subject='".$subject."'
	and
	predicate='http://www.w3.org/2000/01/rdf-schema#comment'
	and
	l_language='".$lg[0]."'
	and
	modelID='".$this->modelID."'
	)
	";

	$this->con->Execute($query);


	foreach ($domain as $key=>$val)
		{
		$this->setStatement($subject, "http://www.w3.org/2000/01/rdf-schema#subClassOf", $this->modelURI."#c".$val,$user);
		}
	$this->setStatement($subject, "http://www.w3.org/2000/01/rdf-schema#label", $labels[0],$user,$mask,"l",$lg[0]);
	$this->setStatement($subject, "http://www.w3.org/2000/01/rdf-schema#comment", $comments[0],$user,$mask,"l",$lg[0]);
	return true;

	}

	function removeRDFSResource($idResource,$deprecatedType="c")
	{
		$subject =  $this->URI($idResource,$deprecatedType);
		$this->removeSubject($subject);
	}

	/*
*Property Management
*Creation of a Property
*@param lg array of languages (EN, FR, etc)
*@param labels array of labels
*@param comments array of comments
*@param domain array of class described by this property
*@array string Url of ressource(Class) or literal
*@param string author
*@param string default umask of author
*/

function setProperty($lg,$labels,$comments,$range,$domain,$widget, $user,$mask)
	{
	$localID = $this->microtime_float();
	$subject =  $this->modelURI."#p".$localID;
	$this->setStatement($subject, "http://www.w3.org/1999/02/22-rdf-syntax-ns#type", "http://www.w3.org/1999/02/22-rdf-syntax-ns#Property",$user,$mask);
	foreach ($domain as $key=>$val)
		{
		$this->setStatement($subject, "http://www.w3.org/2000/01/rdf-schema#domain", $this->modelURI."#c".$val,$user);
		}
	$this->setStatement($subject, "http://www.w3.org/2000/01/rdf-schema#label", $labels[0],$user,$mask,"l",$lg[0]);
	$this->setStatement($subject, "http://www.w3.org/2000/01/rdf-schema#comment", $comments[0],$user,$mask,"l",$lg[0]);
	$this->setStatement($subject, "http://www.w3.org/2000/01/rdf-schema#range", $range,$user,$mask,"r");
	$this->setStatement($subject, "http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget", $widget,$user,$mask,"r");

	return $localID;
	}
function editProperty($property_id, $lg,$labels,$comments,$domain,$range,$widget, $user,$mask)
	{

	$modelID= $this->modelID;
//	$this->con->debug->true;
	$subject =  $this->modelURI."#p".$property_id;
	$query="delete from statements where

	(
	subject='".$subject."'
	and
	predicate='http://www.w3.org/2000/01/rdf-schema#domain'
	and
	modelID in (".$modelID.")
	)
	or
	(
	subject='".$subject."'
	and
	predicate='http://www.w3.org/2000/01/rdf-schema#range'
	and
	modelID in (".$modelID.")
	)
	or
	(
	subject='".$subject."'
	and
	predicate='http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget'
	and
	modelID in (".$modelID.")
	)
	or
	(
	subject='".$subject."'
	and
	predicate='http://www.w3.org/2000/01/rdf-schema#label'
	and
	l_language='".$lg[0]."'
	and
	modelID in (".$modelID.")
	)
	or
	(
	subject='".$subject."'
	and
	predicate='http://www.w3.org/2000/01/rdf-schema#comment'
	and
	l_language='".$lg[0]."'
	and
	modelID in (".$modelID.")
	)
	";
	$this->con->Execute($query);


	foreach ($domain as $key=>$val)
		{
			$this->setStatement($subject, "http://www.w3.org/2000/01/rdf-schema#domain", $this->modelURI."#c".$val,$user);
		}
		$this->setStatement($subject, "http://www.w3.org/2000/01/rdf-schema#label", $labels[0],$user,$mask,"l",$lg[0]);
		$this->setStatement($modelID,$subject, "http://www.w3.org/2000/01/rdf-schema#comment", $comments[0],$user,$mask,"l",$lg[0]);
		$this->setStatement($subject, "http://www.w3.org/2000/01/rdf-schema#range", $range,$user,$mask,"r");
		$this->setStatement($subject, "http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget", $widget,$user,$mask,"r");
	return true;
	}



	function setInstance($lg,$labels,$comments,$type, $user,$mask)
	{
	$modelID= $this->modelID;
	$localID = $this->microtime_float();
	$subject =  $this->modelURI."#i".$localID;

	$type = $this->URI($type,"c");

	$this->setStatement($subject, "http://www.w3.org/1999/02/22-rdf-syntax-ns#type", $type,$user);
	$this->setStatement($subject, "http://www.w3.org/2000/01/rdf-schema#label", $labels[0],$user,$mask,"l",$lg[0]);
	$this->setStatement($subject, "http://www.w3.org/2000/01/rdf-schema#comment", $comments[0],$user,$mask,"l",$lg[0]);
	return $subject;
	}

	function editInstance($idInstance,$lg,$labels,$comments,$type, $user,$mask)
	{

	$modelID= $this->modelID;

	//$this->con->debug->true;
	$subject =  $this->modelURI."#i".$idInstance;
	$query="delete from statements where

	(
	subject='".$subject."'
	and
	predicate='http://www.w3.org/1999/02/22-rdf-syntax-ns#type'
	and
	modelID in (".$modelID.")
	)
	or
	(
	subject='".$subject."'
	and
	predicate='http://www.w3.org/2000/01/rdf-schema#label'
	and
	l_language='".$lg[0]."'
	and
	modelID in (".$modelID.")
	)
	or
	(
	subject='".$subject."'
	and
	predicate='http://www.w3.org/2000/01/rdf-schema#comment'
	and
	l_language='".$lg[0]."'
	and
	modelID in (".$modelID.")
	)
	";
	$this->con->Execute($query);
	$this->setStatement($subject, "http://www.w3.org/1999/02/22-rdf-syntax-ns#type", $this->modelURI."#c".$type,$user);
	$this->setStatement($subject, "http://www.w3.org/2000/01/rdf-schema#label", $labels[0],$user,$mask,"l",$lg[0]);
	$this->setStatement($subject, "http://www.w3.org/2000/01/rdf-schema#comment", $comments[0],$user,$mask,"l",$lg[0]);

	return $idInstance;
	}
function setPropertyValuesforInstance($idinstance,$Idproperty, $lg,$values,$mask)
	{
		//print_r(array($idinstance,$Idproperty, $lg,$values,$mask));

		if ($lg[0]=="XX") {$lg[0]="";}//Lg independant property
		$modelID= $this->modelID;
		$localID = $this->microtime_float();
		$subject =  $this->URI($idinstance,"i");
	$predicate =  $this->URI($Idproperty,"p");
		foreach ($values as $k=>$val)
		{
			if (strpos($values[0],"#i")===0)
			{
			$this->setStatement($subject, $predicate, $this->modelURI.$val,"",$mask,"r");
			}
			else
			{
				if (strpos($values[0],"http")===0)
				{

				$this->setStatement($subject, $predicate, $val,"",$mask,"r");
				}
				else
				{

				$this->setStatement($subject, $predicate, $val,"",$mask,"l",$lg[0]);
				}
			}
		}
return $localID;
	}

function editPropertyValuesforInstance($idinstance,$Idproperty,$lg,$values,$mask)
	{
	if (($lg[0]=="XX") || (strpos($values[0],"#i")===0)  )  {$lg[0]="";}
	$modelID= $this->modelID;
	$localID = $this->microtime_float();
	$subject =  $this->URI($idinstance,"i");
	$predicate =  $this->URI($Idproperty,"p");

	$query="delete from statements where


	subject='".$subject."'
	and
	predicate='".$predicate."'
	and

		l_language='".$lg[0]."'

	and
	modelID in (".$modelID.")

	";

	$this->con->Execute($query);

    $this->setPropertyValuesforInstance($idinstance,$Idproperty,$lg,$values,$mask);
	return true;


	}

function affiliate($idinstance,$idproperty, $memberlist)
			{
				while(list($x,$value)=each($memberlist))
					{
					$this->setPropertyValuesforInstance($idinstance,$idproperty, array("XX"),array($value));
					}
			}

function unaffiliate($idinstance,$idproperty, $memberlist)
	{

		$querye="select Id,rdf from classinstance WHERE Idinstance='".$idinstance."' AND IdProperty='".$idproperty."'";
                $result=$this->con->Execute($querye);
                while (!$result-> EOF) {
		      foreach ($memberlist as $x=>$value)
		      {
		      if (strpos($result->fields[1],$value))
                          {
		       	  $query="Delete from classInstance WHERE Id='".$result->fields[0]."'";
		          $this->con->Execute($query);
			  //echo $query;
			  break 2;
			  }
		      }
                $result-> MoveNext();
                }
	}

function execSQL($WHERE_CLAUSE)
	{



$dockey=$this->modelID;
//$this->con->debug=true;
	$sqlresult = $this->con->Execute("SELECT subject,predicate,object,id,author
				FROM statements
				WHERE modelid in (".$dockey.") ".$WHERE_CLAUSE);

	$rows=array();
	while (!$sqlresult-> EOF)
           {
				$rows[]=$sqlresult->fields;
				$sqlresult-> MoveNext();
		   }
	return $rows;
	}
/*
*	@deprecated
*	use instead getRessourceDescription
*	Return Class description
*	"PropertiesValues" contains all (direct and indirect) properties describing this Class
*/

	function getClassDescription($idclass)
	{

		$URIClass=$this->URI($idclass[0],"c");
		$dockey=$this->modelID;
		/*Benchmark a direct access*/
		$sqlresultlabel = $this->con->Execute("SELECT object, l_language
				FROM statements
				WHERE subject = '".$URIClass ."'
				AND modelid in (".$dockey.")

				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#label' ".$this->filter["read"]."

				");

		$sqlresultcomment = $this->con->Execute("SELECT object, l_language
				FROM statements
				WHERE subject = '".$URIClass ."'
					AND modelid in (".$dockey.")

				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#comment'
				".$this->filter["read"]."
					");
		 $labels = $this->sortSQLResultbyLG($sqlresultlabel);
		$comments = $this->sortSQLResultbyLG($sqlresultcomment);
		 $sqlresultsubClassOf = $this->con->Execute("SELECT object
				FROM statements
				WHERE subject = '".$URIClass ."'
					AND modelid in (".$dockey.")
				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#subClassOf'".$this->filter["read"]);

		 $subClassOf=array();
		 while (!$sqlresultsubClassOf-> EOF)
           {
				$subClassOf[]=str_replace($this->modelURI,"",$sqlresultsubClassOf->fields[0]);
				$sqlresultsubClassOf-> MoveNext();
		   }

		$properties = $this->getAllProperties($idclass[0]);

		return array("pDescription" =>array("InstanceKey" =>str_replace($this->modelURI,"",$URIClass) ,"InstanceLabel"=>$labels[0],"InstanceComment"=>$comments[0],"PropertiesValues" => $properties,"InstanceParent"=>$subClassOf));
	}
	/**
	* return type of a ressource (e.g. a meta class for a class)
	*/
	function getType($Ressource)
	{
		//print_r($this);
		$typeOf=array();$URIRessource="";
		$URIRessource=$this->URI($Ressource);
		$dockey=$this->modelID;

		$sqlresulttypeOf = $this->con->execute("SELECT object
				FROM statements
				WHERE subject = '".$URIRessource."'
				AND modelID in (".$dockey.")
				AND predicate = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type'
				".$this->filter["read"]);


		 while (!$sqlresulttypeOf->EOF)
           {
				$aTypeOf = $sqlresulttypeOf->fields[0];
				$typeOf[]=$aTypeOf;
				$sqlresulttypeOf-> MoveNext();
				/*
				$sqlresultsubClassOf = $this->con->Execute("SELECT object
				FROM statements
				WHERE subject = '".$aTypeOf."'
					AND modelid in (".$dockey.")
				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#subClassOf'".$this->filter["read"]);
				 $subClassOf=array();
				 while (!$sqlresultsubClassOf-> EOF)
				   {

						$typeOf=array_merge($typeOf,$this->getType($sqlresultsubClassOf->fields[0]));
						$sqlresultsubClassOf-> MoveNext();
				   }
				*/

		   }

		$sqlresultsubClassOf = $this->con->Execute("SELECT object
				FROM statements
				WHERE subject = '".$URIRessource ."'
					AND modelid in (".$dockey.")
				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#subClassOf'".$this->filter["read"]);

		 $subClassOf=array();
		 while (!$sqlresultsubClassOf-> EOF)
           {

				$typeOf=array_merge($typeOf,$this->getType($sqlresultsubClassOf->fields[0]));
				$sqlresultsubClassOf-> MoveNext();
		   }
		 return array_unique($typeOf);

	}
	/**
	* return array("label"]=> thelabel,["comment"] => thecomment) if resource is unknown uris instead of label comment are returned
	*/
	function getLabelComment($Ressource)
	{
		/*if ($this->cache->isincache("getLabelComment",array($Ressource)))
			{
				$cache_answer = $this->cache->getincache("getLabelComment",array($Ressource));

				return $cache_answer;
			}*/

		$typeOf=array();$URIRessource="";
		$URIRessource=$this->URI($Ressource);
		$dockey=$this->modelID;

		$sqlresultlabel = $this->con->Execute("SELECT object, l_language
				FROM statements
				WHERE subject = '".$URIRessource ."'
				AND modelid in (".$dockey.")

				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#label'
				".$this->filter["read"]."

				");

		$sqlresultcomment = $this->con->Execute("SELECT object, l_language
				FROM statements
				WHERE subject = '".$URIRessource ."'
				AND modelid in (".$dockey.")

				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#comment'
				".$this->filter["read"]."
					");


		$labels = $this->sortSQLResultbyLG($sqlresultlabel);
		$comments = $this->sortSQLResultbyLG($sqlresultcomment);

		if ($labels==array("")) {$labels=$comments=array( str_replace("\\\\","\\",$URIRessource));}

		$return = array("label" => $labels[0],"comment" => $comments[0]);
		//$this->cache->setincache("getLabelComment",array($Ressource),$return);
		return 	$return;

	}


	function getRessourceDescription($Ressource)
		{

			/*if ($this->cache->isincache("getRessourceDescription",array($Ressource)))
			{
				$cache_answer = $this->cache->getincache("getRessourceDescription",array($Ressource));

				return $cache_answer;
			}*/



			$URIRessource=$this->URI($Ressource);


			$dockey=$this->modelID;
			$type = $this->getType($Ressource);

			$properties =array();
			foreach ($type as $key=>$val)
				{
				$properties =array_merge($properties,$this->getAllProperties($val));
				}
			$properties = $this->multi_unique($properties);
			$propertiesvalues=Array();
			foreach ($properties as $key=>$val)
			{
				if (strpos($val["PropertyKey"],"#")===0)
				{
				$val["PropertyKey"] =$this->modelURI.$val["PropertyKey"];
				}

				$sqlresultvalues = $this->con->execute("SELECT object,l_language,id
				FROM statements
				WHERE subject = '".$URIRessource ."'
				AND modelid in (".$dockey.")
				AND predicate = '".$val["PropertyKey"]."'
				".$this->filter["read"]."

				");
				$this->con->debug=false;
				//print_r($sqlresultvalues);
				$result= $this->sortSQLResultbyLG($sqlresultvalues);

				$isset =false;
				foreach ($result as $k=>$v)
				{	$isset=true;


					$val["PropertyValue"]=str_replace($this->modelURI,"",$v);
					$val["TripleID"]=$k;
					$propertiesvalues[]=$val;
					$sqlresultvalues->MoveNext();
				}
				if (!($isset)) {$propertiesvalues[]=$val;}
			}

			$return = array("type" => $type,"properties" => $propertiesvalues,"relatedproperties" => $this->getAllProperties($Ressource));


			//$this->cache->setincache("getRessourceDescription",array($Ressource),$return);


			return $return;
		}



	function isSubClassOf($pClass, $pSubClass)
		{
			$pSubClass=$this->URI($pSubClass);
			$pClass=$this->URI($pClass);

			$dockey=$this->modelID;

			$sqlresultvalues = $this->con->execute("SELECT object,l_language
				FROM statements
				WHERE subject = '".$pSubClass ."'
				AND modelid in (".$dockey.")
				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#subClassOf'
				AND object = '".$pClass."'

				");

			 while (!$sqlresultvalues-> EOF)
			{
				 return true;
			}
			$classes = $this->getindirectsubClassesId($pClass);

			foreach ($classes as $key=>$val)
				{
					if ($val == $pSubClass) {return true;}
				}


			return false;
		}



	function getAllProperties($idclass)
		{


		$idclass=$this->URI($idclass,"c");


		$dockey=$this->modelID;
		$sqlresult =$this->con->execute("select subject from statements where predicate = 'http://www.w3.org/2000/01/rdf-schema#domain' and object = '".$idclass."' AND modelid in (".$dockey.")".$this->filter["read"] );

		$instancesList=array();
		 while (!$sqlresult-> EOF)
           {
				$id = $sqlresult->fields[0];

				$sqlresultlabel = $this->con->execute("SELECT object, l_language
				from statements
				WHERE subject = '".$id."'
				AND modelid in (".$dockey.")

				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#label'

				".$this->filter["read"]);


				$sqlresultcomment = $this->con->execute("SELECT object, l_language
				from statements
				WHERE subject = '".$id."'
				AND modelid in (".$dockey.")

				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#comment'
				".$this->filter["read"]);
				$labels = $this->sortSQLResultbyLG($sqlresultlabel);
				$comments = $this->sortSQLResultbyLG($sqlresultcomment);
				$sqlresultrange =$this->con->execute("SELECT object
				from statements
				WHERE subject = '".$id."'
				AND modelid in (".$dockey.")
				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#range'".$this->filter["read"]);

				$sqlresultwidget = $this->con->execute("SELECT object
				from statements
				WHERE subject = '".$id."'
				AND modelid in (".$dockey.")
				AND predicate = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget'".$this->filter["read"]);

				$instancesList[]=array("PropertyKey"=>str_replace($this->modelURI,"",$sqlresult->fields[0]),"PropertyLabel"=>$labels[0],"PropertyComment"=>$comments[0],"PropertyRange"=>str_replace($this->modelURI,"",$sqlresultrange->fields[0]),"PropertyWidget"=>$sqlresultwidget->fields[0],"range"=>str_replace($this->modelURI,"",$sqlresultrange->fields[0]),"widget"=>$sqlresultwidget->fields[0]);
				$sqlresult-> MoveNext();


		   }

		$sqlresultsubClassOf = $this->con->execute("SELECT object
				from statements
				WHERE subject = '".$idclass."'
				AND modelid in (".$dockey.")
				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#subClassOf'".$this->filter["read"]);

		 while (!$sqlresultsubClassOf-> EOF)
           {

				$subinstancesList = $this->getAllProperties($sqlresultsubClassOf->fields[0]);


				$instancesList = array_merge($instancesList,$subinstancesList);
				$sqlresultsubClassOf-> MoveNext();
		   }

	return $instancesList;

	}
	function multi_unique($array) {
	   $new=$new1=array();
	   foreach ($array as $k=>$na)
		   $new[$k] = serialize($na);
	   $uniq = array_unique($new);
	   foreach($uniq as $k=>$ser)
		   $new1[$k] = unserialize($ser);

	   return ($new1);
	}

	function getsubClasses($idclass)
	{
		//$hdl = fopen("debugging","a+");fwrite($hdl,"getsubClasses :".$idclass[0]."\r\n");fclose($hdl);
			/*if ($this->cache->isincache("getsubClasses",array($idclass)))
			{
				$cache_answer = $this->cache->getincache("getsubClasses",array($idclass));

				return $cache_answer;
			}*/

		$URIClass=$this->URI($idclass[0],"c");

		//print_r($this);die();

		$dockey=$this->modelID;


		/*Benchmark a direct access*/
		$instancesList=array();
		$sqlresult = $this->con->execute("select subject FROM statements where predicate = 'http://www.w3.org/2000/01/rdf-schema#subClassOf' AND modelid in (".$dockey.") and object = '".$URIClass."'".$this->filter["read"]);

		 while (!$sqlresult-> EOF)
           {
				$id = $sqlresult->fields[0];

				$sqlresultlabel = $this->con->execute("SELECT object, l_language
				FROM statements
				WHERE
				subject = '".$id."'
				AND modelid in (".$dockey.")
				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#label'
				".$this->filter["read"]);
				$labels = $this->sortSQLResultbyLG($sqlresultlabel,$id);

				$sqlresultcomment = $this->con->execute("SELECT object, l_language
				FROM statements
				WHERE subject = '".$id."'
				AND modelid in (".$dockey.")
				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#comment'
				".$this->filter["read"]);
				$comments = $this->sortSQLResultbyLG($sqlresultcomment);

				$instancesList[]=array("ClassKey"=>str_replace($this->modelURI,"",$sqlresult->fields[0]),"ClassLabel"=>$labels[0],
					"ClassComment"=>$comments[0]);
				$sqlresult-> MoveNext();
		   }

		$return = array("pDescription" =>$instancesList);
		//$this->cache->setincache("getsubClasses",array($idclass),$return);


		return $return;


		}

function search($pCriteria,$exact)
	{




		$dockey=$this->modelID;

		$sql = "select subject FROM statements where modelid in (".$dockey.")".$this->filter["read"];
		$i=0;
		$lg = sizeof($pCriteria);

		while ($i<=$lg-1)
			{


				if
					(($pCriteria[$i]=="#label") OR ($pCriteria[$i]=="#comment") OR ($pCriteria[$i]=="#range"))
						{$pCriteria[$i] = "http://www.w3.org/2000/01/rdf-schema".$pCriteria[$i] ;}

				if ($pCriteria[$i]=="#widget")
					{$pCriteria[$i] = "http://www.tao.lu/datatypes/WidgetDefinitions.rdf".$pCriteria[$i] ;}
				if ($pCriteria[$i]=="#type")
					{
					$pCriteria[$i] = "http://www.w3.org/1999/02/22-rdf-syntax-ns".$pCriteria[$i] ;

					}
				$ambiguity = false;
				//Subject may be literal or resource
				if (strpos($pCriteria[$i+1],"#")===0)
				{
					$ambiguity = true;
					$supposition =$this->modelURI.$pCriteria[$i+1];
				}
				//predicate is alway a resource
				if (strpos($pCriteria[$i],"#")===0)
				{
					$pCriteria[$i] =$this->modelURI.$pCriteria[$i];
				}

				if (!($ambiguity))
					{
				$sql .= " and subject in
				(
				select subject FROM statements where modelid in (".$dockey.")
					AND predicate='".$pCriteria[$i]."' AND object='".$pCriteria[$i+1]."'
				)";
					}
				else
					{
					$sql .= "
					AND (
					(
						subject in
						(
						select subject FROM statements where modelid in (".$dockey.")
							AND predicate='".$pCriteria[$i]."' AND object='".$pCriteria[$i+1]."'
						)
					)
						OR
					(
						subject in
						(
						select subject FROM statements where modelid in (".$dockey.")
							AND predicate='".$pCriteria[$i]."' AND object='".$supposition."'
						)
					)
					)
				";
					}

				$i = $i+2;
			}

		$results=Array();
		//$this->con->debug=true;

		$sqlresult = $this->con->execute($sql);
		//$this->con->debug=false;
		 while (($sqlresult!=null) and (!($sqlresult->EOF)))
           {
			 $results[]=$sqlresult->fields[0];
			$sqlresult->MoveNext();
		   }
		//print_r($results);
		return array("pResult" => array_unique($results));


	}
function searchInstances($pCriteria,$exact)
	{


		$dockey=$this->modelID;

		$sql = "select subject FROM statements where modelid in (".$dockey.")". $this->filter["read"];
		$i=0;
		$lg = sizeof($pCriteria);

		while ($i<=$lg-1)
			{


				if
					(($pCriteria[$i]=="#label") OR ($pCriteria[$i]=="#comment") OR ($pCriteria[$i]=="#range"))
						{$pCriteria[$i] = "http://www.w3.org/2000/01/rdf-schema".$pCriteria[$i] ;}

				if ($pCriteria[$i]=="#widget")
					{$pCriteria[$i] = "http://www.tao.lu/datatypes/WidgetDefinitions.rdf".$pCriteria[$i] ;}
				if ($pCriteria[$i]=="#type")
					{
					$pCriteria[$i] = "http://www.w3.org/1999/02/22-rdf-syntax-ns".$pCriteria[$i] ;

					}
				$ambiguity = false;
				//Subject may be literal or resource
				if (strpos($pCriteria[$i+1],"#")===0)
				{
					$ambiguity = true;
					$supposition =$this->modelURI.$pCriteria[$i+1];
				}
				//predicate is alway a resource
				if (strpos($pCriteria[$i],"#")===0)
				{
					$pCriteria[$i] =$this->modelURI.$pCriteria[$i];
				}

				if (!($ambiguity))
					{
				$sql .= " and subject in
				(
				select subject FROM statements where modelid in (".$dockey.")
					AND predicate='".$pCriteria[$i]."' AND object='".$pCriteria[$i+1]."'
				)";
				$sql .= " and subject not in
				(
				select subject FROM statements where modelid in (".$dockey.")
					AND (
					predicate='http://www.w3.org/2000/01/rdf-schema#subClassOf'
					or
					predicate='http://www.w3.org/2000/01/rdf-schema#domain'
					or
						(
						predicate='http://www.w3.org/1999/02/22-rdf-syntax-ns#type'
						and
						object='http://www.w3.org/1999/02/22-rdf-syntax-ns#type'
						)
					)
				)";
					}
				else
					{
					$sql .= "
					AND (
					(
						subject in
						(
						select subject FROM statements where modelid in (".$dockey.")
							AND predicate='".$pCriteria[$i]."' AND object='".$pCriteria[$i+1]."'
						)
					)
						OR
					(
						subject in
						(
						select subject FROM statements where modelid in (".$dockey.")
							AND predicate='".$pCriteria[$i]."' AND object='".$supposition."'
						)
					)
					)
				";
					}

				$i = $i+2;
			}
		$sql .= " and subject not in
				(
				select subject FROM statements where modelid in (".$dockey.")
					AND (
					predicate='http://www.w3.org/2000/01/rdf-schema#subClassOf'
					or
					predicate='http://www.w3.org/2000/01/rdf-schema#domain'
					or
						(
						predicate='http://www.w3.org/1999/02/22-rdf-syntax-ns#type'
						and
						object='http://www.w3.org/2000/01/rdf-schema#Class'
						)
					)
				)";

		$results=Array();

		$sqlresult = $this->con->execute($sql);
		 while (!$sqlresult-> EOF)
           {
			 $results[]=$sqlresult->fields[0];
			$sqlresult-> MoveNext();
		   }

		return array("pResult" => array_unique($results));

	}

/**
* @deprecated
* use instead getRessourceDescription
*@return Array Returns instance description
*@param Array([0] => String) $idinstance : array(14) (without #i)
*@param TAO:session $pSession returned by authenticate service
*@access public
*
**/

function getInstanceDescription($idinstance,$onlylabelcomment=false)
	{

		$URIInstance=$this->URI($idinstance[0],"i");

		$dockey=$this->modelID;
		$sqlresultlabel = $this->con->execute("SELECT object, l_language
				FROM statements
				WHERE subject = '".$URIInstance ."'
				AND modelid in (".$dockey.")

				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#label'
				".$this->filter["read"]."

				");

		$sqlresultcomment = $this->con->execute("SELECT object, l_language
				FROM statements
				WHERE subject = '".$URIInstance ."'
				AND modelid in (".$dockey.")

				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#comment'
				".$this->filter["read"]."

				");
		$labels = $this->sortSQLResultbyLG($sqlresultlabel);
				$comments = $this->sortSQLResultbyLG($sqlresultcomment);
		$sqlresulttypeOf = $this->con->execute("SELECT object
				FROM statements
				WHERE subject = '".$URIInstance."'
				AND modelid in (".$dockey.")
				AND (l_language = '".$this->modelManager->lg."' OR l_language = '' OR l_language = '".$this->modelManager->deflg."')
				AND predicate = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type'
				".$this->filter["read"]."
				LIMIT 1
				");


		 $typeOf=array();
		 $properties = array();
		 while (!$sqlresulttypeOf-> EOF)
           {
				$type= $sqlresulttypeOf->fields[0];
				$typeOf[]=str_replace($this->modelURI,"",$sqlresulttypeOf->fields[0]);
				$properties = array_merge($properties,$this->getAllProperties($type));
				$sqlresulttypeOf-> MoveNext();
		   }

//print_r($this->getindirectSuperClasses($URIInstance));
		//$properties = $this->getAllProperties($type);
		$propertiesvalues=Array();
		foreach ($properties as $key=>$val)
		{
			if (strpos($val["PropertyKey"],"#")===0)
				{
				$x =$this->modelURI.$val["PropertyKey"];
				}
				else
				{
					$x =$val["PropertyKey"];
				}
			$sqlresultvalues = $this->con->execute("SELECT object,l_language
				FROM statements
				WHERE subject = '".$URIInstance ."'
				AND modelid in (".$dockey.")
				AND predicate = '".$x."'
				".$this->filter["read"]);


				$result= $this->sortSQLResultbyLG($sqlresultvalues);

				$isset =false;
				foreach ($result as $k=>$v)
				{	$isset=true;


					$val["PropertyValue"]=str_replace($this->modelURI,"",$v);
					$propertiesvalues[]=$val;
					$sqlresultvalues->MoveNext();
				}
				if (!($isset)) {$propertiesvalues[]=$val;}

		}

		return array("pDescription" =>array("InstanceKey" => str_replace($this->modelURI,"",$URIInstance),"label"=>$labels[0],"comment"=>$comments[0],"PropertiesValues" => $propertiesvalues,"InstanceParent"=>$typeOf));
	}

/**
*@deprecated
*use instead getRessourceDescription
*Returns Description of a property (label comment, domain, widget,range)
*@param TAO:session $pSession returned by authenticate service
*@access public
*@param Array([0] => String) $idproperty : array(14) (without #p) id of property
*@param Array([0] => String) $remotedocKey : is optional. It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule"
*@return Array()

**/
function getPropertyDescription($idproperty)
	{


		$URIProperty=$this->URI($idproperty[0],"c");

		$dockey=$this->modelID;
		$sqlresultlabel = $this->con->execute("SELECT object, l_language
				FROM statements
				WHERE subject = '".$URIProperty ."'
				AND modelid in (".$dockey.")

				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#label'
				".$this->filter["read"]."

				");


		$sqlresultcomment = $this->con->execute("SELECT object, l_language
				FROM statements
				WHERE subject = '".$URIProperty ."'
					AND modelid in (".$dockey.")

				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#comment'
				".$this->filter["read"]."
					");
		$labels = $this->sortSQLResultbyLG($sqlresultlabel);
		$comments = $this->sortSQLResultbyLG($sqlresultcomment);

		 $sqlresultwidget = $this->con->execute("SELECT object
				FROM statements
				WHERE subject = '".$URIProperty ."'
					AND modelid in (".$dockey.")
					AND (l_language = '".$this->modelManager->lg."' OR l_language = '' OR l_language = '".$this->modelManager->deflg."')
				AND predicate = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget'
					".$this->filter["read"]."
					LIMIT 1");
		 $sqlresultdomain = $this->con->execute("SELECT object
				FROM statements
				WHERE subject = '".$URIProperty ."'
					AND modelid in (".$dockey.")

				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#domain'
				".$this->filter["read"]."
					LIMIT 1");

		$domains=array();
		while (!$sqlresultdomain-> EOF)
           {

				$domains[] = str_replace($this->modelURI,"",$sqlresultdomain->fields[0]);

				$sqlresultdomain-> MoveNext();
		   }

		 $sqlresultrange = $this->con->execute("SELECT object
				FROM statements
				WHERE subject = '".$URIProperty ."'
					AND modelid in (".$dockey.")

				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#range'
				".$this->filter["read"]."
					LIMIT 1");



		$description=array("PropertyKey" => str_replace($this->modelURI,"",$URIProperty),"PropertyLabel" => $labels[0], "PropertyComment" =>  $comments[0], "PropertyDomain" => $domains, "PropertyRange" =>  str_replace($this->modelURI,"",$sqlresultrange->fields[0]), "PropertyWidget" =>  $sqlresultwidget->fields[0]);

		return array("pDescription" => $description);

	}

/**
*Returns direct properties of $idclass
*@param TAO:session $pSession returned by authenticate service
*@access public
*@param Array([0] => String) $idclass : array(14) (without #c) id of class
*@param Array([0] => String) $remotedocKey : is optional. It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule"
*@return Array()

**/

function getProperties($idclass)
	{	//$hdl = fopen("debugging","a+");fwrite($hdl,"getProperties :".$idclass[0]."\r\n");fclose($hdl);
		/*if ($this->cache->isincache("getProperties",array($idclass)))
			{
				$cache_answer = $this->cache->getincache("getProperties",array($idclass));

				return $cache_answer;
			}*/

		$URIClass=$this->URI($idclass[0],"c");
		$dockey=$this->modelID;

		$instancesList=array();

		$sqlresult = $this->con->execute("select subject from statements where predicate = 'http://www.w3.org/2000/01/rdf-schema#domain' AND modelid in (".$dockey.") and object = '".$URIClass."'".$this->filter["read"]);


		 while (!$sqlresult-> EOF)
           {
				$id = $sqlresult->fields[0];

				$sqlresultlabel = $this->con->execute("SELECT object, l_language
				from statements
				WHERE subject = '".$id."'
				AND modelid in (".$dockey.")

				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#label'
				".$this->filter["read"]);

				$sqlresultcomment = $this->con->execute("SELECT object, l_language
				from statements
				WHERE subject = '".$id."'
				AND modelid in (".$dockey.")

				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#comment'
				".$this->filter["read"]);
				$labels = $this->sortSQLResultbyLG($sqlresultlabel);
				$comments = $this->sortSQLResultbyLG($sqlresultcomment);
				$sqlresultrange = $this->con->execute("SELECT object
				from statements
				WHERE subject = '".$id."'
				AND modelid in (".$dockey.")
				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#range'".$this->filter["read"]);

				$sqlresultwidget = $this->con->execute("SELECT object
				from statements
				WHERE subject = '".$id."'
				AND modelid in (".$dockey.")
				AND predicate = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget'".$this->filter["read"]);
				$instancesList[]=array("PropertyKey"=>str_replace($this->modelURI,"",$sqlresult->fields[0]),"PropertyLabel"=>$labels[0],"PropertyComment"=>$comments[0],"PropertyRange"=>$sqlresultrange->fields[0],"PropertyWidget"=>$sqlresultwidget->fields[0]);
				$sqlresult-> MoveNext();
		   }
		  $return = array("pDescription" =>$instancesList);
		  //$this->cache->setincache("getProperties",array($idclass),$return);
		return $return;
	}
function getsubProperties($idproperty)
	{
		/*if ($this->cache->isincache("getsubProperties",array($idproperty)))
			{
				$cache_answer = $this->cache->getincache("getsubProperties",array($idproperty));

				return $cache_answer;
			}*/
		$URIClass=$this->URI($idproperty[0],"c");
		$dockey=$this->modelID;

		$instancesList=array();
		$sqlresult = $this->con->execute("select subject from statements where predicate = 'http://www.w3.org/2000/01/rdf-schema#subPropertyOf' AND modelid in (".$dockey.") and object = '".$URIClass."'".$this->filter["read"]);


		 while (!$sqlresult-> EOF)
           {
				$id = $sqlresult->fields[0];

				$sqlresultlabel = $this->con->execute("SELECT object, l_language
				from statements
				WHERE subject = '".$id."'
				AND modelid in (".$dockey.")

				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#label'
				".$this->filter["read"]."
				limit 1");

				$sqlresultcomment = $this->con->execute("SELECT object, l_language
				from statements
				WHERE subject = '".$id."'
				AND modelid in (".$dockey.")

				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#comment'
				".$this->filter["read"]."
				limit 1");
				$labels = $this->sortSQLResultbyLG($sqlresultlabel);
				$comments = $this->sortSQLResultbyLG($sqlresultcomment);
				$sqlresultrange = $this->con->execute("SELECT object
				from statements
				WHERE subject = '".$id."'
				AND modelid in (".$dockey.")
				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#range'"
				.$this->filter["read"]);

				$sqlresultwidget = $this->con->execute("SELECT object
				from statements
				WHERE subject = '".$id."'
				AND modelid in (".$dockey.")
				AND predicate = 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget'"
				.$this->filter["read"]);
				$instancesList[]=array("PropertyKey"=>str_replace($this->modelURI,"",$sqlresult->fields[0]),"PropertyLabel"=>$labels[0],"PropertyComment"=>$comments[0],"PropertyRange"=>$sqlresultrange->fields[0],"PropertyWidget"=>$sqlresultwidget->fields[0]);
				$sqlresult-> MoveNext();
		   }

		$return = array("pDescription" =>$instancesList);
		//$this->cache->setincache("getsubProperties",array($idproperty),$return);
		return $return;
	}

function setSequence($sequence,$user,$mask)
	{
		$l = rand(0,65535);
		$seq = $this->setInstance(array("EN"),array($l),array($l),"http://www.w3.org/1999/02/22-rdf-syntax-ns#Seq", $user,$mask);

		foreach ($sequence as $key=>$val)
			{
				 $this->setPropertyValuesforInstance($seq,"http://www.w3.org/1999/02/22-rdf-syntax-ns#_".$key, array(""),array($val),$mask);
			}
		return $seq;
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

function getInstances($idclass,$indirect=false)

	{
		//$hdl = fopen("debugging","a+");fwrite($hdl,"getInstances :".$idclass[0]."\r\n");fclose($hdl);
		/*if ($this->cache->isincache("getInstances",array($idclass,$indirect)))
			{
				$cache_answer = $this->cache->getincache("getInstances",array($idclass,$indirect));

				return $cache_answer;
			}*/

		$URIClass=$this->URI($idclass[0],"c");

		$dockey=$this->modelID;

		$instancesList=array();
		/*** Very slow one request
		$sqlresult = $this->con->execute("SELECT subject, predicate, object
		FROM statements
		WHERE subject
		IN (

			SELECT subject
			FROM statements
			WHERE predicate = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type'
			AND modelid
			IN ( 6, 7, 8 )
			AND object = '".$URIClass."'
			)
		AND (
			predicate = 'http://www.w3.org/2000/01/rdf-schema#label'
			OR predicate = 'http://www.w3.org/2000/01/rdf-schema#comment'
			)
		AND (
			l_language = 'EN'
			OR l_language = ''
			OR l_language = 'FR'
			)
			LIMIT 0 , 30");
		 while (!$sqlresult-> EOF)
			{
			 $instance=$sqlresult->fields[0];
			 if ($sqlresult->fields[1]=="http://www.w3.org/2000/01/rdf-schema#label")
				{

				 $instancesList[$instance]["InstanceKey"]=$instance;
				 $instancesList[$instance]["InstanceLabel"]=$sqlresult->fields[2];
				 }
				else
				{
					$instancesList[$instance]["InstanceKey"]=$instance;
				 $instancesList[$instance]["InstanceComment"]=$sqlresult->fields[2];
				}
				$sqlresult-> MoveNext();

			}
		*/

		$sqlresult = $this->con->execute("select subject from statements where predicate = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' AND modelid in (".$dockey.") and object = '".$URIClass."' ".$this->filter["read"]);




		//echo "select subject from statements where predicate = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' AND modelid in (".$dockey.") and object = '".$URIClass."'";

		 while (!$sqlresult-> EOF)
           {
				$id = $sqlresult->fields[0];
				/*
				$sqlresultlabel = $this->con->execute("SELECT predicate,object
				from statements
				WHERE subject = '".$id."'
				AND modelid in (".$dockey.")
				AND
					(
					predicate = 'http://www.w3.org/2000/01/rdf-schema#label'
					OR
					predicate = 'http://www.w3.org/2000/01/rdf-schema#comment'
					)
					AND (l_language = '".$this->modelManager->lg."' OR l_language = '' OR l_language = '".$this->modelManager->deflg."')
				");
				while (!$sqlresultlabel-> EOF)
					{
					if ($sqlresultlabel->fields[0] == 'http://www.w3.org/2000/01/rdf-schema#label')
						{$label=$sqlresultlabel->fields[1];}
					else {$comment=$sqlresultlabel->fields[1];}
					$sqlresultlabel-> MoveNext();

					}
				*/

				$sqlresultlabel = $this->con->execute("SELECT object, l_language
				from statements
				WHERE subject = '".$id."'
				AND modelid in (".$dockey.")
				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#label'
				".$this->filter["read"]);

				$sqlresultcomment = $this->con->execute("SELECT object, l_language
				from statements
				WHERE subject = '".$id."'
				AND modelid in (".$dockey.")

				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#comment'
				".$this->filter["read"]);

				$labels = $this->sortSQLResultbyLG($sqlresultlabel,$id);
				$comments = $this->sortSQLResultbyLG($sqlresultcomment,$id);



				/*[$sqlresul....] is used to get distinct results, faster than a distinct in sql*/
				$instancesList[$sqlresult->fields[0]]=array(
					"InstanceKey"=>str_replace($this->modelURI,"",$sqlresult->fields[0])
						,"InstanceLabel"=>$labels[0],"InstanceComment"=>$comments[0]);



				/*In case of a meta class, subclasses of instances may be returned*/

				if (($id!="http://www.w3.org/2000/01/rdf-schema#Class") and ($URIClass=="http://www.w3.org/2000/01/rdf-schema#Class") and ($id=="http://www.w3.org/2000/01/rdf-schema#Resource"))
				{
					$subClassesid = $this->getindirectsubClassesId($id);
					foreach ($subClassesid as $key=>$val)
						{	$lc = $this->getLabelComment($val);

							$instancesList[]=array("InstanceKey"=>str_replace($this->modelURI,"",$val),"InstanceLabel"=>$lc["label"], "InstanceComment" => $lc["comment"]);
						}
						//print_r($subClassesid);
				}


				$sqlresult-> MoveNext();
		   }

		if ($indirect)
		{

			$sqlresult = $this->con->execute("select subject FROM statements where predicate = 'http://www.w3.org/2000/01/rdf-schema#subClassOf' AND modelid in (".$dockey.") and object = '".$URIClass."'");

			while (!$sqlresult-> EOF)
				{
					$subInstances = $this->getInstances(array($sqlresult->fields[0]),true);

					$instancesList=array_merge($subInstances["pDescription"],$instancesList);
					$sqlresult->MoveNext();


				}

		}


		$return = array("pDescription" =>$instancesList);


		//$this->cache->setincache("getInstances",array($idclass,$indirect),$return);
		return $return;
	}


	function getTopClasses()
		{
		/*if ($this->cache->isincache("getTopClasses",array()))
			{
				$cache_answer = $this->cache->getincache("getTopClasses",array());

				return $cache_answer;
			}*/

		$allmetaclasses=$this->getAllMetaClasses();
		$inallmetaclasses="('http://www.w3.org/2000/01/rdf-schema#Class'";
		foreach ($allmetaclasses as $key=>$val)
			{
			$inallmetaclasses.=",'".$val."'";
			}
		$inallmetaclasses.=")";
		$dockey=$this->modelID;
		//$topclasses=array();$this->con->debug=true;
		$sqlresult = $this->con->execute("select subject from statements where
		(
			predicate = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' AND modelid in (".$dockey.") and object in ".$inallmetaclasses." AND subject NOT
			IN (
			SELECT subject
			FROM statements
			WHERE predicate = 'http://www.w3.org/2000/01/rdf-schema#subClassOf'
			AND modelid in (".$dockey.")
			".$this->filter["read"]."
			)
		)

		or
		(

			subject not in
			(
			select subject from statements where (predicate= 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' or predicate= 'http://www.w3.org/2000/01/rdf-schema#subClassOf') and modelID in (".$dockey.")
			)
			and
			subject in (select object from statements where predicate= 'http://www.w3.org/2000/01/rdf-schema#subClassOf' and modelID in (".$dockey.") )

		)
		".$this->filter["read"]);


		/*AND object like '".$this->modelURI."%'*/
		 while (!$sqlresult-> EOF)
           {
				$topclasses[]= $sqlresult->fields[0];
				$sqlresult-> MoveNext();
		   }
		//$this->cache->setincache("getTopClasses",array(),$topclasses);
		return $topclasses;
		}
	function isProperty($ressource)
		{

		$URIressource =$this->modelURI.$ressource;
		$dockey=$this->modelID;
		return in_array("http://www.w3.org/1999/02/22-rdf-syntax-ns#Property",$this->getType($ressource));
		}
	function isClass($ressource)
		{

		$URIressource =$this->modelURI.$ressource;
		$dockey=$this->modelID;
		$typeOf = $this->getType($ressource);
		return $typeOf;
		}
	function isAClass($ressource)
		{
			$URIressource =$this->modelURI.$ressource;
			$dockey=$this->modelID;
			return in_array("http://www.w3.org/2000/01/rdf-schema#Class",$this->getType($ressource));
		}

	function isKnownModel($uri)
		{
			if ($uri=="") {$uri="#";}
			if (strpos($uri,"#")===0)
			{
				$URIClass =$this->modelURI.$uri;
			}
			else
			{
				if (strpos($uri,"http://")===0)
				{
				$URIClass=$uri;
				}
			}
			$modelURI = substr($URIClass,0,strpos($URIClass,"#"));


			if ((in_array($modelURI,$this->modelURIs)) OR (in_array($modelURI."#",$this->modelURIs)))
			{return true;} else return false;



		}
	function isMetaClass($ressource)
		{

		$URIressource =$this->modelURI.$ressource;
		$dockey=$this->modelID;
		//TODO
		return false;
		}
	function getTopMetaClasses()
		{

		return Array("http://www.w3.org/2000/01/rdf-schema#Class");
		/*
		$dockey=$this->modelID;
		$topclasses=array();
		//predicate = 'http://www.w3.org/2000/01/rdf-schema#subClassOf'
		//	AND modelid in (".$dockey.")
		//	AND object = 'http://www.w3.org/2000/01/rdf-schema#Class'
		//	OR/
		$sqlresult = $this->con->execute("SELECT distinct subject
			FROM statements
			WHERE
			 subject ='http://www.w3.org/2000/01/rdf-schema#Class'");

		 while (!$sqlresult-> EOF)
           {
				$topclasses[]= $sqlresult->fields[0];
				$sqlresult-> MoveNext();
		   }

		return $topclasses;
		*/
		}
	function getAllMetaClasses()
		{

		$dockey=$this->modelID;
		$AllMetaClasses=array();
		/*predicate = 'http://www.w3.org/2000/01/rdf-schema#subClassOf'
			AND modelid in (".$dockey.")
			AND object = 'http://www.w3.org/2000/01/rdf-schema#Class'
			OR*/
		$sqlresult = $this->con->execute("SELECT distinct subject
			FROM statements
			WHERE
			 predicate ='http://www.w3.org/2000/01/rdf-schema#subClassOf'
				and
			 object ='http://www.w3.org/2000/01/rdf-schema#Class'".$this->filter["read"]);

		 while (!$sqlresult-> EOF)
           {
				$AllMetaClasses[]= $sqlresult->fields[0];
				$AllMetaClasses=array_merge($AllMetaClasses,$this->getindirectsubClassesId($sqlresult->fields[0]));
				$sqlresult-> MoveNext();
		   }
		return array_unique($AllMetaClasses);
		}
	function getindirectsubClassesId($idclass)
	{

		$dockey=$this->modelID;
		$sqlresult =$this->con->execute("SELECT subject
				FROM statements
				WHERE object = '".$idclass ."'
					AND modelid in (".$dockey.")
				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#subClassOf'".$this->filter["read"]);


		$classesList=array();
		 while (!$sqlresult-> EOF)
           {
				$classesList[]=$sqlresult->fields[0];
				$classesList = array_merge($classesList,$this->getIndirectsubClassesId($sqlresult->fields[0]));
				$sqlresult-> MoveNext();
		   }
		   return $classesList;
	}
	/*$idClass complete URI, key, label, comment returned*/
	function getIndirectsubClasses($idclass)
	{
		$idclass=$this->URI($idclass,"c");
		$dockey=$this->modelID;
		$sqlresult =$this->con->execute("SELECT subject
				FROM statements
				WHERE object = '".$idclass ."'
					AND modelid in (".$dockey.")
				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#subClassOf'".$this->filter["read"]);


		$classesList=array();
		 while (!$sqlresult-> EOF)
           {


				$sqlresultlabel =$this->con->execute("SELECT object, l_language
				FROM statements
				WHERE subject = '".$sqlresult->fields[0]."'
				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#label'

					AND modelid in (".$dockey.")
				".$this->filter["read"]);
				$labels = $this->sortSQLResultbyLG($sqlresultlabel);

				$pdescription =  array("PropertyKey" => str_replace($this->modelURI,"",$sqlresult->fields[0]), "PropertyLabel" => $labels[0]);
				$classesList[]=$pdescription;
				$classesList = array_merge($classesList,$this->getIndirectsubClasses($sqlresult->fields[0]));
				$sqlresult-> MoveNext();
		   }
		   return $classesList;
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
function getAllClasses($idclass)
	{


		$URIClass=$this->URI($idclass[0],"c");

		$dockey=$this->modelID;

		$topClasses = $this->getTopClasses();

			foreach ($topClasses as $key=>$val)
           {

			   	$sqlresultlabel = $this->con->execute("SELECT object, l_language
				from statements
				WHERE subject = '".$val."'
				AND modelid in (".$dockey.")

				AND predicate = 'http://www.w3.org/2000/01/rdf-schema#label'
				".$this->filter["read"]."
				limit 1");

				$labels = $this->sortSQLResultbyLG($sqlresultlabel);

				$classID=$val;
				//FIXME should use ClassKey insteadof PropertyKey , $idClass not used at all.
				$pdescription =  array("PropertyKey" => str_replace($this->modelURI,"",$val), "PropertyLabel" => $labels[0]);

				$properties[]= $pdescription;
				$properties=array_merge($properties,$this->getIndirectsubClasses($classID));

			}



		return array("pDescription" => $properties);


	}
/*$idClass complete URI*/
function getindirectSuperClasses($URIClass)
{
	if ($URIClass == "http://www.w3.org/2000/01/rdf-schema#Class") return array();
	if ($URIClass == "http://www.w3.org/2000/01/rdf-schema#Resource") return array();
	$dockey=$this->modelID;
				$sqlresult =$this->con->execute("SELECT object
				FROM statements
				WHERE subject = '".$URIClass ."'
					AND modelid in (".$dockey.")
				AND (predicate = 'http://www.w3.org/2000/01/rdf-schema#subClassOf'
				OR predicate = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type')
				".$this->filter["read"]);


		/*echo "SELECT object
				FROM statements
				WHERE subject = '".$URIClass ."'
					AND modelid in (".$dockey.")
				AND (predicate = 'http://www.w3.org/2000/01/rdf-schema#subClassOf'
				OR predicate = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type'
				)".$this->filter["read"];*/
		$classesList=array();
		 while (!$sqlresult-> EOF)
           {
				$classesList[]=str_replace($this->modelURI,"",$sqlresult->fields[0]);
				foreach($this->getindirectSuperClasses($sqlresult->fields[0]) as $key=>$val){
				 $classesList[]=$val;
				 }
				$sqlresult-> MoveNext();
		   }
		   return $classesList;
	}

function sortSQLResultbyLG($sqlresultvalues,$uri="")
	{

		$result= array("datalg"=>array(),"deflg"=>array(),"any"=>array(),"ressource"=>array());

		while (($sqlresultvalues->EOF)===false)
		{
			if (isset($sqlresultvalues->fields[2]))
			{
			$tripleID = $sqlresultvalues->fields[2];

			switch ($sqlresultvalues->fields[1])
				{
			case $this->modelManager->lg: $result["datalg"][$tripleID]=$sqlresultvalues->fields[0];break;
			case $this->modelManager->deflg: $result["deflg"][$tripleID]=$sqlresultvalues->fields[0];break;
			case "": $result["ressource"][$tripleID]=$sqlresultvalues->fields[0];break;
			default: $result["any"][$sqlresultvalues->fields[1]][$tripleID] =$sqlresultvalues->fields[0];
				$any = $sqlresultvalues->fields[1];
				break;
				}
			}
			else
			{
			switch ($sqlresultvalues->fields[1])
				{
			case $this->modelManager->lg: $result["datalg"][]=$sqlresultvalues->fields[0];break;
			case $this->modelManager->deflg: $result["deflg"][]=$sqlresultvalues->fields[0];break;
			case "": $result["ressource"][]=$sqlresultvalues->fields[0];break;
			default: $result["any"][$sqlresultvalues->fields[1]][] =$sqlresultvalues->fields[0];
				$any = $sqlresultvalues->fields[1];
				break;
				}
			}

			$sqlresultvalues->MoveNext();
		}



		if (sizeOf($result["datalg"])>0)
			{


				return $result["datalg"];
			}
		if (sizeOf($result["deflg"])>0)
			{

				return $result["deflg"];
			}
		if (sizeOf($result["ressource"])>0)
			{
				return $result["ressource"];
			}
		if (sizeOf($result["any"])>0)
			{
				return $result["any"][$any];
			}
		return array(substr($uri,strpos($uri, "#")+1));
	}

/**
 * Retrieve the subject that have given predicate and object
 *
 * @param String $predicate
 * @param String $object
 */
function getTripleSubject($predicate,$obj) {
	$dockey=$this->modelID;
//	$this->con->debug=true;
	$sqlResult = $this->con->GetAll("
				SELECT 	subject
				FROM 	statements
				WHERE 	predicate = '$predicate'
				AND 	object = '$obj'
				AND		modelid in (".$dockey.")"
				.$this->filter["read"]);

	return $sqlResult;
}


/**
*Returns value (litteral or ressource) of a property about an instance
*@param TAO:session $pSession returned by authenticate service
*@param Array([0] => String) $instance : 14 (without #i) id of instance
*@param Array([0] => String) $propertyName : 14 (without #i) id of property
*@param Array([0] => String) $remotedocKey : is optional. It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule"
*@access public
**/
function GetInstancePropertyValues($instance, $property)
	{
		
		$dockey=$this->modelID;

		$URIInstance=$this->URI($instance[0],"i");

		$URIProperty=$this->URI($property[0],"p");

//$this->con->debug=true;
		$sqlresult =$this->con->execute("SELECT object,l_language
				FROM statements
				WHERE subject = '".$URIInstance ."'
					AND modelid in (".$dockey.")

				AND predicate = '".$URIProperty ."'".$this->filter["read"]);
		if ($sqlresult->RecordCount() == 0 )
		return array();
		else
		return ($this->sortSQLResultbyLG($sqlresult));

		
	}


function GetInstancePropertyLgs($instance, $property)
{
		$dockey=$this->modelID;

		$URIInstance=$this->URI($instance[0],"c");

		$URIProperty=$this->URI($property[0],"c");


		$sqlresult =$this->con->execute("SELECT distinct (l_language)
				FROM statements
				WHERE subject = '".$URIInstance ."'
				AND modelid in (".$dockey.")
				AND predicate = '".$URIProperty ."'".$this->filter["read"]);


		$valuesList=array();
		 while (!$sqlresult-> EOF)
           {

				 $valuesList[]= $sqlresult->fields[0];
				$sqlresult-> MoveNext();
		   }
		 return  $valuesList;
	}

	function GetLgs()
{
		$dockey=$this->modelID;

		$sqlresult =$this->con->execute("SELECT distinct (l_language)
				FROM statements
				where modelid in (".$dockey.") ");


		$valuesList=array();
		 while (!$sqlresult-> EOF)
           {

				 $valuesList[]= $sqlresult->fields[0];
				$sqlresult-> MoveNext();
		   }
		 return  $valuesList;
	}






} //end of class
?>

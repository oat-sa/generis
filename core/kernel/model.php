<?php
/**
* 
* @package generis
* @version 1.1
*/

class generisModel
{
  var $modelURI; //URI of user model (only this model may be changed)
  
  var $modelID;	// Ids List of loaded models
  var $modelURIs=array();// URIs List of loaded models
  var $modelManager;
  var $readrights;
  var $writerights;
  var $con;
  var $filter=Array();

  /**
  * Constructor
  */
  function model()
  {
  }
   function setDatabase($con)
  {
	   $this->con=$con;
  }

  /**
  * set User model URI and ID
  */
   function setModelURI($modelURI)
  {
	    $this->modelURI=$modelURI;
		$sqlResult = $this->con->Execute("SELECT modelID,baseURI FROM models where modelURI='".$modelURI."'");
		
		$this->modelID=$sqlResult->fields[0];
		$this->modelURIs[]=$modelURI;
		
		
		//$this->modelURI=substr($sqlResult->fields[1],0,strlen($sqlResult->fields[1])-1);
  }
   function getUserModelId()
  {
	    
		$sqlResult = $this->con->Execute("SELECT modelID FROM models where modelURI='".$this->modelURI."'");
		return $sqlResult->fields[0];
		
		
  }

  function loadModel($modelURI)
  {
//	    $this->con->debug=true; 
		//print_r($this->con);
		$sqlResult = $this->con->Execute("SELECT modelID FROM models where modelURI LIKE '".$modelURI."%'");
//		 $this->con->debug=false;
		if ($sqlResult->fields[0]!="")
		  {
			$this->modelID.=",".$sqlResult->fields[0];
			$this->modelURIs[$modelURI]=$modelURI;
		  }
		
		
		
		//$this->modelURI=substr($sqlResult->fields[1],0,strlen($sqlResult->fields[1])-1);
  }
function issetModel($modelURI)
  {
	     
		$sqlResult = $this->con->Execute("SELECT modelID FROM models where modelURI='".$modelURI."'");
		
		while (!$sqlResult-> EOF)
		{return true;}
		
		return false;
		
		
  }
function microtime_float() 
	{ 
    list($usec, $sec) = explode(" ", microtime());
    return (str_replace(".","",$sec."".$usec));
	}


} //end of class
?>

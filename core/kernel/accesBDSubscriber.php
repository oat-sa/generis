<?php
/**
* Class to implement all queries with database
* @author patrick
* @access public
* @package kernel
*/
class accesBDsubscriber
{
  var $con;

  /**
  * Constructor
  */
  function accesBDsubscriber()
  {
  }
  
  /**
  * Connection to database
  * @return boolean
  * @access public
  */
  function connection($modulename)
	{
	 $this->con = mysql_connect(DATABASE_URL,DATABASE_LOGIN,DATABASE_PASS);
	mysql_select_db($modulename,$this->con) 
		or mysql_select_db(strtolower($modulename),$this->con)
		or die("Database not found ! ");
	}
  function disconnection()
	{ mysql_close($this->con);}
 

 
 function getGroupsubscriber($loginsubscriber)
	{
	   $query="Select ismember from subscriber where Login='".$loginsubscriber."'";
	   $result = mysql_query($query);
	   while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
		{
	   $i=0;
	   return $row[$i];
		  
		}
	}
 /**
  * XML RDF Generation from database
  * For a user or an admin
  * @access public
  */
 function rewriteXMLRDFDataforsubscriber($loginsubscriber)
	{
		set_time_limit(250);
		$idproperties = array(); /*Bag of Id of properties which may be listed*/
		$subscribergroup = $this->getGroupsubscriber($loginsubscriber);
		$query =
	
	"SELECT distinct C.rdf FROM Class as C,accessgroupclass as AGC
	WHERE
		C.enabled = '1'
	AND
		(AGC.View = '1' OR AGC.View = '2')
	AND 
		AGC.IDClass=C.Id
	AND 
		AGC.IdSubscribersgroup = '".$subscribergroup."'";
			
		$classes = mysql_query($query) or die("Error: Error with getCLasses " . mysql_error());
		


		$query ="SELECT distinct C.rdf FROM Property as C,accessgroupproperty as AGC
	WHERE
		C.enabled = '1'
	AND
		(AGC.View = '1' OR AGC.View = '2')
	AND 
		AGC.IDproperty=C.Id
	AND 
		AGC.IdSubscribersgroup = '".$subscribergroup."'";
		
		$properties = mysql_query($query) or die("Error: Cannot create RDF file. " . mysql_error());

			


	$query ="SELECT distinct C.rdf,C.id FROM Instance as C,accessgroupinstance as AGC
	WHERE
		C.enabled = '1'
	AND
		(AGC.View = '1' OR AGC.View = '2')
	AND 
		AGC.IDinstance=C.Id
	AND 
		AGC.IdSubscribersgroup = '".$subscribergroup."'";
		
	$instances = mysql_query($query) or die("Error: Cannot create RDF file. " . mysql_error());
	
	$text = RDF_DOC_BEGINNING;
	
	$text .='xmlns:module="'.$this->getNamespace().'#">' ;

	     
	while ($line = mysql_fetch_array($classes))
		{$text .= $line[0]."
				
				"; } 
   while ($line = mysql_fetch_array($properties))
		{$text .= $line[0]."
				
				";}				  
   /*Get properties which can be listed (values)*/
	
	$query ="SELECT distinct property.Id FROM property,accessgroupproperty 
	WHERE
		property.enabled = '1'
	AND
		(
		accessgroupproperty.View = '2')
	AND 
		accessgroupproperty.IDproperty=property.Id
	AND 
		accessgroupproperty.IdSubscribersgroup = '".$subscribergroup."'";

		
				$result = mysql_query($query);
				$Listofproperties=array();
				while ($rows = mysql_fetch_array($result))
				{$Listofproperties[]=$rows[0];}
  
   while ($line = mysql_fetch_array($instances))
		{  
	   
			/*Retrieve instance description before values*/
				$text .= $line[0];
				
				
				foreach ($Listofproperties as $key=>$val)
				{
				$query = "SELECT rdf FROM ClassInstance 
				WHERE
				IdInstance  = '".$line[1]."'
				AND
				IdProperty = '".$val."'";
				
				$rdfthatcanbeseen = mysql_query($query) or die("Error: Cannot retrieve data values for properties" . mysql_error());
					while ($rows = mysql_fetch_array($rdfthatcanbeseen))
					{$text .= $rows[0]."
					
					";
					}
				
				}
				
				$text .= "</rdf:Description>";
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
function getLongObject($blobID)
	{
	$querye="select Object from longObjects WHERE IDObject='".$blobID."'";
	
	$result=mysql_query($querye);
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
		{
	     $xml=$row[0];
		 $xml =ereg_replace("--MULTIMEDIA[^-]*--" , "" , $xml ) ;
		 $xml =ereg_replace("--TEXTBOX[^-]*--" , "" , $xml ) ;
		 return $xml;
		}
	}
function getNamespace()
	{
	   $query="Select value from settings where key = 'NameSpace'";
	   $result = mysql_query($query);
	   while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
		{
	   $i=0;
	   return $row[$i];
		}
	}

function authenticatesubscriber($login,$password)
	{
	   $query="Select password,id from subscriber where login='".$login."'";
	   $result = mysql_query($query);
	   
	   while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
		{
	   $i=0;
	   if   ($password == $row[$i])
		   {return $login;} else {return "";}
		}
	}
			
} //end of class
/*
require_once("./config.php");
$x = new accesBDsubscriber();
$x->connection("schandeleritems");
$x->rewriteXMLRDFDataforsubscriber("LOG");
*/
?>
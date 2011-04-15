<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* Performs a search on knowledge base
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");		   
//include("serverModule.php");	
require_once("generis_utils.php");


/**
* This function takes posted criteria in Search form and returns the associated sql where clause
*/
function genSQL($POST_CRITERIA,$FULLTEXT=false)
{	$moduleURI = calltoKernel('getNs',array($_SESSION["session"]));
	
	if (sizeOf($POST_CRITERIA)>1) {$needimbricatedqueries = true; $first=true; }
	$first=false;


	$sqlwhereclause="";
	if (!($FULLTEXT))
		{
				foreach ($POST_CRITERIA as $property=>$values)
				{
					
						foreach ($values as $key=>$val)
						{
						$val = trim(strip_tags($val));
						/*$val = str_replace("
						","",$val);
						$val = str_replace('\n',"",$val);
						$val = str_replace('\r',"",$val);
							$val = str_replace('\n\r',"",$val);
							$val = ereg_replace("
				","",$val);*/
						if (($val!="") and ($val!="NULL") and  ($val!=" ") and  ($val!="&nbsp;"))
							{
								if (strpos(urldecode($val),"#")===0)
								{	
								$val =$moduleURI.$val;
								}
								if ($needimbricatedqueries and (!($first))) 
								{
									$sqlwhereclause.="AND subject in (select subject from statements where predicate='".$property."' AND object LIKE '%".urldecode($val)."%')";
								}
								else 
								{
									$sqlwhereclause.="AND predicate='".$property."' AND object LIKE '%".urldecode($val)."%' 
																";
								}
							}
						}
					
				}
		}
		else
		{	
			$sqlwhereclause.="AND (predicate='http://www.w3.org/2000/01/rdf-schema#label' OR predicate='http://www.w3.org/2000/01/rdf-schema#comment') AND object LIKE '%".$POST_CRITERIA."%'";
								
			
		}
	return $sqlwhereclause;

}
/**
*Return html table with results
*/
function genTAb($rows,$fulltext=false,$criteria)
{
	include_once('../../common/ext/loader/extension.php');
	include_once('../../common/common.php');
	$ext = extension::getExtension();
	$log = $ext->loadExtension(EXTENSION);
	$_SESSION["ext"]=$ext;
	
	$unique=Array();
	$moduletype = calltoKernel('getTypeModule',array($_SESSION["session"]));
	$output='';
	
	if ($moduletype=="http://www.tao.lu/Ontologies/TAOResult.rdf#") 
		{$output.="<FORM action=".$_SESSION["ext"]->httpLocation.$_SESSION["ext"]->plugins."TLAResults/index.php name=newressource id=newressource method=post>";}

	
	$output.=HEAD.'<body class=paneIframe><div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div><script language="JavaScript" src="./JS/overlib.js"><!-- overLIB (c) Erik Bosrup --></script><UL>';
	//$rows=array_unique($rows);
	foreach ($rows as $key=>$val)
	{	

		
		if (!(in_array($val[0],$unique)))
		{
			$unique[]=$val[0];
				$labelComment = calltoKernel('getLabelComment',array($_SESSION["session"],$val[0],array("")));
				
				
				if ($fulltext)
					{
				$labelComment["label"] = str_replace($criteria,"<span class=highlight>".$criteria."</span>",$labelComment["label"]);
				
				$labelComment["comment"] = str_replace($criteria,"<span class=highlight>".$criteria."</span>",$labelComment["comment"]);

					}
				
				$overdivlink=getOverDivLink("./generis_UiControllerHtml.php?do=show&param1=".urlencode($val[0])."&type=im",$labelComment["label"],$labelComment["comment"]);
				$output.="<LI><input type=checkbox  name=external[] value=".urlencode($val[0]).">&nbsp;&nbsp;&nbsp;".$overdivlink;
		}
	}
	$output.="</UL>";
	if ($moduletype=="http://www.tao.lu/Ontologies/TAOResult.rdf#") 
		{$output.="<br><br><div align=right><input type=submit value=Selection></div></FORM>
	
	<SCRIPT LANGUAGE=\"JavaScript\">

function checkAll(field)
{
for (i = 0; i < field.length; i++)
	field[i].checked = true ;
}

function uncheckAll(field)
{
for (i = 0; i < field.length; i++)
	field[i].checked = false ;
}
form = document.forms[0];
	checkboxes = form.elements[\"external[]\"];

</script>
	
	<input type=\"button\" name=\"CheckAll\" value=\"Check All\"
onClick=\"checkAll(checkboxes)\">
<input type=\"button\" name=\"UnCheckAll\" value=\"Uncheck All\"
onClick=\"uncheckAll(checkboxes)\">
	
	";}
	return $output;
}
				
				
if (!(isset($_SESSION))) {session_start();}
//$essai = calltoKernel('search',array($_SESSION["session"],array("#label","Demo"),array(""),true));
//print_r($essai);
$ressource=$_POST["instanceCreation"];


if (!(isset($_POST["fulltext"]))) {$fulltext=false;} else {$fulltext=true;}
$sqlwhereclause = genSQL($ressource["properties"],$fulltext);

$rows = calltokernel('execSQL', array($_SESSION["session"],$sqlwhereclause,array("")));
//$rows=array_unique($rows);
echo genTAb($rows,$fulltext,$ressource["properties"]);


unset($_SESSION["getResults"]);
unset($_SESSION["exactmatch"]);
unset($_SESSION["show"]);

/*
class TAOsearch
{
	function TAOsearch()
	{
	}
	
	function getOutput($ressource)
	{
		
			$type=array();
			$essai=$ressource['type'];
			$type = array_map("modifyType",$essai);
			
			$criteria=array();
			if (isset($ressource['properties']))
		{
			foreach ($ressource['properties'] as $key => $val)
			{		
					$pid = substr($key,2);
					if (!(is_array($val)))
					{
						if (!((strpos($val,"~"))===FALSE))
								{$val=substr($val,strpos($val,"~")+1);}
						
						if (($val!="") && ($val!="???"))
						{
						$criteria[]=$key;
						$criteria[]=$val;
						}
					}

					else
					{
						foreach ($val as $key2 => $val2)
						{
							if (!((strpos($val2,"~"))===FALSE))
								{$val2=substr($val2,strpos($val2,"~")+1);}
						if ($val!="")
						{
						$criteria[]=$key;
						$criteria[]=$val;
						}	
						}

					}
			}
		}
		
		$criteria[]="#type";
		$criteria[]=$ressource["type"][0];
		if ($ressource["label"]!="")
						{
						$criteria[]="#label";
						$criteria[]=$ressource["label"];
						}
		if ($ressource["comment"]!="")
						{
						$criteria[]="#comment";
						$criteria[]=$ressource["comment"];
						}
		
		$exactmatch = $_POST["instanceCreation"]["exactmatch"];
		$bool=false;
		if ($exactmatch) 
			
		{
			
		if ($exactmatch=="1") {$bool=TRUE;} else {$bool=FALSE;}
		
		}
		$ns = $ressource['ns'];
		
		//echo $ns;
		
					
		//refresh data in all languages 			
		calltoKernel('refreshXMLRDFDataforuser',array($_SESSION["session"],$_SESSION["datalg"],true));
		set_time_limit(800);			
		$result = calltoKernel('search',array($_SESSION["session"],$criteria,array($ns),$bool));
//print_r(array('search',array($_SESSION["session"],$criteria,array($ns),$bool)));
		
		$_SESSION["showresults"]=$result;
		
		
				$ressource=$_SESSION["showresults"];
				unset($_SESSION["showresults"]);
				$TAOshowresultsGUI=new TAOshowresultsGUI();
				$TAOshowresultsOutput=$TAOshowresultsGUI->getOutput($ressource,$ns);
				$output.=$TAOshowresultsOutput;
		echo HEAD.$output;
	
	
	}
	   
	 
	 
	


}
*/
				
?>
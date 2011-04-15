<?php 

/**
* XMl rdf import within the active module
*/
//The session contanins the active session with the kernle, used to call generis API
if (!(isset($_SESSION))) {session_start();}

include("generis_ConstantsOfGui.php");
include("generis_utils.php");


//Simple case , the imported file is given using a URL, generis import function will handle file downloading on its own
if (isset($_POST["url"]))
{
	$dlmodel = calltoKernel('importrdfs',array($_SESSION["session"],$_POST["url"],$_POST["url"],true));
	$_SESSION["session"]=$dlmodel["pSession"];
}

//In other case retrieve uploaded files and retrieve the content
foreach ($_FILES as $keyuz=>$valuz){
	move_uploaded_file($valuz["tmp_name"],"../../Ontologies/".$valuz["name"]);
	$xmlRdfData = file_get_contents("../../Ontologies/".$valuz["name"]);
	//Retrieve the namepsace of the current module, all resources in the xml rdf file will get the local namespace of the active module
	$namespace = calltoKernel('getNs',array($_SESSION["session"]));
	
	//Retrieve the namespace used tod escribe resources in the provided xml rdf file
	$bases=array();
	ereg('xml:base="[^"]*"',$xmlRdfData,$bases);
	

	$toreplace = substr($bases[0],10,strlen($bases[0])-12);
		echo "<br>";
	$xmlRdfData = str_replace($toreplace,$namespace,$xmlRdfData);


	$ressources=array();
	preg_match_all('| ID="[^"]*"|U',$xmlRdfData,$ressources);
	$ressources[0]=array_unique($ressources[0]);

	foreach ($ressources[0] as $k=>$v)
		{
			
			$str = substr($v,5,strlen($v)-6);
			
			$xmlRdfData = str_replace("\"".$str."\"","\"imp".$str."\"",$xmlRdfData);
			$xmlRdfData = str_replace(":".$str." ",":imp".$str." ",$xmlRdfData);
			$xmlRdfData = str_replace(":".$str.">",":imp".$str.">",$xmlRdfData);
			$xmlRdfData = str_replace("\"#".$str."\"","\"#imp".$str."\"",$xmlRdfData);
			$xmlRdfData = str_replace("<![CDATA[#".$str."]]>","#imp".$str."",$xmlRdfData);
			
		}


error_reporting(E_ALL);
$x = fopen("../../Ontologies/".$valuz["name"],"wb");
fwrite($x,trim($xmlRdfData));
fclose($x);

echo "../../Ontologies/".$valuz["name"];
if ((isset($valuz["name"])) and ($valuz["name"]!=""))
	{
$dlmodel = calltoKernel('importrdfs',array($_SESSION["session"],"../../Ontologies/".$valuz["name"],"../../Ontologies/".$valuz["name"],true));
$_SESSION["session"]=$dlmodel["pSession"];
	}

}
if (isset($_POST["MAX_FILE_SIZE"]))
{
echo "<body onLoad=\"top.location='index.php'\"></body>";die();
}

echo HEAD."<body class=paneIframe><br><br><br><form name=importfile enctype=\"multipart/form-data\" action=generis_hardimportfile.php method=post><span class=leftmargin><br><br>

".TABLEHEADER;

?>
<tr>
	<td colspan=2>The import function will rewrite all ressources related to xml:base<br> namespace with the local namespace</td></tr>
<tr><td><input type=hidden name=MAX_FILE_SIZE value=99999999>Select Content of file
</td><td>
<input type="file" name=problem></td></tr>
<tr>
<tr><td></td><td>
<input type=submit>
</td></tr></table></form>
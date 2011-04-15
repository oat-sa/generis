<?php 
include("generis_ConstantsOfGui.php");
include("generis_utils.php");
if (!(isset($_SESSION))) {session_start();}
if ((isset($_POST["url"])) and ($_POST["url"]!=""))
{

$dlmodel = calltoKernel('importrdfs',array($_SESSION["session"],$_POST["url"],$_POST["url"]));
$_SESSION["session"]=$dlmodel["pSession"];
}

foreach ($_FILES as $keyuz=>$valuz){
move_uploaded_file($valuz["tmp_name"],"../ontology/".$valuz["name"]);
if ((isset($valuz["name"])) and ($valuz["name"]!=""))
	{
	
$dlmodel = calltoKernel('importrdfs',array($_SESSION["session"],"../ontology/".$valuz["name"],"../ontology/".$valuz["name"]));
$_SESSION["session"]=$dlmodel["pSession"];
	}

}
if (isset($_POST["MAX_FILE_SIZE"]))
{
//echo "<body onLoad=\"top.location='index.php'\"></body>";die();
}

echo HEAD."<body class=paneIframe><br><br><br><form name=importfile enctype=\"multipart/form-data\" action=generis_importfile.php method=post><span class=leftmargin>".TABLEHEADER;

?>



<tr><td>

<input type=hidden name=MAX_FILE_SIZE value=1000000>Select Content of file
</td><td>
<input type="file" name=problem></td></tr>

<tr>
<tr>
<td>

Or enter Url :
</td><td>

<input type=text name=url size=45 value="http://www.w3.org/2002/07/owl#">

</td></tr>

<tr><td></td><td>
<input type=submit>
</td></tr></table></form>
<?php



?>

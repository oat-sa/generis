<?php
/**
*@package plugins.hypergraph
*/


include_once("../../core/view/generis_utils.php");   

if (!(isset($_SESSION))) {session_start();}
//print_r($_SESSION);
error_reporting(0);
$graphxml = '<?xml version="1.0"?>

<!DOCTYPE GraphXML SYSTEM "GraphXML.dtd">

<GraphXML>

	<graph id="My First Graph">';

		$tree= calltoKernel('getHTMLTree',array($_SESSION["session"],array("instances"=>true,"properties"=>true),"",true));
			
$graphxml.=$tree["pXMLTree"][0];


$graphxml.='	</graph>

</GraphXML>';
$handle=fopen("tot.xml","wb");
fwrite($handle,$graphxml);
fclose($handle);
$jarjarbin="http://".$_SERVER["HTTP_HOST"]."/middleware/taoplugins/hypergraph/hyper.jar";
//echo $jarjarbin;

echo '<HTML>
<applet archive="hyperapplet.jar" code="hypergraph.applications.hexplorer.HExplorerApplet.class" align="baseline"  width="100%" height="100%" >
<param name = "cache_archive" value = "hyperapplet.jar">
<param name="file" value="tot.xml" >

</applet >

</HTML>';


?>

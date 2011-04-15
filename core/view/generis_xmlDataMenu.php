<?php
if (!(isset($_SESSION))) {session_start();}
header("Content-Type: text/xml");




//include generis API
include_once("generis_utils.php");

/**
* Load existing plugins using generis extension loaded
*/
function loadPlugins()
{	
	$plugins =array(
	"hypergraph" => "HyperGraph",
	"requetes" => "Requetes"
	
	);
	
	return $plugins;
}

loadGUIlanguage();

//Retrieve enabled plugins for this module
$moduleModel = calltoKernel('getTypeModule',array($_SESSION["session"]));

//$plugins = calltoKernel('GetInstancePropertyValues',array($_SESSION["session"],array(substr($moduleModel,0,strlen($moduleModel)-1)),array("http://www.tao.lu/Ontologies/generis.rdf#Plugin"),array("")));

$plugins = loadPlugins();


//print_r($_SESSION);
echo "<?xml version='1.0'  ?>";
echo '<menu absolutePosition="auto" mode="classic" maxItems="50" xname="" mixedImages="yes" type="a1">
	<MenuItem name="'.FILE.'" id="main_file" width="100px" withoutImages="true">
			<MenuItem name="'.LOAD.'"  id="loadfile"/>
			<MenuItem name="'.IMPORT.'"  id="importfile"/>
			<MenuItem name="'.EXPORT.'"  id="exportfile"/>
			<divider id="div_2"/>
			<MenuItem name="'.LOADUNLOADMODELS.'" id="loadM"/>
			<divider id="div_2"/>
			<MenuItem name="'.LOGOFF.'" id="logoff" href="../../portal/generisPortal.php"/>
			
	</MenuItem>
	<MenuItem name="'.RESSOURCE.'" absolutePosition="yes" menuAlign="center" id="Ressource"  left="300px" width="100px" panelWidth="200px" withoutImages="true">	
			
			
			
			
			<MenuItem name="'.SEARCH.'" id="search" withoutImages="true">
				<MenuItem name="'.FULLTEXT.'" id="SearchText"/>
				<MenuItem name="'.ADVSEARCH.'" id="SearchStruct"/>
				</MenuItem>
			<MenuItem name="'.DISCUSS.'" id="discuss"/>
			
			
	</MenuItem>
	<MenuItem name="'.PLUGINS.'" id="plugins"  width="50px" withoutImages="true">';
	
	
	foreach ($plugins as $k=>$v)
	{
		echo '<MenuItem name="'.$v.'"  id="'.$v.'" href="./generis_plugins/'.$v.'/" target="workpane"/>';
	}			
	?>
	</MenuItem>
</menu>
<?php

?>

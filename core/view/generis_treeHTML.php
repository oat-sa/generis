<?php
/**
 * @author ppl
 * @package usergui
 */

if (!(isset($_SESSION))) {
	session_start();
}

include_once(dirname(__FILE__).'/generis_ConstantsOfGui.php');
include_once(dirname(__FILE__).'/../../common/common.php');
include_once(dirname(__FILE__).'/generis_kernelController.php');
include_once(dirname(__FILE__).'/generis_tree.php');


/**************************************************************************************************/
//work in progress inclusion of the new tree
error_reporting(E_ALL);


if (!(isset($_SESSION["generis_admin"])))
	{
		
	//Retreving Tree using old api
//	$factory = TreeFactory::singleton();
//	$factory->setSessions($_SESSION["session"]);
//	$tree2 = $factory->getTree();
//	$ajaxTree2 = new OldAjaxTree(false);
//	$treeOutput = $ajaxTree2->getAjaxTree($tree2);

	//Retreving Tree using new api
//	$api =  core_kernel_impl_ApiModelOO::singleton();
//	$api->logIn($_SESSION["cuser"],md5($_SESSION["pass"]),$_SESSION["bd"],true);
//	$tree = $api->getResourceTree("http://www.tao.lu/Ontologies/generis.rdf#generis_Ressource",3);
//	$ajaxTree = new core_view_classes_AjaxTree(false);
	
	//Different AjaxTree for new api (still working... stuff missing in API to be fully functionnal)
//	$treeOutput = $ajaxTree->getAjaxTree($tree);

	//old TREE for PIAAC
	$generisTree = new TAOTree();
	$treeOutput = $generisTree->getOutput(FALSE,"","");

	
	$oldInclude = '
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
		<script language="JavaScript" src="./JS/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
		<script language="JavaScript" src="./JS/tree.js"></script>
		<script language="JavaScript" src="./JS/tree_items.js"></script>
		<script language="JavaScript" src="./JS/tree_tpl.js"></script>
		<script language="JavaScript">var selected=[];var idproperty=\'\';var urlicons=\'\'</script>';

	
	echo HEAD.'<body class=treeIframe>'.$oldInclude;
	echo $treeOutput;
	echo '</body></html>';
	}
	else
	{
		error_reporting(E_ALL);
		include_once("generis_Admin.php");
		$generisadmin = new generis_Admin();
		$treeOutput=$generisadmin->getOutput($_SESSION["generis_admin"]);

		echo HEAD.'
		<body class=treeIframe>
		<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
		<script language="JavaScript" src="./JS/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
		<script language="JavaScript" src="./JS/tree.js"></script>
		<script language="JavaScript" src="./JS/tree_items.js"></script>
		<script language="JavaScript" src="./JS/tree_tpl.js"></script>
		<script language="JavaScript">var selected=[];var idproperty=\'\';var urlicons=\'\'</script>
		'.$treeOutput.'
		';
	}



/**************************************************************************************************/

//tobe Refactored  accordingly
$get="";
if (isset($_GET["external"])) {
	$external=TRUE;$get="?external=true";
} else {
	$external=FALSE;
}
/*
include_once('../../common/ext/loader/extension.php');
include_once('../../common/common.php');

$ext = extension::getExtension();
$log = $ext->loadExtension(EXTENSION);
$_SESSION["ext"]=$ext;
//load generis extension if a manifest is available
$ext = extension::getExtension();
$log = $ext->loadExtension(EXTENSION);
$_SESSION["ext"]=$ext;
*/
/*

if (!(isset($_GET["popup"])))
{
	if (!(isset($_SESSION["generis_admin"])))
		{
			if (!(isset($_SESSION["settings"])))
			{
				if (!(isset($_GET["replacecontentof"])))
					{	
						$TAOtree = new TAOtree();
						$Treeoutput=$TAOtree->getOutput(FALSE,$external);
					}
				else
					{
						$TAOtree = new TAOtree();
						$Treeoutput=$TAOtree->getOutput(FALSE,$external,$_GET["replacecontentof"]);
					}
			}
			else
			{$Treeoutput="<br><a href=index.php?settings=stop target=_top>Back to ressource management</a><br><br>";}
		}
		else
		{
			$generisadmin = new generis_Admin();
			$Treeoutput=$generisadmin->getOutput($_SESSION["generis_admin"]);
		}
}
if ($Treeoutput!="") {$div="TREE";} else {$div="CANCEL";} 


echo HEAD.'
	<body class=treeIframe>
	<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
	<script language="JavaScript" src="./JS/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
	<script language="JavaScript" src="./JS/tree.js"></script>
	<script language="JavaScript" src="./JS/tree_items.js"></script>
	<script language="JavaScript" src="./JS/tree_tpl.js"></script>
	<script language="JavaScript">var selected=[];var idproperty=\'\';var urlicons=\'\'</script>
	</div>';

 */

?>


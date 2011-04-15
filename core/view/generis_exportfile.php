<?php 

include("generis_ConstantsOfGui.php");
include("generis_utils.php");

if (!(isset($_SESSION))) {session_start();}
if (isset($_POST["format"]))
{
$xmlrdf = calltoKernel('exportxmlRDF',array($_SESSION["session"]));

	if ($_POST["format"]=="1")
	
	{$xslt = new xsltProcessor;
	$xsl = new DOMDocument;
	if ($xsl->load('../kernel/rdf2nt.xsl')) {echo "OK";} else {echo "Ko";}
	echo $xsl->saveXML();
	$xml = new DOMDocument;
	if ($xml->loadXML(trim($xmlrdf))) {echo "OK";} else {echo "Ko";}
	
	
	$xslt->importStyleSheet($xsl);
	print $xslt->transformToXML($xml);
	}
	else
	{ob_clean();
		header("Content-type: text/force-download");
		header('Content-Disposition: attachment; filename="'.$_SESSION["bd"].'.rdfs"');
	}
	
echo $xmlrdf;
die();
}

echo HEAD."<body class=paneIframe><br><br><br><form name=exportfile enctype=\"multipart/form-data\" action=generis_exportfile.php method=post><span class=leftmargin>".TABLEHEADER;
?>

N triples not IMPLEMENTED YET !

<tr><td>

Select format of file
</td><td>
<input type=radio CHECKED value="0" name="format">XML/RDF
<input type=radio value="1" name="format">N triples
<tr>
<tr>
</tr>

<tr><td></td><td>
<input type=submit value="Download">
</td></tr></table></form>


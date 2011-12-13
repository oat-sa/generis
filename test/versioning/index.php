<?php
error_reporting(E_ALL);
require_once dirname(__FILE__) . '/../../../tao/test/TestRunner.php';

$testSuite = new TestSuite('Versioning unit tests');
$testSuite->addFile(dirname(__FILE__).'/VersioningEnabledTestCase.php');
//$testSuite->addFile(dirname(__FILE__).'/../../../wfEngine/test/TranslationProcessExecutionTestCase.php');

//add the reporter regarding the context
if(PHP_SAPI == 'cli'){
	$reporter = new XmlTimeReporter();
}
else{
	$reporter = new HtmlReporter();
}

//run the unit test suite
$testSuite->run($reporter);
?>

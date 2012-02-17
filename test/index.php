<?php
error_reporting(E_ALL);
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
$testSuite = new TestSuite('Generis unit tests');

//get the test into each extensions
$tests = array_merge(
	TestRunner::getTests(array('generis'))
	,TestRunner::findTest(dirname(__FILE__).'/common')
);

//create the test sutie
foreach($tests as $i => $testCase){

    //TODO disable for release, remove after
    if(strpos($testCase, 'VirtuosoImplTestCase.php')== false 
    		&& strpos($testCase, 'SubscriptionsServiceTestCase.php') == false){
       $testSuite->addFile($testCase);
    }
}

//add versioning disabled test case
$testSuite->addFile(dirname(__FILE__).'/versioning/VersioningDisabledTestCase.php');

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
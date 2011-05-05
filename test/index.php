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
	$testSuite->addFile($testCase);
}

//$testSuite->addFile('PersistenceSwitcherTestCase.php');
//$testSuite->addFile('HardApiTestCase.php');

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
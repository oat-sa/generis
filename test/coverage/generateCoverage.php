<?php
require_once dirname(__FILE__) . '/../../../tao/test/TestRunner.php';

$testSuite = new TestSuite('Generis unit tests');

//get the test into each extensions
$tests = array_merge(
	TestRunner::getTests(array('generis'))
	,TestRunner::findTest(dirname(__FILE__).'/../common')
);

//create the test sutie
foreach($tests as $i => $testCase){

    //TODO disable for release, remove after
    if(strpos($testCase, 'VirtuosoImplTestCase.php')== false 
    		&& strpos($testCase, 'SubscriptionsServiceTestCase.php') == false
    && strpos($testCase, 'PDOWrapperTestCase.php') == false){
       $testSuite->addFile($testCase);
    }
}

//add versioning disabled test case
$testSuite->addFile(dirname(__FILE__).'/../versioning/VersioningDisabledTestCase.php');


//add the reporter regarding the context
if(PHP_SAPI == 'cli'){
	$reporter = new XmlTimeReporter();
}
else{
	$reporter =  new HtmlReporter();
}

require_once  PHPCOVERAGE_HOME. "CoverageRecorder.php";
require_once PHPCOVERAGE_HOME . "reporter/HtmlCoverageReporter.php";
$includePaths = array(ROOT_PATH.'generis/core',ROOT_PATH.'generis/common',ROOT_PATH.'generis/helpers');
$excludePaths = array(ROOT_PATH.'generis/common/conf',ROOT_PATH.'generis/common/exception');
$covReporter = new HtmlCoverageReporter("Code Coverage Report Generis", "", PHPCOVERAGE_REPORTS."generis/");
$cov = new CoverageRecorder($includePaths, $excludePaths, $covReporter);
//run the unit test suite
$cov->startInstrumentation();
$testSuite->run($reporter);
$cov->stopInstrumentation();

$cov->generateReport();
$covReporter->printTextSummary(PHPCOVERAGE_REPORTS.'generis_coverage.txt');
?>
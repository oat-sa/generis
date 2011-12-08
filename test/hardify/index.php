<?php
error_reporting(E_ALL);
require_once dirname(__FILE__) . '/../../../tao/test/TestRunner.php';
include_once dirname(__FILE__) . '/../../../tao/includes/raw_start.php';

Bootstrap::loadConstants ('tao');
Bootstrap::loadConstants ('filemanager');
Bootstrap::loadConstants ('taoItems');
Bootstrap::loadConstants ('taoGroups');
Bootstrap::loadConstants ('taoTests');
Bootstrap::loadConstants ('taoResults');
Bootstrap::loadConstants ('wfEngine');
Bootstrap::loadConstants ('taoDelivery');

$testSuite = new TestSuite('Hardify Unit Test Case');
$testSuite->addFile(dirname(__FILE__) . '/../../../tao/test/dataTest/MassInsertTestCase.php');
$testSuite->addFile(dirname(__FILE__) . '/HardifyTestCase.php');

//load generis test case
$testSuite->addFile(dirname(__FILE__) . '/../CollectionTestCase.php');
$testSuite->addFile(dirname(__FILE__) . '/../FileTestCase.php');
//$testSuite->addFile(dirname(__FILE__) . '/../ModelsRightTestCase.php');//policies in hard does not respect model rights
$testSuite->addFile(dirname(__FILE__) . '/../NamespaceTestCase.php');
$testSuite->addFile(dirname(__FILE__) . '/../PropertyTestCase.php');
//$testSuite->addFile(dirname(__FILE__) . '/../ResourceTestCase.php');//the test case still uses references to old api (setStatements ..). These references have to be refactored with the new persistence layer
$testSuite->addFile(dirname(__FILE__) . '/../UserServiceTestCase.php');
$testSuite->addFile(dirname(__FILE__) . '/../UtilsTestCase.php');

//load other extensions' test cases
$tests = array_merge(
	TestRunner::getTests(array('tao'))
	, TestRunner::getTests(array('taoItems'))
	, TestRunner::getTests(array('taoTests'))
	, TestRunner::getTests(array('taoSubjects'))
	, TestRunner::getTests(array('taoResults'))
	, TestRunner::getTests(array('taoDelivery'))
	, TestRunner::getTests(array('taoGroups'))
	, TestRunner::getTests(array('wfEngine'))
	, TestRunner::getTests(array('filemanager'))
);
foreach($tests as $i => $testCase){	
	//TODO disable for release, remove after
    if(strpos($testCase, 'VirtuosoImplTestCase.php')== false
    	&& strpos($testCase, 'VirtuosoImplTestCase.php')== false){
       $testSuite->addFile($testCase);
    }
}
   
$testSuite->addFile(dirname(__FILE__) . '/UnhardifyTestCase.php');
$testSuite->addFile(dirname(__FILE__) . '/../../../tao/test/dataTest/CleanMassInsertTestCase.php');

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
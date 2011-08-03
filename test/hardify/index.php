<?php
error_reporting(E_ALL);
require_once dirname(__FILE__) . '/../../../tao/test/TestRunner.php';

$testSuite = new TestSuite('Hardened unit test case');
$testSuite->addFile('HardifyTestCase.php');
$testSuite->addFile(dirname(__FILE__) . '/../../../tao/test/dataTest/MassInsertTestCase.php');
$testSuite->addFile(dirname(__FILE__) . '/../CollectionTestCase.php');
$testSuite->addFile(dirname(__FILE__) . '/../FileTestCase.php');
//$testSuite->addFile(dirname(__FILE__) . '/../ModelsRightTestCase.php');//policies in hard does not respect model rights
$testSuite->addFile(dirname(__FILE__) . '/../NamespaceTestCase.php');
$testSuite->addFile(dirname(__FILE__) . '/../PropertyTestCase.php');
//$testSuite->addFile(dirname(__FILE__) . '/../ResourceTestCase.php');//the test case still uses references to old api (setStatements ..). These references have to be refactored with the new persistence layer
$testSuite->addFile(dirname(__FILE__) . '/../UserServiceTestCase.php');
$testSuite->addFile(dirname(__FILE__) . '/../UtilsTestCase.php');
$testSuite->addFile(dirname(__FILE__) . '/../../../tao/test/dataTest/CleanMassInsertTestCase.php');
$testSuite->addFile('UnhardifyTestCase.php');

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
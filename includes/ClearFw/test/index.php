<?php
require_once ("./simpletest/unit_tester.php");
require_once ("./simpletest/reporter.php");
require_once ("../core/HttpRequest.class.php");
require_once ("../core/Error.class.php");
require_once ("../core/TestVariables.php");

$test	= new GroupTest("All tests");
$test->addTestFile("HttpRequestTestCase.class.php");
$test->run(new HtmlReporter());

?>

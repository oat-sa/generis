<?php

require_once dirname(__FILE__).'/../../common/inc.extension.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';
require_once INCLUDES_PATH.'/ClearFw/core/simpletestRunner/_main.php';


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

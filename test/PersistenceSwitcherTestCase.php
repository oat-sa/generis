<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class PersistenceSwitcherTestCase extends UnitTestCase {
	
	public function setUp(){

	    TestRunner::initTest();

	}

	public function testUtils(){
		$class = new core_kernel_classes_Class(CLASS_ROLE);
		$shortName = core_kernel_persistence_switcher_Utils::getShortName($class);
		$this->assertEqual($shortName, "15ClassRole");
	}
	
	
}
?>
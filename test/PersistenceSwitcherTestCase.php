<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class PersistenceSwitcherTestCase extends UnitTestCase {
	
	public function setUp(){

	    TestRunner::initTest();

	}

	public function testPropertyFinder(){
		
		$testTakerClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
		
		$ps = new core_kernel_persistence_switcher_PropertySwitcher($testTakerClass, new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#User'));
		$result = $ps->getProperties();
		$this->assertEqual(count($result), 8);
		$this->assertTrue(array_key_exists('http://www.tao.lu/Ontologies/generis.rdf#userFirstName', $result));
		$this->assertTrue(array_key_exists('http://www.tao.lu/Ontologies/generis.rdf#userLastName', $result));
		$this->assertTrue(array_key_exists('http://www.tao.lu/Ontologies/generis.rdf#password', $result));
		$this->assertTrue(array_key_exists('http://www.tao.lu/Ontologies/generis.rdf#login', $result));
		$this->assertTrue(array_key_exists('http://www.tao.lu/Ontologies/generis.rdf#userMail', $result));
		$this->assertTrue(array_key_exists('http://www.tao.lu/Ontologies/generis.rdf#userDefLg', $result));
		$this->assertTrue(array_key_exists('http://www.tao.lu/Ontologies/generis.rdf#userUILg', $result));
		
		
		$ps = new core_kernel_persistence_switcher_PropertySwitcher($testTakerClass, new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#TAOObject'));
		$result = $ps->getProperties();
		$this->assertEqual(count($result), 1);
		$this->assertTrue(array_key_exists(RDFS_LABEL, $result));
		
		$ps = new core_kernel_persistence_switcher_PropertySwitcher($testTakerClass);
		$result = $ps->getProperties();
		$this->assertTrue(array_key_exists(RDFS_LABEL, $result));
	}
	
	public function testHardify(){
		$testTakerClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
		$userClass		= new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#User');

		core_kernel_persistence_Switcher::hardifier($testTakerClass, array(
			'topClass'		=> $userClass,
			'recursive'		=> true
		));
	}
	
}
?>
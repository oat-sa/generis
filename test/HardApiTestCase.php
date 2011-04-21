<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class HardApiTestCase extends UnitTestCase {
	
	public function setUp(){
	    TestRunner::initTest();
	}
	
	public function testUtils(){
		
		$class = new core_kernel_classes_Class(CLASS_ROLE);
		$shortName = core_kernel_persistence_hardapi_Utils::getShortName($class);
		$this->assertEqual($shortName, "15ClassRole");
	}
	
	public function testCreateDTable(){
		$myTblMgr = new core_kernel_persistence_hardapi_TableManager('15ClassRole');
		$this->assertFalse($myTblMgr->exists());
		$this->assertTrue($myTblMgr->create());
		$this->assertTrue($myTblMgr->exists());
		$this->assertTrue($myTblMgr->remove());
	}
	
	public function testCreateComplexTable(){
		
		$myLevelTblMgr = new core_kernel_persistence_hardapi_TableManager('15ClassLevel');
		$this->assertFalse($myLevelTblMgr->exists());
		$this->assertTrue($myLevelTblMgr->create());
		
		$myRoleTblMgr = new core_kernel_persistence_hardapi_TableManager('15ClassRole');
		$this->assertFalse($myRoleTblMgr->exists());
		$this->assertTrue($myRoleTblMgr->create(array(
			array('name' => '15Description'),
			array(
				'name' 		=> '15Level',
				'foreign'	=> '15ClassLevel'
			)
		)));
		$this->assertTrue($myRoleTblMgr->exists());
		
		$this->assertTrue($myRoleTblMgr->remove());
		$this->assertTrue($myLevelTblMgr->remove());
	}
	
	public function testResourceReferencer(){
		$rsRef = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		$this->assertIsA($rsRef, 'core_kernel_persistence_hardapi_ResourceReferencer');
		
		$testTakerClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
		$testTaker = $testTakerClass->createInstance('test taker 1');
		
		$rsRef->referenceResource($testTaker, false);
	}
}
?>
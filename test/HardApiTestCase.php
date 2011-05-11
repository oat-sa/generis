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
	
	public function testCreateTable(){
		$myTblMgr = new core_kernel_persistence_hardapi_TableManager('_15ClassRole');
		$this->assertFalse($myTblMgr->exists());
		
		$this->assertTrue($myTblMgr->create());
		$this->assertTrue($myTblMgr->exists());
		
		$this->assertTrue($myTblMgr->remove());
		$this->assertFalse($myTblMgr->exists());
	}
	
	public function testCreateComplexTable(){
		
		$myLevelTblMgr = new core_kernel_persistence_hardapi_TableManager('_15ClassLevel');
		$this->assertFalse($myLevelTblMgr->exists());
		$this->assertTrue($myLevelTblMgr->create());
		$this->assertTrue($myLevelTblMgr->exists());
		
		$myRoleTblMgr = new core_kernel_persistence_hardapi_TableManager('_15ClassRole');
		$this->assertFalse($myRoleTblMgr->exists());
		$this->assertTrue($myRoleTblMgr->create(array(
			array('name' => '15Description'),
			array(
				'name' 		=> '15Level',
				'foreign'	=> '15ClassLevel'
			)
		)));
		$this->assertTrue($myRoleTblMgr->exists());
		
		$this->assertTrue($myLevelTblMgr->remove());
		$this->assertFalse($myLevelTblMgr->exists());
		
		$this->assertTrue($myRoleTblMgr->remove());
		$this->assertFalse($myRoleTblMgr->exists());
	}
	
	public function testResourceReferencer(){
		$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		$this->assertIsA($referencer, 'core_kernel_persistence_hardapi_ResourceReferencer');
		
		$testTakerClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
		$testTaker = $testTakerClass->createInstance('test taker 1');
		
		$table = core_kernel_persistence_hardapi_Utils::getShortName($testTakerClass);
		
		$referencer->referenceResource($testTaker, $table);
		$this->assertTrue($referencer->isResourceReferenced($testTaker));
		$this->assertEqual($referencer->resourceLocation($testTaker), $table);
		
		$referencer->unReferenceResource($testTaker);
		$this->assertFalse($referencer->isResourceReferenced($testTaker));
	}
	
	public function testClassReferencer(){
		
		$referencer = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		$this->assertIsA($referencer, 'core_kernel_persistence_hardapi_ResourceReferencer');
		
		$class = new core_kernel_classes_Class(CLASS_ROLE) ;
		
		$table = '_'.core_kernel_persistence_hardapi_Utils::getShortName($class);
		
		$myTblMgr = new core_kernel_persistence_hardapi_TableManager($table);
		$this->assertFalse($myTblMgr->exists());
		
		$this->assertTrue($myTblMgr->create());
		$this->assertTrue($myTblMgr->exists());
		
		
		$referencer->referenceClass($class, $table);
		
		$this->assertTrue($referencer->isClassReferenced($class));
		$this->assertTrue($referencer->isClassReferenced($class, $table));
		$foundTables = $referencer->classLocations($class);
		foreach($foundTables as $foundTable){
			$this->assertEqual($foundTable['table'], $table);
			$this->assertEqual($foundTable['uri'], $class->uriResource);
		}
		
		$referencer->unReferenceClass($class);
		$this->assertFalse($referencer->isClassReferenced($class));
		
		
		$this->assertTrue($myTblMgr->remove());
		$this->assertFalse($myTblMgr->exists());
	}
}
?>
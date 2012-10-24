<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class HardDbSubjectTestCase extends UnitTestCase {

	protected $targetSubjectClass = null;
	protected $targetSubjectSubClass = null;
	protected $dataIntegrity = array ();

	public function setUp(){
		//if (!defined('DEBUG_PERSISTENCE'))
		//	define('DEBUG_PERSISTENCE', true);
		TestRunner::initTest();
	}

	private function countStatements (){
		$query =  "SELECT count(*) FROM statements";
		$result = core_kernel_classes_DbWrapper::singleton()->query($query);
		$row = $result->fetch();
		return $row[0];
	}
	
	public function testCreateContextOfThetest(){
		$this->dataIntegrity['statements'] = $this->countStatements();
		
		// Top Class : TaoSubject
		$subjectClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
		// Create a new subject class for the unit test
		$this->targetSubjectClass = $subjectClass->createSubClass ("Sub Subject Class (Unit Test)");

		// Add an instance to this subject class
		$this->subject1 = $this->targetSubjectClass->createInstance ("Sub Subject (Unit Test)");
		$this->assertEqual (count($this->targetSubjectClass->getInstances ()), 1);

		// Create a new subject sub class to the previous sub class
		$this->targetSubjectSubClass = $this->targetSubjectClass->createSubClass ("Sub Sub Subject Class (Unit Test)");
		// Add an instance to this sub subject class
		$this->subject2 = $this->targetSubjectSubClass->createInstance ("Sub Sub Subject (Unit Test)");
		$this->assertEqual (count($this->targetSubjectSubClass->getInstances ()), 1);

		$this->assertEqual (count($this->targetSubjectClass->getInstances ()), 1);
		// If get instances in the sub classes of the targetSubjectClass, we should get 2 instances
		$this->assertEqual (count($this->targetSubjectClass->getInstances (true)), 2);

	}

	public function testStoreKeyDataIntegrity (){
		
		$this->assertTrue(core_kernel_persistence_ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectClass) instanceof core_kernel_persistence_smoothsql_Class);
	}

	public function testHardifier () {
		
		$switcher = new core_kernel_persistence_Switcher();
		$switcher->hardify($this->targetSubjectClass, array(
			'topClass'		=> new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#User'),
			'recursive'		=> true,
			'createForeigns'=> false,
			'rmSources'		=> true
		));
		unset ($switcher);
		
		$this->assertTrue(core_kernel_persistence_ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectClass) instanceof core_kernel_persistence_hardsql_Class);
		$this->assertTrue(core_kernel_persistence_ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectSubClass) instanceof core_kernel_persistence_hardsql_Class);
	}

	public function testUnhardifier () {
		
		$switcher = new core_kernel_persistence_Switcher();
		$switcher->unhardify($this->targetSubjectClass, array(
			'recursive'			=> true,
			'removeForeigns'	=> false
		));
		unset ($switcher);
	}
	
	public function testDataIntegrity (){
		
		$this->assertFalse(core_kernel_persistence_ClassProxy::singleton()->isValidContext('hardsql', $this->targetSubjectClass));
		$this->assertTrue(core_kernel_persistence_ClassProxy::singleton()->isValidContext('smoothsql', $this->targetSubjectClass));
		$this->assertTrue(core_kernel_persistence_ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectClass) instanceof core_kernel_persistence_smoothsql_Class);
		$this->assertFalse(core_kernel_persistence_ClassProxy::singleton()->isValidContext('hardsql', $this->targetSubjectSubClass));
		$this->assertTrue(core_kernel_persistence_ClassProxy::singleton()->isValidContext('smoothsql', $this->targetSubjectSubClass));
		$this->assertTrue(core_kernel_persistence_ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectSubClass) instanceof core_kernel_persistence_smoothsql_Class);
	}
	
	public function testClean (){
		// Remove the resources
		foreach ($this->targetSubjectClass->getInstances() as $instance){
			$instance->delete ();
		}
		foreach ($this->targetSubjectSubClass->getInstances() as $instance){
			$instance->delete ();
		}
		
		$this->targetSubjectClass->delete(true);
		$this->targetSubjectSubClass->delete(true);
	}
	
}

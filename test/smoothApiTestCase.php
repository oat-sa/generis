<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class HardDbSubjectTestCase extends UnitTestCase {
	
	protected $targetSubjectClass = null;
	protected $targetSubjectSubClass = null;
	
	public function setUp(){

	    TestRunner::initTest();

	}

	public function testGetSetContext(){		
		// Force mode to Smooth
		tao_helpers_Context::load ('PERSISTENCE_SMOOTH');
		$this->assertTrue (tao_helpers_Context::check ('PERSISTENCE_SMOOTH'));
		tao_helpers_Context::unload ('PERSISTENCE_SMOOTH');
		$this->assertTrue (!tao_helpers_Context::check ('PERSISTENCE_SMOOTH'));
	}
	
	public function testCreateContextOfThetest(){
		// Top Class : TaoSubject
		$subjectClass =  new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
		// Create a new subject class for the unit test
		$this->targetSubjectClass = $subjectClass->createSubClass ("Sub Subject Class (Unit Test)");
		// Add an instance to this subject class
		$this->targetSubjectClass->createInstance ("Sub Subject (Unit Test)");
		$this->assertEqual (count($this->targetSubjectClass->getInstances ()), 1);
		
		// Create a new subject sub class to the previous sub class
		$this->targetSubjectSubClass = $this->targetSubjectClass->createSubClass ("Sub Sub Subject Class (Unit Test)");
		// Add an instance to this sub subject class
		$this->targetSubjectSubClass->createInstance ("Sub Sub Subject (Unit Test)");
		$this->assertEqual (count($this->targetSubjectSubClass->getInstances ()), 1);
		
		$this->assertEqual (count($this->targetSubjectClass->getInstances ()), 1);
		// If get instances in the sub classes of the targetSubjectClass, we should get 2 instances
		$this->assertEqual (count($this->targetSubjectClass->getInstances (true)), 2);
	}
	
	public function testHardifier (){
		core_kernel_persistence_Switcher::hardifier($this->targetSubjectClass, array(
			'topClass'		=> new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#User'),
			'additionalProperties' => array (new core_kernel_classes_Property (RDF_TYPE)),
			'recursive'		=> true,
			'createForeigns'=> true
		));
		core_kernel_persistence_Switcher::hardifier($this->targetSubjectSubClass, array(
			'topClass'		=> new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#User'),
			'additionalProperties' => array (new core_kernel_classes_Property (RDF_TYPE)),
			'recursive'		=> true,
			'createForeigns'=> true
		));
		// Reset the persistence cache
		core_kernel_persistence_ClassProxy::$ressourcesDelegatedTo = array();
		core_kernel_persistence_ResourceProxy::$ressourcesDelegatedTo = array();
		core_kernel_persistence_PropertyProxy::$ressourcesDelegatedTo = array();
	}
	
	public function testHardGetInstances (){
		$this->assertEqual (count($this->targetSubjectClass->getInstances ()), 1);
		$this->assertEqual (count($this->targetSubjectSubClass->getInstances ()), 1);
		$this->assertEqual (count($this->targetSubjectClass->getInstances (true)), 2);
	}
	
	public function testHardCreateInstance() {
//		var_dump(core_kernel_persistence_ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectClass));
		$subI = $this->targetSubjectClass->createInstance ("Hard Sub Subject (Unit Test)");
//		var_dump(core_kernel_persistence_ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectClass));
//		var_dump(core_kernel_persistence_hardapi_ResourceReferencer::singleton()->isResourceReferenced ($subI));
//		var_dump($subI->getType());
		$this->assertEqual (count($this->targetSubjectClass->getInstances ()), 2);
		$subSubI = $this->targetSubjectSubClass->createInstance ("Hard Sub Sub Subject (Unit Test)");
		$this->assertEqual (count($this->targetSubjectSubClass->getInstances ()), 2);
		$this->assertEqual (count($this->targetSubjectClass->getInstances (true)), 4);
		
		$subI->delete();
	}
	
	public function testProperties (){
		
	}
	
	public function testClean (){
		$tm = new core_kernel_persistence_hardapi_TableManager (core_kernel_persistence_hardapi_Utils::getShortName ($this->targetSubjectClass));
		$tm->remove();
		$tm = new core_kernel_persistence_hardapi_TableManager (core_kernel_persistence_hardapi_Utils::getShortName ($this->targetSubjectSubClass));
		$tm->remove();
		$tm = new core_kernel_persistence_hardapi_TableManager ("06Languages");
		$tm->remove();
	}
	
	public function testHardImpl(){
//		$subjectClass =  new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
//		tao_helpers_Context::load ('PERSISTENCE_HARD');
//		$instances = $subjectClass->getInstances (true);
//		var_dump (count($instances));
//		tao_helpers_Context::unload ('PERSISTENCE_HARD');
	}

}
?>

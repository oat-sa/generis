<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class HardDbSubjectTestCase extends UnitTestCase {
	
	protected $targetSubjectClass = null;
	protected $targetSubjectSubClass = null;
	
	public function setUp(){

	    TestRunner::initTest();

	}
	
	public function testCreateContextOfThetest(){
		// Top Class : TaoSubject
		$subjectClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
		// Create a new subject class for the unit test
		$this->targetSubjectClass = $subjectClass->createSubClass ("Sub Subject Class (Unit Test)");
		// Add a custom property to the newly created class

		// Add an instance to this subject class
		$this->subject1 = $this->targetSubjectClass->createInstance ("Sub Subject (Unit Test)");
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
		$switcher = new core_kernel_persistence_Switcher();
		$switcher->hardify($this->targetSubjectClass, array(
			'topClass'		=> new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#User'),
			'additionalProperties' => array (new core_kernel_classes_Property (RDF_TYPE)),
			'recursive'		=> true,
			'createForeigns'=> true
		));
		unset ($switcher);
	}
	
	public function testSwitchOnHardOK(){
		// Test that resource are now available from the hard sql implementation
		$this->assertTrue (core_kernel_persistence_ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectClass) instanceof core_kernel_persistence_hardsql_Class);
		$this->assertTrue (core_kernel_persistence_ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectSubClass) instanceof core_kernel_persistence_hardsql_Class);
	}
	
	public function testHardGetInstances (){
		// Get the hardified instance from the hard sql imlpementation
		$this->assertEqual (count($this->targetSubjectClass->getInstances ()), 1);
		$this->assertEqual (count($this->targetSubjectSubClass->getInstances ()), 1);
		$this->assertEqual (count($this->targetSubjectClass->getInstances (true)), 2);
	}
	
	public function testHardCreateInstance() {
		// Create instance with the hard sql implementation
		$subject = $this->targetSubjectClass->createInstance ("Hard Sub Subject (Unit Test)");
		$this->assertTrue (core_kernel_persistence_hardapi_ResourceReferencer::singleton()->isResourceReferenced($subject));
		$this->assertTrue (core_kernel_persistence_ResourceProxy::singleton()->getImpToDelegateTo($subject) instanceof core_kernel_persistence_hardsql_Resource);
		$this->assertEqual (count($this->targetSubjectClass->getInstances ()), 2);

		$subSubject = $this->targetSubjectSubClass->createInstance ("Hard Sub Sub Subject (Unit Test)");
		$this->assertTrue (core_kernel_persistence_hardapi_ResourceReferencer::singleton()->isResourceReferenced($subSubject));
		$this->assertEqual (count($this->targetSubjectSubClass->getInstances ()), 2);
		$this->assertEqual (count($this->targetSubjectClass->getInstances (true)), 4);
	}
	
	public function testForceMode (){
		// Check if the returner implementation are correct
		core_kernel_persistence_PersistenceProxy::setMode (PERSISTENCE_SMOOTH);
		$classProxy = core_kernel_persistence_ClassProxy::singleton();
		$impl = $classProxy->getImpToDelegateTo($this->targetSubjectClass);
		$this->assertTrue ($impl instanceof core_kernel_persistence_smoothsql_Class);
		$this->assertEqual (count($this->targetSubjectClass->getInstances ()), 1);
		$this->assertEqual (count($this->targetSubjectSubClass->getInstances ()), 1);
		core_kernel_persistence_PersistenceProxy::resetMode ();
		$this->assertTrue (core_kernel_persistence_ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectClass) instanceof core_kernel_persistence_hardsql_Class);
		$this->assertTrue (core_kernel_persistence_ResourceProxy::singleton()->getImpToDelegateTo($this->subject1) instanceof core_kernel_persistence_hardsql_Resource);
	}
	
	public function testProperties (){
		// Set properties
		foreach ($this->targetSubjectClass->getInstances(true) as $instance){
			// Set mutltiple property
			$instance->setPropertyValue (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'), new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN'));
			$instance->setPropertyValue (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'), new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangFR'));
			// Set property value by lg
			$instance->setPropertyValueByLg (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent'), 'Test Content EN', 'EN');
			$instance->setPropertyValueByLg (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent'), 'Test Content FR', 'FR');
			// Set property type (SPECIAL CASE)
			$instance->setPropertyValue (new core_kernel_classes_Property(RDF_TYPE), 'http://www.tao.lu/Ontologies/TAO.rdf#TaoSubjectRole');
			// Set foreign property
			$instance->setPropertyValue (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'), 'http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
			$instance->setPropertyValue (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userUiLg'), 'http://www.tao.lu/Ontologies/TAO.rdf#LangFR');
		}
		// Get properties
		foreach ($this->targetSubjectClass->getInstances() as $instance){
			
			///// GET ONE PROPERTY VALUE
			
			// Specific case show later
			$prop = $instance->getOnePropertyValue (new core_kernel_classes_Property(RDF_TYPE));
			$this->assertTrue ($prop instanceof core_kernel_classes_Resource);
			// Get single property label
			$prop = $instance->getOnePropertyValue (new core_kernel_classes_Property(RDFS_LABEL));
			$this->assertTrue ($prop instanceof core_kernel_classes_Literal);
			// Get multiple property (is lg dependent)
			$prop = $instance->getOnePropertyValue (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent'));
			$this->assertTrue ($prop instanceof core_kernel_classes_Literal);
			// Get mutliple property (multiple)
			$prop = $instance->getOnePropertyValue (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$this->assertTrue ($prop instanceof core_kernel_classes_Resource);
			//$this->assertTrue ($label instanceof core_kernel_classes_Literal);

			// GET PROPERTY VALUES
			
			// Get property values on single (literal) property 
			$props = $instance->getPropertyValues (new core_kernel_classes_Property(RDFS_LABEL));
			$this->assertEqual (count($props), 1);
			foreach ($props as $prop){
				$this->assertTrue ($prop instanceof core_kernel_classes_Literal);
			}
			// Get property values on single (resource) property 
			$props = $instance->getPropertyValues (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'));
			$this->assertEqual (count($props), 1);
			foreach ($props as $prop){
				$this->assertTrue ($prop instanceof core_kernel_classes_Resource);
			}
			// Get property values on mutltiple property
			$props = $instance->getPropertyValues (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$this->assertEqual (count($props), 2);
			foreach ($props as $prop){
				$this->assertTrue ($prop instanceof core_kernel_classes_Resource);
			}
			// Get property values on mutltiple (by lg) property
			// Common behavior is to return reccords function of a defined language or function of the default system language if the record is language dependent
			$props = $instance->getPropertyValues (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent'));
			$this->assertEqual (count($props), 1);
			foreach ($props as $prop){
				$this->assertTrue ($prop instanceof core_kernel_classes_Literal);
			}
			
			///// GET PROPERTY VALUE BY LG
			
//			$prop = $instance->getPropertyValuesByLg (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent'), 'EN');
//			var_dump ($prop);
//			$this->assertTrue ($prop instanceof core_kernel_classes_Resource);
			
		}
	}
	
	public function testClean (){
		// Remove the resources
		foreach ($this->targetSubjectClass->getInstances() as $instance){
			$instance->delete ();
			$this->assertFalse (core_kernel_persistence_hardapi_ResourceReferencer::singleton()->isResourceReferenced($instance));
		}
		foreach ($this->targetSubjectSubClass->getInstances() as $instance){
			$instance->delete ();
			$this->assertFalse (core_kernel_persistence_hardapi_ResourceReferencer::singleton()->isResourceReferenced($instance));
		}
		
		// Remove the tables
		$tm = new core_kernel_persistence_hardapi_TableManager ('_'.core_kernel_persistence_hardapi_Utils::getShortName ($this->targetSubjectClass));
		$tm->remove();
		$tm = new core_kernel_persistence_hardapi_TableManager ('_'.core_kernel_persistence_hardapi_Utils::getShortName ($this->targetSubjectSubClass));
		$tm->remove();
		$tm = new core_kernel_persistence_hardapi_TableManager ('_06Languages');
		$tm->remove();
	}

}
?>

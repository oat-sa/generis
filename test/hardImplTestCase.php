<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class HardImplTestCase extends UnitTestCase {
	
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
		core_kernel_persistence_PersistenceProxy::forceMode (PERSISTENCE_SMOOTH);
		$classProxy = core_kernel_persistence_ClassProxy::singleton();
		$impl = $classProxy->getImpToDelegateTo($this->targetSubjectClass);
		$this->assertTrue ($impl instanceof core_kernel_persistence_smoothsql_Class);
		$this->assertEqual (count($this->targetSubjectClass->getInstances ()), 1);
		$this->assertEqual (count($this->targetSubjectSubClass->getInstances ()), 1);
		core_kernel_persistence_PersistenceProxy::resetMode ();
		$this->assertTrue (core_kernel_persistence_ClassProxy::singleton()->getImpToDelegateTo($this->targetSubjectClass) instanceof core_kernel_persistence_hardsql_Class);
		$this->assertTrue (core_kernel_persistence_ResourceProxy::singleton()->getImpToDelegateTo($this->subject1) instanceof core_kernel_persistence_hardsql_Resource);
	}
	
	public function testSetProperties (){
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
	}
	
	public function testGetOnePropertyValue (){
		foreach ($this->targetSubjectClass->getInstances() as $instance){
			
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
		}
	}
	
	public function testGetPropertyValues () {
		foreach ($this->targetSubjectClass->getInstances() as $instance){
			
			// Get property values on single (literal) property 
			$props = $instance->getPropertyValues (new core_kernel_classes_Property(RDFS_LABEL));
			$this->assertEqual (count($props), 1);
			foreach ($props as $prop){
				$this->assertTrue (is_string($prop));
			}
			// Get property values on single (resource) property 
			$props = $instance->getPropertyValues (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'));
			$this->assertEqual (count($props), 1);
			foreach ($props as $prop){
				$this->assertTrue (common_Utils::isUri($prop));
			}
			// Get property values on mutltiple property
			$props = $instance->getPropertyValues (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$this->assertEqual (count($props), 2);
			foreach ($props as $prop){
				$this->assertTrue (common_Utils::isUri($prop));
			}
			// Get property values on mutltiple (by lg) property
			// Common behavior is to return reccords function of a defined language or function of the default system language if the record is language dependent
			$props = $instance->getPropertyValues (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent'));
			$this->assertEqual (count($props), 1);
			foreach ($props as $prop){
				$this->assertTrue (is_string($prop));
			}		
		}
	}
			
	public function testGetPropertyValuesCollection (){
		foreach ($this->targetSubjectClass->getInstances() as $instance){
			
			$props = $instance->getPropertyValuesCollection (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$this->assertTrue ($props instanceof core_kernel_classes_ContainerCollection);
			$this->assertEqual ($props->count(), 2);
			foreach ($props->getIterator() as $prop){
				
				$this->assertTrue ($prop instanceof core_kernel_classes_Resource);
			}		
		}
	}
	
	public function testGetPropertyValuesByLg (){
		foreach ($this->targetSubjectClass->getInstances() as $instance){	
			
			$props = $instance->getPropertyValuesByLg (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent'), 'FR');
			$this->assertEqual (count($props), 1);
			$this->assertEqual ($props[0], 'Test Content FR');
			foreach ($props as $prop){
				
				$this->assertTrue ($prop instanceof core_kernel_classes_Literal);
			}		
		}
	}
	
	public function testRemovePropertyValues (){
		foreach ($this->targetSubjectClass->getInstances() as $instance){	
			
			// Remove literal single property
//			$instance->removePropertyValues (new core_kernel_classes_Property(RDFS_LABEL));
//			$props = $instance->getPropertyValues (new core_kernel_classes_Property(RDFS_LABEL));
//			$this->assertTrue (empty($props));
			
			// Remove foreign single property
			$instance->removePropertyValues (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'));
			$props = $instance->getPropertyValues (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'));
			$this->assertTrue (empty($props));
			
			// Remove literal multiple property
			$instance->removePropertyValues (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent'));
			$props = $instance->getPropertyValues (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent'));
			$this->assertTrue (empty($props));
			
			// Remove foreign multiple property
			$instance->removePropertyValues (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$props = $instance->getPropertyValues (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$this->assertTrue (empty($props));
			
		}
	}
	
	public function testRemovePropertyValuesByLg (){
		
		$this->testSetProperties();
		
		foreach ($this->targetSubjectClass->getInstances() as $instance){	
			
			// Remove literal single property
			$instance->removePropertyValueByLg (new core_kernel_classes_Property(RDFS_LABEL), 'FR');
			$props = $instance->getPropertyValues (new core_kernel_classes_Property(RDFS_LABEL));
			$this->assertTrue (empty($props));
			
			// Remove foreign single property
			$instance->removePropertyValueByLg (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'), 'FR');
			$props = $instance->getPropertyValues (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/generis.rdf#userDefLg'));
			$this->assertFalse (empty($props));
			
			// Remove literal multiple property
			$instance->removePropertyValueByLg (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent'), 'FR');
			$props = $instance->getPropertyValues (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTest.rdf#TestContent'));
			$this->assertTrue (empty($props));
			
			// Remove foreign multiple property
			$instance->removePropertyValueByLg (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'), 'FR');
			$props = $instance->getPropertyValues (new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOGroup.rdf#Members'));
			$this->assertFalse (empty($props));
			
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
	
	public function testFilterByLanguage() {
		return;
		$session = core_kernel_classes_Session::singleton();
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$true = new core_kernel_classes_Resource(GENERIS_TRUE);
		
		$this->object->setStatement($true->uriResource,RDFS_SEEALSO,'test1', '');
		$this->object->setStatement($true->uriResource,RDFS_SEEALSO,'test2', '');
		$this->object->setStatement($true->uriResource,RDFS_SEEALSO,'testing', 'EN');
		$this->object->setStatement($true->uriResource,RDFS_SEEALSO,'essai', 'FR');
		$this->object->setStatement($true->uriResource,RDFS_SEEALSO,'testung1', 'SE');
		$this->object->setStatement($true->uriResource,RDFS_SEEALSO,'testung2', 'SE');
		
		// Get some propertyValues as if it was obtained by an SQL Statement.
		// First test is made with the default language selected.
		$modelIds	= implode(',',array_keys($session->getLoadedModels()));
        $query =  "SELECT object, l_language FROM statements 
		    		WHERE subject = ? AND predicate = ?
		    		AND (l_language = '' OR l_language = ? OR l_language = ?)
		    		AND modelID IN ({$modelIds})";
		    		
        $result	= $dbWrapper->execSql($query, array(
        	GENERIS_TRUE,
        	RDFS_SEEALSO,
        	$session->defaultLg,
        	($session->getLg() != '') ? $session->getLg() : $session->defaultLg
        ));
        
        $sorted = core_kernel_persistence_smoothsql_Utils::sortByLanguage($result, 'l_language');
        $filtered = core_kernel_persistence_smoothsql_Utils::getFirstLanguage($sorted);
        $this->assertTrue(count($sorted) == 3 && $sorted[0]['value'] == 'testing');
        $this->assertTrue(count($filtered) == 1 && $filtered[0] == 'testing');
       
        // Second test is based on a particular language.
        $session->setLg('FR');
        $result	= $dbWrapper->execSql($query, array(
        	GENERIS_TRUE,
        	RDFS_SEEALSO,
        	$session->defaultLg,
        	($session->getLg() != '') ? $session->getLg() : $session->defaultLg
        ));
        
        $sorted = core_kernel_persistence_smoothsql_Utils::sortByLanguage($result, 'l_language');
        $filtered = core_kernel_persistence_smoothsql_Utils::getFirstLanguage($sorted);
        $this->assertTrue(count($sorted) == 4 && $sorted[0]['value'] == 'essai');
        $this->assertTrue(count($filtered) == 1 && $filtered[0] == 'essai');
		
		// Third test looks if the default language is respected.
		// No japanese values here, but default language set to EN.
		// Here we use the function filterByLanguage which aggregates sortByLanguage
		// and getFirstLanguage.
		$session->setLg('JA');
        $result	= $dbWrapper->execSql($query, array(
        	GENERIS_TRUE,
        	RDFS_SEEALSO,
        	$session->defaultLg,
        	($session->getLg() != '') ? $session->getLg() : $session->defaultLg
        ));
        
        $filtered = core_kernel_persistence_smoothsql_Utils::filterByLanguage($result, 'l_language');
        $this->assertTrue(count($filtered) == 1 && $filtered[0] == 'testing');
		
		$session->setLg('');
		
		// Set back ontology to normal.
		$this->object->removeStatement($true->uriResource,RDFS_SEEALSO,'test1', '');
		$this->object->removeStatement($true->uriResource,RDFS_SEEALSO,'test2', '');
		$this->object->removeStatement($true->uriResource,RDFS_SEEALSO,'testing', 'EN');
		$this->object->removeStatement($true->uriResource,RDFS_SEEALSO,'essai', 'FR');
		$this->object->removeStatement($true->uriResource,RDFS_SEEALSO,'testung1', 'SE');
		$this->object->removeStatement($true->uriResource,RDFS_SEEALSO,'testung2', 'SE');
	}
	
	public function testIdentifyFirstLanguage() {
		$values = array(
			array('language' => 'EN', 'value' => 'testFallback'),
			array('language' => '', 'value' => 'testEN')
		);
		
		$this->assertTrue(core_kernel_persistence_smoothsql_Utils::identifyFirstLanguage($values) == 'EN');
		
		$values = array(
			array('language' => 'JA', 'value' => 'testJA1'),
			array('language' => 'JA', 'value' => 'testJA2'),
			array('language' => 'EN', 'value' => 'testEN1'),
			array('language' => 'EN', 'value' => 'testEN1'),
			array('language' => '', 'value' => 'testFallback1'),
			array('language' => '', 'value' => 'testFallback2')	
		);
		
		$this->assertTrue(core_kernel_persistence_smoothsql_Utils::identifyFirstLanguage($values) == 'JA');
	}

}
?>

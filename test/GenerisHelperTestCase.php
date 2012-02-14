<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';

class GenerisHelperTestCase extends UnitTestCase{
	
	protected $object;
	
	public function setUp(){
		TestRunner::initTest();
		$this->object = new helpers_GenerisHelper();
	}
	
	public function testGetsubClasses(){
		$clazzL;
		$clazzP;
		//Ontology creation
		//class + properties
		$propertiesUriArrayL = $this->object->createOntology("Local", array("Places", "Nom", "Disponible"), $clazzL); 
		$propertiesUriArrayP = $this->object->createOntology("Personne", array("Nom", "Prenom", "Age"), $clazzP);
		//$this->object->getSubClasses();
		//show the properties of the class in argument
		$this->object->getProperties($clazzL->uriResource);
		$this->object->getProperties($clazzP->uriResource);
		
		//create 2 instances of each type
		$instanceUri1 = $this->object->createInstance($clazzL, "E11");
		$instanceUri2 = $this->object->createInstance($clazzL, "E12");
		$instanceUri3 = $this->object->createInstance($clazzP, "Nico");
		$instanceUri4 = $this->object->createInstance($clazzP, "Yan");
		
		
		//create an array to assign a value to each parameters URI
		$i = 0;
		foreach($propertiesUriArrayL as $prop)
			$propertiesValue[$prop] = "test" . $i++;
		
		//Recover every instance of the class passed in argument
		//$this->object->getInstances($clazz->uriResource);
		
		//Assign a value to each parameter of the array
		$this->object->setValue($propertiesValue, $instanceUri1);
		
		$i = 10;
		foreach($propertiesUriArrayL as $prop)
			$propertiesValue[$prop] = "test" . $i++;
		
		$this->object->setValue($propertiesValue, $instanceUri2);
		
		$i = 20;
		foreach($propertiesUriArrayP as $prop)
			$propertiesValue[$prop] = "test" . $i++;
		
		$this->object->setValue($propertiesValue, $instanceUri3);
		
		$i = 30;
		foreach($propertiesUriArrayP as $prop)
			$propertiesValue[$prop] = "test" . $i++;
		
		$this->object->setValue($propertiesValue, $instanceUri4);
		
		
		//show the details of every instance of type personne
		$this->object->getInstancesDetails($clazzP);
		
		//show the details of every instance of type local
		$this->object->getInstancesDetails($clazzL);
		
		
		$this->object->listTriples($instanceUri1);
		$this->object->listTriples($instanceUri2);
		$this->object->listTriples($instanceUri3);
		$this->object->listTriples($instanceUri4);
		
		$this->object->describeInstance($instanceUri1);
		//delete the ontology, its instances and properties
		$this->object->deleteAllOntology($clazzP);
		
		$this->object->deleteAllOntology($clazzL);
	}
	
}
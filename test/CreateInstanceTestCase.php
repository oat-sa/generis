<?php


// require_once dirname(__FILE__).'/../common/common.php';
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';



/**
 * Test class for Class.
 * 
 * @author lionel.lecaque@tudor.lu
 * @package test
 */


class CreateInstanceTestCase extends UnitTestCase {
	protected $class;
	
	public function setUp(){

	    TestRunner::initTest();
	    $classres = core_kernel_classes_ResourceFactory::create(new core_kernel_classes_Class(RDF_CLASS), 'TestClass');
	    $this->class = new core_kernel_classes_Class($classres->getUri());
	    $this->assertIsA($this->class, 'core_kernel_classes_Class');
	    $this->assertTrue($this->class->hasType(new core_kernel_classes_Class(RDF_CLASS)));
	    common_Logger::i('using class '.$this->class->getLabel().' for Create instance Tests');
	}
	
	public function testCreateInstance(){
		$class = $this->class;
		$instance = $class->createInstance('toto' , 'tata');
		$this->assertEqual($instance->getLabel(), 'toto');
		$this->assertEqual($instance->getComment(), 'tata');
		$instance2 = $class->createInstance('toto' , 'tata');
		$this->assertNotIdentical($instance,$instance2);
		$instance->delete();
		$instance2->delete();
	}
	
	public function testCreateInstanceViaFactory(){
		$class = $this->class;
		$instance = core_kernel_classes_ResourceFactory::create($class, 'testlabel', 'testcomment');
		$this->assertTrue($instance->hasType($class));
		$this->assertEqual($instance->getLabel(), 'testlabel');
		$this->assertEqual($instance->getComment(), 'testcomment');
		$instance2 = core_kernel_classes_ResourceFactory::create($class);
		$this->assertTrue($instance2->hasType($class));
		$this->assertNotIdentical($instance,$instance2);
		$instance->delete();
		$instance2->delete();
	}
	
	public function testCreateInstanceWithProperties(){
		
		// simple case, without params

		$class		= $class = $this->class;
		$property	= new core_kernel_classes_Property(core_kernel_classes_ResourceFactory::create(new core_kernel_classes_Class(RDF_PROPERTY))->getUri());
		
		$instance = $class->createInstanceWithProperties(array());
		$this->assertTrue($instance->hasType($class));
		
		// simple literal properties
		$instance1 = $class->createInstanceWithProperties(array(
			RDFS_LABEL		=> 'testlabel',
			RDFS_COMMENT	=> 'testcomment'
		));
		
		$this->assertTrue($instance1->hasType($class));
		$this->assertEqual($instance1->getLabel(), 'testlabel');
		$this->assertEqual($instance1->getComment(), 'testcomment');
		
		// multiple literal properties
		$instance2 = $class->createInstanceWithProperties(array(
			RDFS_LABEL		=> 'testlabel',
			RDFS_COMMENT	=> array('testcomment1', 'testcomment2')
		));
		$this->assertTrue($instance2->hasType($class));
		$this->assertEqual($instance2->getLabel(), 'testlabel');
		$comments = $instance2->getPropertyValues(new core_kernel_classes_Property(RDFS_COMMENT));
		sort($comments);
		$this->assertEqual($comments, array('testcomment1', 'testcomment2'));
		
		// single ressource properties
		$propInst = core_kernel_classes_ResourceFactory::create($class);				
		$instance3 = $class->createInstanceWithProperties(array(
			RDFS_LABEL			=> 'testlabel',
			RDFS_COMMENT		=> 'testcomment',
			$property->getUri()	=> $propInst
		));
		$this->assertTrue($instance3->hasType($class));
		$this->assertEqual($instance3->getLabel(), 'testlabel');
		$propActual = $instance3->getUniquePropertyValue($property); // returns a ressource
		$this->assertEqual($propInst->getUri(), $propActual->getUri());
		
		// multiple ressource properties
		$propInst2 = core_kernel_classes_ResourceFactory::create($class);
		$instance4 = $class->createInstanceWithProperties(array(
			RDFS_LABEL			=> 'testlabel',
			RDFS_COMMENT		=> 'testcomment',
			$property->getUri()	=> array($propInst, $propInst2)
		));
		$this->assertTrue($instance4->hasType($class));
		$this->assertEqual($instance4->getLabel(), 'testlabel');
		
		$propActual = array_values($instance4->getPropertyValues($property));// returns uris
		$propNormative = array($propInst->getUri(), $propInst2->getUri());
		sort($propActual);
		sort($propNormative);
		$this->assertEqual($propActual, $propNormative);
		
		$instance->delete();
		$instance1->delete();
		$instance2->delete();
		
		$propInst->delete();
		$propInst2->delete();
		$property->delete();
		
	}
	
	public function testCreateInstanceHardified() {
		
		$rr = core_kernel_persistence_hardapi_ResourceReferencer::singleton();
		$this->assertFalse($rr->isClassReferenced($this->class));
		$softinstance = core_kernel_classes_ResourceFactory::create($this->class);
		$this->assertFalse($rr->isResourceReferenced($softinstance));
		
		$switcher = new core_kernel_persistence_Switcher();
		$switcher->hardify($this->class, array(
			'topClass'		=> $this->class,
		));
		
		$this->assertTrue($rr->isClassReferenced($this->class));
		common_Logger::i('creating hardified');
		$hardinstance = core_kernel_classes_ResourceFactory::create($this->class);
		$this->assertTrue($rr->isResourceReferenced($hardinstance), 'Instance created from harmode class was added in softmode');
		
		$this->testCreateInstance();
		$this->testCreateInstanceViaFactory();
		$this->testCreateInstanceWithProperties();
		
		$softinstance->delete();
		$hardinstance->delete();
		
		$switcher->unhardify($this->class);
	}
	
	public function after($pMethode) {
		$this->class->delete();
		parent::after($pMethode);
	}
}
?>
<?php


require_once dirname(__FILE__).'/../common/common.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';



/**
 * Test class for Class.
 * 
 * @author lionel.lecaque@tudor.lu
 * @package test
 */


class ClassTestCase extends UnitTestCase {
	protected $object;
	
	public function setUp(){

	    TestRunner::initTest();

		$this->object = new core_kernel_classes_Class(RDF_RESOURCE);
		$this->object->debug = __METHOD__;
	}

	public function testGetSubClasses(){

		$generisResource = new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE);
	
		$subClass0 = $generisResource->createSubClass('test0','test0 Comment');
		$subClass1 = $subClass0->createSubClass('test1','test1 Comment');

	
		$subClass2 = $subClass0->createSubClass('test2','test2 Comment');
		$subClass3 = $subClass2->createSubClass('test3','test3 Comment');
		$subClass4 = $subClass3->createSubClass('test4','test4 Comment');
		
		$subClassesArray = $subClass0->getSubClasses();
		foreach ( $subClassesArray as $subClass) {
			$this->assertTrue($subClass->isSubClassOf($subClass0));
		}
		
		$subClassesArray2 = $subClass0->getSubClasses(true);
		foreach ( $subClassesArray2 as $subClass) {
			if($subClass->getLabel() == 'test1'){
				$this->assertTrue($subClass->isSubClassOf($subClass0));
			}
			if($subClass->getLabel() == 'test2'){
				$this->assertTrue($subClass->isSubClassOf($subClass0));
			}
			if($subClass->getLabel() == 'test3'){
				$this->assertTrue($subClass->isSubClassOf($subClass2));
			}
			if($subClass->getLabel() == 'test4'){
				$this->assertTrue($subClass->isSubClassOf($subClass3));
				$this->assertTrue($subClass->isSubClassOf($subClass2));
				$this->assertFalse($subClass->isSubClassOf($subClass1));
			}
			
		}

		$subClass0->delete();
		$subClass1->delete();
		$subClass2->delete();
		$subClass3->delete();
		$subClass4->delete();
	}

	public function testGetParentClasses(){
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN);
		$indirectParentClasses = $class->getParentClasses(true);

		$this->assertTrue(count($indirectParentClasses) == 2);
		$expectedResult = array (CLASS_GENERIS_RESOURCE , RDF_RESOURCE);
		foreach ($indirectParentClasses  as $parentClass) {
			$this->assertIsA($parentClass,'core_kernel_classes_Class');	
			$this->assertTrue(in_array($parentClass->uriResource,$expectedResult));
		}
		
		$directParentClass = $class->getParentClasses(); 
		$this->assertTrue(count($directParentClass) == 1);
		foreach ($indirectParentClasses  as $parentClass) {
			$this->assertIsA($parentClass,'core_kernel_classes_Class');	
			$parentClass->uriResource = RDF_RESOURCE; 
		}

	}
	

	

	public function testGetProperties(){
		$list = new core_kernel_classes_Class(RDF_LIST);
		$properties = $list->getProperties();
		$this->assertTrue(count($properties) == 2);
		$expectedResult = array (	RDF_FIRST, RDF_REST);
	
		foreach ($properties as $property) {
			
			$this->assertTrue($property instanceof core_kernel_classes_Property);
			$this->assertTrue(in_array($property->uriResource,$expectedResult));
			if ($property->uriResource === RDF_FIRST) {
				$this->assertEqual($property->getRange()->uriResource, RDF_RESOURCE);
				$this->assertEqual($property->getLabel(),'first');
				$this->assertEqual($property->getComment(),'The first item in the subject RDF list.');		
			}
			if ($property->uriResource === RDF_REST) {
				$this->assertEqual($property->getRange()->uriResource, RDF_LIST);
				$this->assertEqual($property->getLabel(),'rest');
				$this->assertEqual($property->getComment(),'The rest of the subject RDF list after the first item.');		
			}
		}
		
		
		$class = $list->createSubClass('toto','toto');
		$properties2 = $class->getProperties(true);
		$this->assertFalse(empty($properties2));
		
		$class->delete();
	}

	

	
 	public function testGetInstances(){
 		$class = new core_kernel_classes_Class(CLASS_WIDGET,__METHOD__);
 		$plop = $class->createInstance('test','comment');
 		$instances = $class->getInstances();
		$subclass = $class->createSubClass('subTest Class', 'subTest Class');
		$subclassInstance = $subclass->createInstance('test3','comment3');
		

 		$this->assertTrue(count($instances)  > 0);

 		foreach ($instances as $k=>$instance) {
 			$this->assertTrue($instance instanceof core_kernel_classes_Resource );
 						
 			if ($instance->uriResource === WIDGET_COMBO) {
 				$this->assertEqual($instance->getLabel(),'Drop down menu' );
 				$this->assertEqual($instance->getComment(),'In drop down menu, one may select 1 to N options' );
 			}
 		 	if ($instance->uriResource === WIDGET_RADIO) {
 				$this->assertEqual($instance->getLabel(),'Radio button' );
 				$this->assertEqual($instance->getComment(),'In radio boxes, one may select exactly one option' );
 			}
 		 	if ($instance->uriResource === WIDGET_CHECK) {
 				$this->assertEqual($instance->getLabel(),'Check box' );
 				$this->assertEqual($instance->getComment(),'In check boxes, one may select 0 to N options' );
 			}
 		  	if ($instance->uriResource === WIDGET_FTE) {
 				$this->assertEqual($instance->getLabel(),'A Text Box' );
 				$this->assertEqual($instance->getComment(),'A particular text box' );
 			}
 			if ($instance->uriResource === $subclassInstance->uriResource){
 				$this->assertEqual($instance->getLabel(),'test3' );
 				$this->assertEqual($instance->getComment(),'comment3' );
 			}			
 		}
 		
 		$instances2 = $class->getInstances(true);
 		$this->assertTrue(count($instances2)  > 0);
 		foreach ($instances2 as $k=>$instance) {
 			$this->assertTrue($instance instanceof core_kernel_classes_Resource );		
 			if ($instance->uriResource === WIDGET_COMBO) {
 				$this->assertEqual($instance->getLabel(),'Drop down menu' );
 				$this->assertEqual($instance->getComment(),'In drop down menu, one may select 1 to N options' );
 			}
 		 	if ($instance->uriResource === WIDGET_RADIO) {
 				$this->assertEqual($instance->getLabel(),'Radio button' );
 				$this->assertEqual($instance->getComment(),'In radio boxes, one may select exactly one option' );
 			}
 		 	if ($instance->uriResource === WIDGET_CHECK) {
 				$this->assertEqual($instance->getLabel(),'Check box' );
 				$this->assertEqual($instance->getComment(),'In check boxes, one may select 0 to N options' );
 			}
 		  	if ($instance->uriResource === WIDGET_FTE) {
 				$this->assertEqual($instance->getLabel(),'A Text Box' );
 				$this->assertEqual($instance->getComment(),'A particular text box' );
 			}
 			if ($instance->uriResource === $plop->uriResource){
 				$this->assertEqual($instance->getLabel(),'test' );
 				$this->assertEqual($instance->getComment(),'comment' );
 			}	
			if ($instance->uriResource === $plop->uriResource){
 				$this->assertEqual($instance->getLabel(),'test' );
 				$this->assertEqual($instance->getComment(),'comment' );
 			}	
 			
 		}
 		
 		$plop->delete();
 		$subclass->delete();
 		$subclassInstance->delete();
 	}
 	
 	
 	
	public function testIsSubClassOf(){
		$class = new core_kernel_classes_Class(GENERIS_BOOLEAN);
		$subClass = $class->createSubClass('test', 'test'); 
		$this->assertTrue($class->isSubClassOf(new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE)));
		$this->assertTrue($subClass->isSubClassOf($class) );
		$this->assertFalse($subClass->isSubClassOf($subClass) );
		$this->assertTrue($subClass->isSubClassOf(new core_kernel_classes_Class(CLASS_GENERIS_RESOURCE)));
		$subClass->delete();

	}
	

	
	public function testSetSubClasseOf(){
		$class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#Boolean');
		$subClass = $class->createSubClass('test', 'test'); 
		$subClass1 = $subClass->createSubClass('subclass of test', 'subclass of test'); 
		$subClass2 = $subClass->createSubClass('subclass of test2', 'subclass of test2'); 
		
		$this->assertTrue($subClass->isSubClassOf($class) );
		$this->assertTrue($subClass1->isSubClassOf($class) );
		$this->assertTrue($subClass2->isSubClassOf($class) );
		
		$this->assertFalse($subClass2->isSubClassOf($subClass1) );
		$subClass2->setSubClassOf($subClass1);
		$this->assertTrue($subClass2->isSubClassOf($subClass1) );

		
		$subClass->delete();
		$subClass1->delete();
		$subClass2->delete();

	}

	public function testCreateInstance(){
		$class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#Boolean');
		$instance = $class->createInstance('toto' , 'tata');
		$this->assertEqual($instance->getLabel(), 'toto');
		$this->assertEqual($instance->getComment(), 'tata');
		$this->assertNotIdentical($instance,$class->createInstance('toto' , 'tata'));
		$instance->delete();
	}
	
	public function testCreateSubClass(){
		$class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#Boolean');
		$subClass = $class->createSubClass('toto' , 'tata');
		$this->assertNotEqual($class,$subClass);
		$this->assertEqual($subClass->getLabel(),'toto');
		$this->assertEqual($subClass->getComment(), 'tata');
		$subClassOfProperty = new core_kernel_classes_Property('http://www.w3.org/2000/01/rdf-schema#subClassOf');
		$subClassOfPropertyValue = $subClass->getPropertyValues($subClassOfProperty);
		$this->assertTrue(in_array($class->uriResource, array_values($subClassOfPropertyValue))); 
		$subClass->delete();
	}

	public function testCreateProperty(){
		$class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#Boolean');

		$property = $class->createProperty('tata','toto');
		$property2 = $class->createProperty('tata2','toto2',true);
		$this->assertTrue($property->getLabel() == 'tata');

		$this->assertTrue($property->getComment() == 'toto');
		$this->assertTrue($property2->isLgDependent());
		$this->assertTrue($property->getDomain()->get(0)->uriResource ==$class->uriResource );
		$property->delete();
		$property2->delete();

	}
	
	public function testSearchInstances(){
		
		$propertyClass = new core_kernel_classes_Class(RDF_PROPERTY);
		
		$propertyFilter = array(
			PROPERTY_IS_LG_DEPENDENT => GENERIS_TRUE
		);
		$options = array('like' => false, 'checkSubclasses' => false);
		$languagesDependantProp = $propertyClass->searchInstances($propertyFilter, $options);
		
		$found = count($languagesDependantProp);
		$this->assertTrue($found > 0);
		
		$propertyFilter = array(
			PROPERTY_IS_LG_DEPENDENT => GENERIS_TRUE,
			RDF_TYPE				 => RDF_PROPERTY
		);
		$languagesDependantProp = $propertyClass->searchInstances($propertyFilter, $options);
		$nfound = count($languagesDependantProp);
		$this->assertTrue($nfound > 0);
		$this->assertEqual($found, $nfound);
	}

	
}
?>
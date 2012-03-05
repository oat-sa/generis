<?php
error_reporting(E_ALL);
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class ApiModelTestCase extends UnitTestCase {
	protected $object;

	function __construct() {
    	parent::__construct();
    }
	
    /**
     * Setting the Api to test
     *
     */
    public function setUp(){
		TestRunner::initTest();
    	
		$this->object = core_kernel_impl_ApiModelOO::singleton();
		core_kernel_classes_DbWrapper::singleton()->dbConnector->debug=false;
	}
	
	public function testGetRootClass(){
		$session = core_kernel_classes_Session::singleton();
		$this->assertNotNull($session);
		$localModel = $session->getNameSpace();
		$this->assertFalse(empty($localModel));
		
		$rootClasses = $this->object->getRootClasses();
		$this->assertIsA($rootClasses,'common_Collection');
		$expectedResult = 	array( 	
			WIDGET_CONSTRAINT_TYPE,
			CLASS_WIDGET,
			RDF_RESOURCE
		);
		
		$pattern = "/^".preg_quote($localModel, '/')."/";
		foreach ($rootClasses->getIterator() as $rootClass) {
			
			$this->assertIsA($rootClass,'core_kernel_classes_Class');
			
			$parentClasses = $rootClass->getParentClasses(true);
			$this->assertEqual(count($parentClasses), 1);
			foreach($parentClasses as $uri => $parent){
				$this->assertEqual($uri,  RDF_CLASS);
			}
			//don't check the user root classes
			if(!preg_match($pattern, $rootClass->uriResource)){
				$this->assertTrue(in_array($rootClass->uriResource, $expectedResult));
			}
		}
	}
	
	public function testGetMetaClasses(){
		$metaClasses = $this->object->getMetaClasses();
		$this->assertIsA($metaClasses,'core_kernel_classes_ContainerCollection');
		$metaClass = $metaClasses->get(0);
		$this->assertIsA($metaClass,'core_kernel_classes_Class');
		$this->assertEqual($metaClass->uriResource,RDFS_DATATYPE);
		$this->assertEqual($metaClass->getLabel(),'Datatype');
		$this->assertEqual($metaClass->getComment(),'The class of RDF datatypes.');
	}
	
	public function testSetStatement(){
		$true = new core_kernel_classes_Resource(GENERIS_TRUE, __METHOD__);
		$predicate = RDFS_SEEALSO;
		$property = new core_kernel_classes_Property($predicate,__METHOD__); 
		$this->assertTrue($this->object->setStatement($true->uriResource,$predicate,'test', 'EN'), 
						  "setStatement should be able to set a value.");
		
		$values = $true->getPropertyValues($property);
		$this->assertTrue(count($values) > 0);
		
		$tripleFound = false;
		foreach ($values as $value) {
			if (!common_Utils::isUri($value) && $value == 'test') {
				$tripleFound = true;
				break;
			}
		}
		
		$this->assertTrue($tripleFound, "A property value for property " . $property->uriResource . 
										" should be found for resource " . $true->uriResource);
		
		$this->object->removeStatement($true->uriResource,$predicate,'test','EN');
	}
	
	public function testRemoveStatement(){
		$true = new core_kernel_classes_Resource(GENERIS_TRUE, __METHOD__);
		$predicate = RDFS_SEEALSO;
		$property = new core_kernel_classes_Property($predicate,__METHOD__); 
		$this->assertTrue($this->object->setStatement(GENERIS_TRUE,$predicate,'test', 'EN'));
		$remove = $this->object->removeStatement(GENERIS_TRUE,$predicate,'test','EN');
		$this->assertTrue($remove);
		$value = $true->getPropertyValuesCollection($property);
		$this->assertTrue($value->isEmpty());
	}
	
	public function testGetSubject(){
		$set = $this->object->getSubject(RDFS_LABEL , 'True');
		if($set instanceof core_kernel_classes_ContainerCollection) {
			$this->assertFalse($set->isEmpty());
			$found = false;
			foreach($set->getIterator() as $resource){
				if($resource->uriResource == GENERIS_TRUE){
					$found = true;
					break;
				}
			}
			$this->assertTrue($found);
		}
		else {
			$this->fail('GetSubject do not retrieve proper resource');
		}
	}

	public function testGetAllClasses(){
		$collection = $this->object->getAllClasses();
		$this->assertIsA($collection,'core_kernel_classes_ContainerCollection');
		foreach ($collection->getIterator() as $aClass) {
			$this->assertIsA($aClass,'core_kernel_classes_Class');
			if($aClass->uriResource === RDF_CLASS){
				$this->assertEqual($aClass->getLabel(),'Class');
				$this->assertEqual($aClass->getComment(),'The class of classes.');
			}
			if($aClass->uriResource === RDFS_STATEMENT){
				$this->assertEqual($aClass->getLabel(),'Statement');
				$this->assertEqual($aClass->getComment(), 'The class of RDF statements.');
			}
			if($aClass->uriResource === RDF_RESOURCE){
				$this->assertEqual($aClass->getLabel(),'Resource');
				$this->assertEqual($aClass->getComment(), 'The class resource, everything.');
			}
			if($aClass->uriResource ===  RDF_PROPERTY){
				$this->assertEqual($aClass->getLabel(),'Property');
				$this->assertEqual($aClass->getComment(), 'The class of RDF properties.');
			}
			if($aClass->uriResource ===  CLASS_GENERIS_RESOURCE){
				$this->assertEqual($aClass->getLabel(),'generis_Ressource');
				$this->assertEqual($aClass->getComment(), 'generis_Ressource');
			}
			if($aClass->uriResource ===  RDFS_DATATYPE){
				$this->assertEqual($aClass->getLabel(),'Datatype');
				$this->assertEqual($aClass->getComment(), 'The class of RDF datatypes.');
			}
		}
	}
}
?>
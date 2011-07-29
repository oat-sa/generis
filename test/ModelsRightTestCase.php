<?php
require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class ModelsRightTestCase extends UnitTestCase {
	
	public function setUp(){
	    TestRunner::initTest();
	}
	
	public function testRightModels(){
		// In tao context, the only one model which is updatable
		$updatableModels = core_kernel_classes_Session::singleton()->getUpdatableModels();
		$this->assertEqual(count($updatableModels), 1);
		$this->assertTrue(array_search(LOCAL_NAMESPACE, $updatableModels) !== false);
		
		// Try to delete a resource of a locked model
		$clazz = new core_kernel_classes_Class(RDF_RESOURCE);
		$this->assertFalse ($clazz->delete());
		
		// Try to remove a property of a locked model
		$property = new core_kernel_classes_Property(RDFS_LABEL);
		$this->assertFalse ($clazz->unsetProperty($property));
		
		// Try to remove a property value which is lg dependent of a locked model
		$clazz = new core_kernel_classes_Class('http://www.tao.lu/middleware/Rules.rdf#And');
		$this->assertFalse ($clazz->removePropertyValueByLg($property, 'EN'));
	}
}
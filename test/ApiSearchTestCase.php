<?php
error_reporting(E_ALL);
require_once dirname(__FILE__).'/../common/common.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class ApiSearchTestCase extends UnitTestCase {
	
	protected $api;

	function __construct() {
    	parent::__construct();
    }
	
    /**
     * Setting the Api to test
     *
     */
    public function setUp(){
		TestRunner::initTest();
		$this->api = new core_kernel_impl_ApiSearchI();
	}
	
	public function testInstance(){
		$this->assertIsA($this->api, 'core_kernel_impl_ApiSearchI');
		$this->assertIsA($this->api, 'core_kernel_impl_ApiI');
	}
	
	public function testSearchInstances(){
		
		$propertyClass = new core_kernel_classes_Class(RDF_PROPERTY);
		
		$propertyFilter = array(
			PROPERTY_IS_LG_DEPENDENT => GENERIS_TRUE
		);
		$options = array('like' => false, 'subclasses' => false);
		$languagesDependantProp = $this->api->searchInstances($propertyFilter, $propertyClass, $options);
		
		$found = count($languagesDependantProp);
		$this->assertTrue($found > 0);
		
		$propertyFilter = array(
			PROPERTY_IS_LG_DEPENDENT => GENERIS_TRUE,
			RDF_TYPE				 => RDF_PROPERTY
		);
		$languagesDependantProp = $this->api->searchInstances($propertyFilter, null, $options);
		$nfound = count($languagesDependantProp);
		$this->assertTrue($nfound > 0);
		$this->assertEqual($found, $nfound);
	}

	public function testFullTextSearch(){
		
		$result = $this->api->fullTextSearch("Is language dependent?");
		
		$this->assertIsA($result, 'core_kernel_classes_ContainerCollection');
		$this->assertTrue($result->count() == 1);
		$this->assertIsA($result->get(0), 'core_kernel_classes_Resource');
		$this->assertEqual($result->get(0)->uriResource, PROPERTY_IS_LG_DEPENDENT);
	}
}
?>
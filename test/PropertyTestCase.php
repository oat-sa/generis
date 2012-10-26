<?php
require_once dirname(__FILE__) . '/GenerisTestRunner.php';


class PropertyTestCase extends UnitTestCase{
	
	protected $object;
	
	public function setUp(){
        GenerisTestRunner::initTest();
		$this->object = new core_kernel_classes_Property(PROPERTY_WIDGET);
	}
	
	public function testGetDomain(){
		$domainCollection = $this->object->getDomain();
		$this->assertTrue($domainCollection instanceof core_kernel_classes_ContainerCollection  );
		$domain = $domainCollection->get(0);
		$this->assertEqual($domain->uriResource,RDF_PROPERTY);
		$this->assertEqual($domain->getLabel(),'Property');
		$this->assertEqual($domain->getComment(),'The class of RDF properties.');
	}
	
	public function testGetRange(){
		$range = $this->object->getRange();
		$this->assertTrue($range instanceof core_kernel_classes_Class );
		$this->assertEqual($range->uriResource,CLASS_WIDGET);
		$this->assertEqual($range->getLabel(), 'Widget Class');
		$this->assertEqual($range->getComment(), 'The class of all possible widgets');
	}
	
	public function testGetWidget(){
		$widget = $this->object->getWidget();
		$this->assertTrue($widget instanceof core_kernel_classes_Resource );
		$this->assertEqual($widget->uriResource,WIDGET_COMBO);
		$this->assertEqual($widget->getLabel(), 'Drop down menu');
		$this->assertEqual($widget->getComment(), 'In drop down menu, one may select 1 to N options');
	}	

}
?>
<?php

error_reporting(E_ALL);
require_once dirname(__FILE__).'/../../common/common.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';

class RdfListTestCase extends UnitTestCase {
	
	
	
	public function setUp(){
		core_kernel_impl_ApiModelOO::singleton()->logIn(LOGIN,md5(PASS),MODULE,true);
			}
	
	public function testAdd(){
		$rdfList = core_kernel_classes_RdfListFactory::create('Test Rdf List', 'Test RDF for dummies');
		$this->assertTrue($rdfList->add(new core_kernel_classes_Resource(GENERIS_TRUE)));
		$this->assertTrue($rdfList->add(new core_kernel_classes_Resource(GENERIS_FALSE)));
		$first = new core_kernel_classes_Property(RDF_FIRST);
		$rest = new core_kernel_classes_Property(RDF_REST);
		$result = $rdfList->getUniquePropertyValue($first);
		$this->assertEqual($result->uriResource,GENERIS_TRUE);
		$restInst = $rdfList->getUniquePropertyValue($rest);
		$this->assertIsA($restInst,core_kernel_classes_Resource);
		$this->assertEqual($restInst->getUniquePropertyValue($first)->uriResource,GENERIS_FALSE);
		$this->assertEqual($restInst->getUniquePropertyValue($rest)->uriResource,RDF_NIL);
		$rdfList->delete();
		
	}
	
	public function testGet(){
		$rdfList = core_kernel_classes_RdfListFactory::create('Test Rdf List', 'Test RDF for dummies');
		$rdfList->add(new core_kernel_classes_Resource(GENERIS_TRUE));
		$rdfList->add(new core_kernel_classes_Resource(GENERIS_FALSE));
		$this->assertEqual($rdfList->get(0)->uriResource,GENERIS_TRUE );
		$this->assertEqual($rdfList->get(1)->uriResource,GENERIS_FALSE );
		$this->assertNull($rdfList->get(2));	
		$rdfList->delete();

	}
	
	public function testCount(){
		$rdfList = core_kernel_classes_RdfListFactory::create('Test Rdf List', 'Test RDF for dummies');
		$this->assertEqual($rdfList->count(),0 );
		$rdfList->add(new core_kernel_classes_Resource(GENERIS_TRUE));
		$this->assertEqual($rdfList->count(),1 );
		$rdfList->add(new core_kernel_classes_Resource(GENERIS_FALSE));
		$this->assertEqual($rdfList->count(),2 );
		$rdfList->delete();

	}
	
	public function testGetCollection(){
		$rdfList = core_kernel_classes_RdfListFactory::create('Test Rdf List', 'Test RDF for dummies');
		$this->assertTrue($rdfList->getCollection()->isEmpty());
		$true = new core_kernel_classes_Resource(GENERIS_TRUE);
		$false = new core_kernel_classes_Resource(GENERIS_FALSE);
		$rdfList->add($true);
		$rdfList->add($false);
		$collection = $rdfList->getCollection();
		$this->assertFalse($collection->isEmpty());
		$this->assertIsA($collection,'common_Collection');
		$this->assertEqual($collection->indexOf($true), 0);
		$this->assertEqual($collection->indexOf($false), 1);	
		$this->assertEqual($collection->get(0)->uriResource,GENERIS_TRUE);
		$this->assertEqual($collection->get(1)->uriResource,GENERIS_FALSE);
		$rdfList->delete();
	}
	
	
	

}
?>
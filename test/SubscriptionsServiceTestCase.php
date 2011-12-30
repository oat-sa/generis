<?php
error_reporting(E_ALL);

require_once dirname(__FILE__).'/../common/common.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';

/**
 * Test class for Class.
 * 
 * @author lionel.lecaque@tudor.lu
 * @package test
 */


class SubscriptionsServiceTestCase extends UnitTestCase {

	private $subcriptionInst;
	private $maskInst;
	
    public function setUp(){
	     TestRunner::initTest();
	   
	}
	

	
//    public function testGetSubscriptions(){
//
//    	$subscptionClass = new core_kernel_classes_Class(CLASS_SUBCRIPTION);
//    	$this->subcriptionInst = $subscptionClass->createInstance('testSubcription','testSubcription');
//    	$maskClass = new core_kernel_classes_Class(CLASS_MASK);
//    	$this->maskInst = $maskClass->createInstance('testMask','testMask');
//    	$object = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAOItem.rdf#Item');
//    	$this->maskInst->setPropertyValue(new core_kernel_classes_Property(PROPERTY_MASK_PREDICATE),RDF_TYPE);
//		$this->maskInst->setPropertyValue(new core_kernel_classes_Property(PROPERTY_MASK_PREDICATE),RDFS_LABEL);
//    	$this->maskInst->setPropertyValue(new core_kernel_classes_Property(PROPERTY_MASK_OBJECT),$object->uriResource);
//    	$this->subcriptionInst->setPropertyValue(new core_kernel_classes_Property(PROPERTY_SUBCRIPTION_URL),'http://192.168.0.199/generis/');
//    	$this->subcriptionInst->setPropertyValue(new core_kernel_classes_Property(PROPERTY_SUBCRIPTION_MASK),$this->maskInst->uriResource);
//        $subcriptions = core_kernel_subscriptions_Service::singleton()->getSubscriptions(null,new core_kernel_classes_Property(RDF_TYPE),$object);
//
//       	$this->assertTrue(in_array($this->subcriptionInst->uriResource,$subcriptions));
//
//
//    }
//    
//    public function testGetInstancesFromSubscription(){
//    	$object = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAOItem.rdf#Item');
//        $instances = core_kernel_subscriptions_Service::singleton()->getInstancesFromSubscription($this->subcriptionInst,$object);
//		var_dump($instances);
//        $this->fail('not imp yet');
//    }
//    
//    public function testGetPropertyValuesFromSubscription(){
//    	$items = $this->maskInst->getPropertyValues(new core_kernel_classes_Property(PROPERTY_MASK_SUBJECT));
//    	$labelProp = new core_kernel_classes_Property(RDFS_LABEL);
//		foreach ($items as $item){
//			$resource = new core_kernel_classes_Resource($item);
//			$value = core_kernel_subscriptions_Service::singleton()->getPropertyValuesFromSubscription($this->subcriptionInst,$resource,$labelProp);
//		var_dump($value);
//		}
//		
//		
//        $this->fail('not imp yet');
//    }
    
    public function testGetInstances(){
    	return;
    	$itemClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOItem.rdf#Item');
    	$labelProp = new core_kernel_classes_Property(RDFS_LABEL);
    	$items = $itemClass->getInstances();
    	
    	foreach ($items as $item){
//    		var_dump($item->getPropertyValues($labelProp));
//    		var_dump($item->getPropertyValuesCollection($labelProp));
    	}
    	
    	$this->fail('not imp yet');
    }
    
//    public function testClean(){
//    	$this->subcriptionInst->delete();
//        $this->maskInst->delete();
//
//    }
    
}
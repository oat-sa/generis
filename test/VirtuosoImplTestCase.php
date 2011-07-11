<?php

require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class VirtuosoImplTestCase extends UnitTestCase {
        
        public function setUp(){
                TestRunner::initTest();
                core_kernel_persistence_PersistenceProxy::forceMode(PERSISTENCE_VIRTUOSO);
	}
        
        public function _testGetType(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                $types = $resource->getType();
                
                $this->assertFalse(empty($types));
                $theType = array_pop($types);
                $this->assertEqual($theType->uriResource, 'http://www.tao.lu/Ontologies/TAO.rdf#Languages');
        }
        
        public function _testGetLabel(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                $this->assertEqual($resource->getLabel(), 'EN');
        }
        
        public function _testGetPropertyValues(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                $property1 = new core_kernel_classes_Property('http://www.w3.org/1999/02/22-rdf-syntax-ns#type');
                $types = $resource->getPropertyValues($property1);
                
                $this->assertFalse(empty($types));
                $this->assertEqual($types[0], 'http://www.tao.lu/Ontologies/TAO.rdf#Languages');
                
                $property2 = new core_kernel_classes_Property('http://www.w3.org/1999/02/22-rdf-syntax-ns#value');
                $values = $resource->getPropertyValues($property2);
                $this->assertEqual($values[0], 'EN');
                
                $property3 = new core_kernel_classes_Property('http://www.w3.org/2000/01/rdf-schema#label');
                $values = $resource->getPropertyValues($property3);
                $this->assertEqual($values[0], 'EN');
                
                $label = $resource->getOnePropertyValue($property3);
                $this->assertIsA($label, 'core_kernel_classes_Literal');
                
                
        }
        
        public function _testPropertyValuesCollection(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                $property1 = new core_kernel_classes_Property('http://www.w3.org/1999/02/22-rdf-syntax-ns#type');
                $typesCollection = $resource->getPropertyValuesCollection($property1);
                $this->assertFalse($typesCollection->isEmpty());
                $this->assertEqual($typesCollection->count(), 1);
                
                foreach($typesCollection->getIterator() as $type){
                        $this->assertIsA($type, 'core_kernel_classes_Resource');
                        $this->assertEqual($type->uriResource, 'http://www.tao.lu/Ontologies/TAO.rdf#Languages');
                }
                
        }
        
        public function testSetGetPropertyValue(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                $property1 = new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOtestCase.rdf#Property1');
                $value1 = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAOtestCase.rdf#Resource1_'.time());
                
                $this->assertTrue($resource->setPropertyValue($property1, $value1->uriResource));
                $this->assertTrue($resource->removePropertyValues($property1));
                $this->assertEqual(count($resource->getPropertyValues($property1)), 0);
                
                //language dependent:
                $value2 = 'personal value EN '.date('d-m-Y H:i:s');
                $this->assertTrue($resource->setPropertyValueByLg($property1, $value2, 'EN'));
                $values = $resource->getPropertyValuesByLg($property1, 'EN');
                $this->assertEqual(count($values), 1);
                $this->assertEqual($values[0], $value2);
                
                $value3 = 'personal value DE '.date('d-m-Y H:i:s');
                $this->assertTrue($resource->setPropertyValueByLg($property1, $value3, 'DE'));
                $values = $resource->getPropertyValuesByLg($property1, 'DE');
                $this->assertEqual(count($values), 1);
                $this->assertEqual($values[0], $value3);
                
                $this->assertEqual(count($resource->getPropertyValues($property1)), 2);
                $this->assertTrue($resource->removePropertyValueBylg($property1, 'EN'));
                $this->assertEqual(count($resource->getPropertyValues($property1)), 1);
                $this->assertTrue($resource->removePropertyValues($property1));
                $this->assertEqual(count($resource->getPropertyValues($property1)), 0);
        }
        
}
?>

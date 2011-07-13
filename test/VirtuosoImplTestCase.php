<?php

require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class VirtuosoImplTestCase extends UnitTestCase {
        
        public function setUp(){
                TestRunner::initTest();
                core_kernel_persistence_PersistenceProxy::forceMode(PERSISTENCE_VIRTUOSO);
	}
        
        public function testGetType(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                $types = $resource->getType();
                
                $this->assertFalse(empty($types));
                $theType = array_pop($types);
                $this->assertEqual($theType->uriResource, 'http://www.tao.lu/Ontologies/TAO.rdf#Languages');
        }
        
        public function testGetLabel(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                $this->assertEqual($resource->getLabel(), 'EN');
        }
        
        public function testGetPropertyValues(){
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
        
        public function testPropertyValuesCollection(){
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
        
        public function testSetType(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                $class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#myLanguages');
                $this->assertTrue($resource->setType(new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#Languages')));
                
                //add type 'myLanguages':
                $this->assertTrue($resource->setType($class));
                $this->assertEqual(count($resource->getType()), 2);
                
                //remove type 'myLanguages'
                $this->assertTrue($resource->removeType($class));
                $this->assertEqual(count($resource->getType()), 1);
        }
        
        public function testSetPropertiesValues(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                
                $propertiesValues = array(
                    'http://www.tao.lu/Ontologies/TAOTestCase1.rdf#Prop1' => 'value@'.time(),
                    'http://www.tao.lu/Ontologies/TAOTestCase2.rdf#Prop2' => 'value2@'.time(),
                    'http://www.tao.lu/Ontologies/TAOTestCase1.rdf#Prop3' => 'http://www.tao.lu/Ontologies/TAOtestCase3.rdf#Value_'.time()
                );
                
                $this->assertTrue($resource->setPropertiesValues($propertiesValues));
                
                foreach($propertiesValues as $propUri => $val){
                        $this->assertTrue($resource->removePropertyValues(new core_kernel_classes_Property($propUri)));
                }
        }
        
        public function testGetRDFtriples(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                $this->assertFalse($resource->getRdfTriples()->isEmpty());
        }
        
        public function testDuplicate(){
                $resource = new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAO.rdf#LangEN');
                $clone = $resource->duplicate();
                
                $this->assertIsA($clone, 'core_kernel_classes_Resource');
                $this->assertEqual($clone->getLabel(), $resource->getLabel());
                $this->assertTrue($clone->delete());
        }
        
        public function testCreateCountInstances(){
                $class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#Languages');
                $count = $class->countInstances();
                $instances = $class->getInstances();
                
//                $this->assertEqual($count, 9);
                $this->assertEqual($count, count($instances));
        }
        
        public function testCreateInstances(){
                $class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#Languages');
                $count = $class->countInstances();
                $instances = $class->getInstances();
                
                $newLabel = 'newInstance';
                $newInstance =  $class->createInstance($newLabel, 'created for unit virtuoso test @ '.date('Y:i:s'));
                $this->assertIsA($newInstance, 'core_kernel_classes_Resource');
                $this->assertEqual($class->countInstances(), $count+1);
                $this->assertEqual($newLabel, $newInstance->getLabel());
                
                
                //delete it and count instances again:
                $this->assertTrue($newInstance->delete());
                $this->assertEqual($class->countInstances(), $count);
        }
        
        public function testCreateSubclass(){
                $class = new core_kernel_classes_Class(RDF_CLASS);
                $label = 'new subclass';
                $comment = 'created for unit virtuoso test @ '.date('Y:i:s');
                $subclass = $class->createSubClass($label, $comment);
                $this->assertIsA($subclass, 'core_kernel_classes_Class');
                
                $label2 = 'sub_'.$label;
                $subSubClass = $subclass->createSubClass($label2, $comment);
                $this->assertIsA($subSubClass, 'core_kernel_classes_Class');
                
                $foundSubclasses = $subclass->getSubClasses();
                $this->assertEqual(count($foundSubclasses), 1);
                
                //one identical triple allowed by language.
//                $this->assertTrue($subSubClass->setPropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTestCase1.rdf#Prop1'), $subclass->uriResource));
//                $this->assertTrue($subSubClass->setPropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTestCase1.rdf#Prop1'), $subclass->uriResource));
//                $this->assertTrue($subSubClass->setPropertyValue(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTestCase1.rdf#Prop1'), 'hello'));
//                $this->assertTrue($subSubClass->setPropertyValueByLg(new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOTestCase1.rdf#Prop1'), 'hello', 'EN'));
                
                $this->assertTrue($subSubClass->isSubClassOf($subclass));
                $parentClasses = $subSubClass->getParentClasses();
                $this->assertEqual(count($parentClasses), 1);
                $theParentClass = array_pop($parentClasses);
                $this->assertEqual($theParentClass->uriResource, $subclass->uriResource);
                
                $this->assertTrue($subSubClass->delete());
                $this->assertTrue($subclass->delete());
        }
        
        public function testGetProperties(){
                $class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#List');
                $this->assertEqual(count($class->getProperties()), 1);
        }
        
        public function testSetInstance(){
                
                $class = new core_kernel_classes_Class(RDF_CLASS);
                $label1 = 'new subclass 1';
                $label2 = 'new subclass 2';
                $comment = 'created for virtuoso unit test @ '.date('d-m-Y H:i:s');
                
                $subclass1 = $class->createSubClass($label1, $comment);
                $subclass2 = $class->createSubClass($label2, $comment);
                
                $this->assertIsA($subclass1, 'core_kernel_classes_Class');
                $this->assertIsA($subclass2, 'core_kernel_classes_Class');
                
                $label3 = 'new instance';
                $newInstance1 = $subclass1->createInstance($label3, $comment);
                
                $this->assertIsA($newInstance1, 'core_kernel_classes_Resource');
                $this->assertEqual($subclass1->countInstances(), 1);
                
                $newInstance2 = $subclass2->setInstance($newInstance1);
//                var_dump($newInstance1, $newInstance2, $subclass1, $subclass2);
                
                $this->assertIsA($newInstance1, 'core_kernel_classes_Resource');
                $this->assertEqual($subclass2->countInstances(), 1);
                $this->assertNotEqual($newInstance1->uriResource, $newInstance2->uriResource);
                $this->assertEqual($newInstance1->getLabel(), $newInstance2->getLabel());
                
                $this->assertTrue($newInstance1->delete());
                $this->assertTrue($newInstance2->delete());
                $this->assertTrue($subclass1->delete());
                $this->assertTrue($subclass2->delete());
        }
}
?>

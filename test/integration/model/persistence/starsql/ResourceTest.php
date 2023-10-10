<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA ;
 */

namespace oat\generis\test\integration\model\persistence\starsql;

use common_Utils;
use core_kernel_classes_Class;
use core_kernel_classes_Literal;
use core_kernel_persistence_starsql_Resource;
use oat\generis\model\data\Model;
use oat\generis\model\data\ModelManager;
use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyRdfs;
use oat\generis\model\WidgetRdf;
use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\generis\test\OntologyMockTrait;

/**
 * Test class for Class.
 *
 */
class ResourceTest extends GenerisPhpUnitTestRunner
{
    use OntologyMockTrait;

    protected $object;
    private Model $model;

    protected function setUp(): void
    {
        GenerisPhpUnitTestRunner::initTest();
        $this->model = ModelManager::getModel();
        $this->object = new core_kernel_persistence_starsql_Resource($this->model);

        //create test class
        $clazz = new core_kernel_classes_Class(GenerisRdf::CLASS_GENERIS_RESOURCE);
        $this->clazz = $clazz->createSubClass($clazz);
    }

    protected function tearDown(): void
    {
        $this->clazz->delete();
    }

    /*
        *
        * TOOLS FUNCTIONS
        *
        */

    private function createTestResource()
    {
        return $this->clazz->createInstance();
    }

    private function createTestProperty()
    {
        return $this->clazz->createProperty('ResourceTestCaseProperty ' . common_Utils::getNewUri());
    }

    /*
        *
        * TEST CASE FUNCTIONS
        *
        */

    public function testGetPropertiesValuesWithoutProperties()
    {
        $resource = $this->createTestResource();
        $this->assertEquals(0, count($this->object->getPropertiesValues($resource, [])));
    }

    public function testGetPropertiesValuesWithProperties()
    {
        $resource = $this->createTestResource();
        $property1 = $this->createTestProperty();
        $property2 = $this->createTestProperty();
        $property3 = $this->createTestProperty();

        $resource->setPropertyValue($property1, 'prop1');
        $resource->setPropertyValue($property2, 'prop2');
        $resource->setPropertyValue($property3, 'prop3');

        try {
            $this->object->getPropertiesValues($resource, [$property1]);
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

        //test if a property1 is stored
        $result = $this->object->getPropertiesValues($resource, [$property1]);
        $this->assertTrue(in_array(new core_kernel_classes_Literal('prop1'), $result[$property1->getUri()]));

        //test if a property2 is stored
        $result = $this->object->getPropertiesValues($resource, [$property2]);
        $this->assertTrue(in_array('prop2', $result[$property2->getUri()]));


        //test if three propierties is stored
        $result = $resource->getPropertiesValues([$property1, $property2, $property3]);
        $this->assertTrue(in_array('prop1', $result[$property1->getUri()]));
        $this->assertTrue(in_array('prop2', $result[$property2->getUri()]));
        $this->assertTrue(in_array('prop3', $result[$property3->getUri()]));

        //clean all
        $property1->delete();
        $property2->delete();
        $property3->delete();
        $resource->delete();
    }

    public function testGetParentClassesWithoutParentNoRecursive()
    {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $subClasses = $class->getParentClasses(false);
        $this->assertEquals(0, count($subClasses));
    }

    public function testGetParentClassesWithoutParentRecursively()
    {
        $class = new core_kernel_classes_Class(
            'http://www.tao.lu/Ontologies/generis.rdf#UserRole'
        );//TODO change by correct value
        $ancestors = $class->getParentClasses(false);
        $this->assertEquals(1, count($ancestors));
    }

    public function testGetParentClassesWithAParentNoRecursive()
    {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $subclass = $class->createSubClass('example', 'comment', 'tata.com');
        $ancestors = $subclass->getParentClasses(false);
        $this->assertEquals(1, count($ancestors));
    }


    public function testGetParentClassesWithAParentRecusively()
    {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $subclass = $class->createSubClass('example', 'comment', 'tata.com');
        $ancestors = $subclass->getParentClasses(true);
        $this->assertEquals(1, count($ancestors));
    }

    public function testSetPropertyValueLanguageIndependentNorRelationship()
    {
        $resource = $this->createTestResource();
        $property1 = $this->createTestProperty();

        $resource->setPropertyValue($property1, 'prop1');
        $result = $this->object->getPropertiesValues($resource, [$property1]);
        $this->assertTrue(in_array('prop1', $result[$property1->getUri()]));

        $resource->setPropertyValue($property1, 'prop1newvalue');
        $result = $this->object->getPropertiesValues($resource, [$property1]);
        $this->assertTrue(in_array('prop1newvalue', $result[$property1->getUri()]));
    }

    public function testSetPropertyValueLanguageDependent()
    {
        $resource = $this->createTestResource();
        $property1 = $this->createTestProperty();

        $resource->setPropertyValue($property1, 'prop1');
        $result = $this->object->getPropertiesValues($resource, [$property1]);
        $this->assertTrue(in_array('prop1', $result[$property1->getUri()]));

//
//        $resource = $this->createTestResource();
//        $property1 = $this->createTestProperty();
////        $property1->isMultiple();
//
//        $property1->setLgDependent(true);
//        $resource->setPropertyValue($property1, 'prop1');
//
//        $result = $this->object->getPropertiesValues($resource, [$property1]);
//        $this->assertTrue(in_array('prop1', $result[$property1->getUri()]));
        $resource->setPropertyValue($property1, 'prop1newvalue');
        $result = $this->object->getPropertiesValues($resource, [$property1]);
        $this->assertTrue(in_array('prop1newvalue', $result[$property1->getUri()]));
    }


    public function testSetPropertyValueIsRelationship()
    {
        $resource = $this->createTestResource();
        $property1 = $this->createTestProperty();
        $resource->setPropertyValue($property1, OntologyRdfs::RDFS_CLASS);
        $result = $this->object->getPropertiesValues($resource, [$property1]);
        $returnedResource = $result[$property1->getUri()][0];
        $this->assertEquals(OntologyRdfs::RDFS_CLASS, $returnedResource->getUri());
    }

    public function testRemovePropertyValue()
    {
        $resource = $this->createTestResource();
        $property1 = $this->createTestProperty();

        $resource->setPropertyValue($property1, 'prop1');
        $resource->removePropertyValues($property1);
        $result = $this->object->getPropertiesValues($resource, [$property1]);
        $this->assertFalse(in_array(new core_kernel_classes_Literal('prop1'), $result[$property1->getUri()]));
    }



    public function testRemovePropertyValueWithPattern()
    {
        $resource = $this->createTestResource();
        $property1 = $this->createTestProperty();

        $resource->setPropertyValue($property1, 'prop1');
        $resource->removePropertyValues($property1, ['pattern' => 'prop1']);
        $result = $this->object->getPropertiesValues($resource, [$property1]);
        $this->assertFalse(in_array(new core_kernel_classes_Literal('prop1'), $result[$property1->getUri()]));
    }

    public function testRemovePropertyValueWithPatternAndLikeFalse()
    {
        $resource = $this->createTestResource();
        $property1 = $this->createTestProperty();
        $resource->setPropertyValue($property1, 'prop1');
        $resource->removePropertyValues($property1, ['like' => false, 'pattern' => ['prop1']]);
        $result = $this->object->getPropertiesValues($resource, [$property1]);
        $this->assertFalse(in_array(new core_kernel_classes_Literal('prop1'), $result[$property1->getUri()]));
    }

    public function testRemovePropertyValueWithPatternAndLikeTrue()
    {
        $resource = $this->createTestResource();
        $property1 = $this->createTestProperty();
        $resource->setPropertyValue($property1, 'prop1');
        $resource->removePropertyValues($property1, ['like' => true, 'pattern' => ['*prop*']]);
        $result = $this->object->getPropertiesValues($resource, [$property1]);
        $this->assertFalse(in_array(new core_kernel_classes_Literal('prop1'), $result[$property1->getUri()]));
    }


    public function testRemovePropertyValueWithPatternLikeTrueUsingTwoPatternsAndBothOfThemHaveToBeMeet()
    {
        $resource = $this->createTestResource();
        $property1 = $this->createTestProperty();
        $resource->setPropertyValue($property1, 'prop1');
        $resource->removePropertyValues($property1, ['like' => true, 'pattern' => [['pr*'], ['*1']]]);
        $result = $this->object->getPropertiesValues($resource, [$property1]);
        $this->assertFalse(in_array(new core_kernel_classes_Literal('prop1'), $result[$property1->getUri()]));
    }

    public function testRemovePropertyValueWithPatternLikeTrueUsingTwoPatternsWhichkkkOneOfThemHasToBeMeet()
    {
        $resource = $this->createTestResource();
        $property1 = $this->createTestProperty();
        $resource->setPropertyValue($property1, 'prop1');
        $resource->removePropertyValues($property1, ['like' => true, 'pattern' => [['pr*', '*1'], ['*o*']]]);
        $result = $this->object->getPropertiesValues($resource, [$property1]);
        $this->assertFalse(in_array(new core_kernel_classes_Literal('prop1'), $result[$property1->getUri()]));
    }

    public function testRemovePropertyValueWithPatternLikeFalseAndTwoValues1()
    {
        $resource = $this->createTestResource();
        $property1 = $this->createTestProperty();
        $resource->setPropertyValue($property1, 'prop1');
        $resource->removePropertyValues($property1, ['like' => false, 'pattern' => [['prop1', 'prop2']]]);
        $result = $this->object->getPropertiesValues($resource, [$property1]);
        $this->assertFalse(in_array(new core_kernel_classes_Literal('prop1'), $result[$property1->getUri()]));
    }

    public function testRemovePropertyValueWithPatternLikeTrueAndTwoValuesOneOfThemDifferentThanProperty()
    {
        $resource = $this->createTestResource();
        $property1 = $this->createTestProperty();
        $resource->setPropertyValue($property1, 'prop1');
        $resource->removePropertyValues($property1, ['like' => true, 'pattern' => [['prop1'], ['4']]]);
        $result = $this->object->getPropertiesValues($resource, [$property1]);

        $this->assertTrue(in_array(new core_kernel_classes_Literal('prop1'), $result[$property1->getUri()]));
    }

    public function testRemovePropertyValueWithPatternLikeFalseAndTwoValuesWithAnOr()
    {
        $resource = $this->createTestResource();
        $property1 = $this->createTestProperty();
        $resource->setPropertyValue($property1, 'prop1');
        $resource->removePropertyValues(
            $property1,
            ['like' => false, 'pattern' => [['prop1', 'prop2'], ['prop1', 'prop4']]]
        );
        $result = $this->object->getPropertiesValues($resource, [$property1]);
        $this->assertFalse(in_array(new core_kernel_classes_Literal('prop1'), $result[$property1->getUri()]));
    }

    public function testRemovePropertyValueWithPatternLikeFalseAndTwoValuesAndTwoSetOfValues()
    {
        $resource = $this->createTestResource();
        $property1 = $this->createTestProperty();
        $resource->setPropertyValue($property1, 'prop1');
        $resource->removePropertyValues($property1, ['like' => false, 'pattern' => [['prop1', 'value2'], ['prop1']]]);
        $result = $this->object->getPropertiesValues($resource, [$property1]);
        $this->assertFalse(in_array(new core_kernel_classes_Literal('prop1'), $result[$property1->getUri()]));
    }


    //TODO Duplicate last functions with like true


}

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
use core_kernel_classes_Property;
use core_kernel_classes_Resource;
use core_kernel_persistence_starsql_Resource;
use oat\generis\model\data\Model;
use oat\generis\model\data\ModelManager;
use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyRdfs;
use oat\generis\model\WidgetRdf;
use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\generis\test\OntologyMockTrait;

class ResourceTest extends GenerisPhpUnitTestRunner
{
    use OntologyMockTrait;

    protected core_kernel_persistence_starsql_Resource $object;
    private Model $model;
    private ?core_kernel_classes_Class $class;

    public function testGetPropertiesValuesWithoutProperties()
    {
        $resource = $this->createTestResource();

        $this->assertCount(0, $this->object->getPropertiesValues($resource, []));
    }

    private function createTestResource(): core_kernel_classes_Resource
    {
        return $this->class->createInstance();
    }

    public function testGetPropertiesValuesWithProperties()
    {
        $resource = $this->createTestResource();

        $properties = [
            'prop1' => $this->createTestProperty(),
            'prop2' => $this->createTestProperty(),
            'prop3' => $this->createTestProperty(),
        ];

        foreach ($properties as $label => $property) {
            $resource->setPropertyValue($property, $label);
        }

        $propertiesValues = $resource->getPropertiesValues(array_values($properties));

        foreach ($properties as $label => $property) {
            $this->assertTrue(in_array($label, $propertiesValues[$property->getUri()]));
        }
    }

    private function createTestProperty(): core_kernel_classes_Property
    {
        return $this->class->createProperty('ResourceTestCaseProperty ' . common_Utils::getNewUri());
    }

    public function testGetParentClassesWithoutParentNoRecursive()
    {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $subClasses = $class->getParentClasses();

        $this->assertCount(0, $subClasses);
    }

    public function testGetParentClassesWithoutParentRecursively()
    {
        $class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#UserRole');
        $ancestors = $class->getParentClasses();

        $this->assertCount(1, $ancestors);
    }

    public function testGetParentClassesWithAParentNoRecursive()
    {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $subclass = $class->createSubClass('example', 'comment', 'example.com');
        $ancestors = $subclass->getParentClasses();

        $this->assertCount(1, $ancestors);
        $this->assertEquals(WidgetRdf::CLASS_URI_WIDGET, $ancestors[WidgetRdf::CLASS_URI_WIDGET]->getUri());
    }

    public function testGetParentClassesWithAParentRecursively()
    {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $subclass = $class->createSubClass('example', 'comment', 'example.com');
        $ancestors = $subclass->getParentClasses(true);

        $this->assertCount(1, $ancestors);
    }

    public function testSetPropertyValueLanguageIndependentNorRelationship()
    {
        $resource = $this->createTestResource();
        $property = $this->createTestProperty();

        $resource->setPropertyValue($property, 'prop1');
        $propertiesValues = $this->object->getPropertiesValues($resource, [$property]);
        $this->assertTrue(in_array('prop1', $propertiesValues[$property->getUri()]));

        $resource->setPropertyValue($property, 'prop1newvalue');
        $propertiesValues = $this->object->getPropertiesValues($resource, [$property]);
        $this->assertTrue(in_array('prop1newvalue', $propertiesValues[$property->getUri()]));
    }

    public function testSetPropertyValueLanguageDependent()
    {
        $resource = $this->createTestResource();
        $property1 = $this->createTestProperty();

        $property1->setLgDependent(true);
        $resource->setPropertyValue($property1, 'prop2');
        $propertiesValues = $this->object->getPropertiesValues($resource, [$property1]);
        $this->assertTrue(in_array('prop2', $propertiesValues[$property1->getUri()]));

        $resource->setPropertyValue($property1, 'prop2newvalue');
        $propertiesValues = $this->object->getPropertiesValues($resource, [$property1]);
        $this->assertTrue(in_array('prop2newvalue', $propertiesValues[$property1->getUri()]));
    }

    public function testSetPropertyValueIsRelationship()
    {
        $resource = $this->createTestResource();
        $property = $this->createTestProperty();

        $resource->setPropertyValue($property, OntologyRdfs::RDFS_CLASS);
        $propertiesValues = $this->object->getPropertiesValues($resource, [$property]);
        $returnedResource = $propertiesValues[$property->getUri()][0];

        $this->assertEquals(OntologyRdfs::RDFS_CLASS, $returnedResource->getUri());
    }

    public function testRemovePropertyValue()
    {
        $resource = $this->createTestResource();
        $property = $this->createTestProperty();

        $resource->setPropertyValue($property, 'prop1');
        $resource->removePropertyValues($property);
        $result = $this->object->getPropertiesValues($resource, [$property]);

        $this->assertFalse(in_array(new core_kernel_classes_Literal('prop1'), $result[$property->getUri()]));
    }

    public function testRemovePropertyValueWithPattern()
    {
        $resource = $this->createTestResource();
        $property = $this->createTestProperty();

        $resource->setPropertyValue($property, 'prop1');
        $resource->removePropertyValues($property, ['pattern' => 'prop1']);
        $propertiesValues = $this->object->getPropertiesValues($resource, [$property]);

        $this->assertFalse(in_array(new core_kernel_classes_Literal('prop1'), $propertiesValues[$property->getUri()]));
    }

    public function testRemovePropertyValueWithPatternAndLikeFalse()
    {
        $resource = $this->createTestResource();
        $property = $this->createTestProperty();
        $resource->setPropertyValue($property, 'prop1');
        $resource->removePropertyValues($property, ['like' => false, 'pattern' => ['prop1']]);
        $propertiesValues = $this->object->getPropertiesValues($resource, [$property]);

        $this->assertFalse(in_array(new core_kernel_classes_Literal('prop1'), $propertiesValues[$property->getUri()]));
    }

    public function testRemovePropertyValueWithPatternAndLikeTrue()
    {
        $resource = $this->createTestResource();
        $property = $this->createTestProperty();
        $resource->setPropertyValue($property, 'prop1');
        $resource->removePropertyValues($property, ['like' => true, 'pattern' => ['*prop*']]);
        $propertiesValues = $this->object->getPropertiesValues($resource, [$property]);

        $this->assertFalse(in_array(new core_kernel_classes_Literal('prop1'), $propertiesValues[$property->getUri()]));
    }

    public function testRemovePropertyValueWithPatternLikeTrueUsingTwoPatternsAndBothOfThemHaveToBeMeet()
    {
        $resource = $this->createTestResource();
        $property = $this->createTestProperty();
        $resource->setPropertyValue($property, 'prop1');
        $resource->removePropertyValues($property, ['like' => true, 'pattern' => ['pr*', '*1']]);
        $result = $this->object->getPropertiesValues($resource, [$property]);

        $this->assertFalse(in_array(new core_kernel_classes_Literal('prop1'), $result[$property->getUri()]));
    }

    public function testRemovePropertyValueWithPatternLikeTrueUsingThreePatternsTwoOfThemHaveToBeMeetOtherOptional()
    {
        $resource = $this->createTestResource();
        $property = $this->createTestProperty();
        $resource->setPropertyValue($property, 'prop1');
        $resource->removePropertyValues(
            $property,
            ['like' => true, 'pattern' => ['pr*', 'notnecesarypattern', '*o*']]
        );
        $propertiesValues = $this->object->getPropertiesValues($resource, [$property]);

        $this->assertFalse(in_array(new core_kernel_classes_Literal('prop1'), $propertiesValues[$property->getUri()]));
    }

    public function testRemovePropertyValueWithPatternLikeFalseAndTwoPatternsWhichOnlyOneHasToBeMeet()
    {
        $resource = $this->createTestResource();
        $property = $this->createTestProperty();
        $resource->setPropertyValue($property, 'prop1');
        $resource->removePropertyValues($property, ['like' => false, 'pattern' => ['prop1', 'prop2']]);
        $propertiesValues = $this->object->getPropertiesValues($resource, [$property]);

        $this->assertFalse(in_array(new core_kernel_classes_Literal('prop1'), $propertiesValues[$property->getUri()]));
    }

    public function testRemovePropertyValueWithPatternLikeFalseAndTwoPatternsWhichOnlyOneHas()
    {
        $resource = $this->createTestResource();
        $property = $this->createTestProperty();
        $resource->setPropertyValue($property, 'prop1');
        $resource->removePropertyValues($property, ['like' => false, 'pattern' => ['prop1', 'prop2']]);
        $propertiesValues = $this->object->getPropertiesValues($resource, [$property]);

        $this->assertFalse(in_array(new core_kernel_classes_Literal('prop1'), $propertiesValues[$property->getUri()]));
    }

    //---------------------END TESTS----------------------

    protected function setUp(): void
    {
        GenerisPhpUnitTestRunner::initTest();
        $this->model = ModelManager::getModel();
        $this->object = new core_kernel_persistence_starsql_Resource($this->model);

        $class = new core_kernel_classes_Class(GenerisRdf::CLASS_GENERIS_RESOURCE);
        $this->class = $class->createSubClass();
    }

    protected function tearDown(): void
    {
        if ($this->class !== null) {
            $this->class->delete();
        }
    }
}

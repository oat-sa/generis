<?php

/*
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung
 *                         (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor
 *                         (under the project TAO-SUSTAIN & TAO-DEV);
 *               2017 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyRdf;
use oat\generis\model\OntologyRdfs;
use oat\generis\model\WidgetRdf;
use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\generis\test\OntologyMockTrait;

/**
 * Test class for Class.
 *
 * @author lionel.lecaque@tudor.lu
 * @package test
 */

class ClassTest extends GenerisPhpUnitTestRunner
{
    use OntologyMockTrait;

    protected $object;

    protected function setUp(): void
    {

        GenerisPhpUnitTestRunner::initTest();
        $ontologyModel = $this->getOntologyMock();

        $this->object = new core_kernel_classes_Class(OntologyRdfs::RDFS_RESOURCE);
        $this->object->debug = __METHOD__;
    }

    public function testGetSubClasses()
    {

        $generisResource = new core_kernel_classes_Class(GenerisRdf::CLASS_GENERIS_RESOURCE);

        $subClass0 = $generisResource->createSubClass('test0', 'test0 Comment');
        $subClass1 = $subClass0->createSubClass('test1', 'test1 Comment');


        $subClass2 = $subClass0->createSubClass('test2', 'test2 Comment');
        $subClass3 = $subClass2->createSubClass('test3', 'test3 Comment');
        $subClass4 = $subClass3->createSubClass('test4', 'test4 Comment');

        $subClassesArray = $subClass0->getSubClasses();
        foreach ($subClassesArray as $subClass) {
            $this->assertTrue($subClass->isSubClassOf($subClass0));
        }

        $subClassesArray2 = $subClass0->getSubClasses(true);
        foreach ($subClassesArray2 as $subClass) {
            if ($subClass->getLabel() == 'test1') {
                $this->assertTrue($subClass->isSubClassOf($subClass0));
            }
            if ($subClass->getLabel() == 'test2') {
                $this->assertTrue($subClass->isSubClassOf($subClass0));
            }
            if ($subClass->getLabel() == 'test3') {
                $this->assertTrue($subClass->isSubClassOf($subClass2));
            }
            if ($subClass->getLabel() == 'test4') {
                $this->assertTrue($subClass->isSubClassOf($subClass3));
                $this->assertTrue($subClass->isSubClassOf($subClass2));
                $this->assertFalse($subClass->isSubClassOf($subClass1));
            }
        }

        $subClass0->delete();
        $subClass1->delete();
        $subClass2->delete();
        $subClass3->delete();
        $subClass4->delete();
    }

    public function testGetParentClasses()
    {
        $class = new core_kernel_classes_Class(GenerisRdf::GENERIS_BOOLEAN);
        $indirectParentClasses = $class->getParentClasses(true);

        $this->assertEquals(2, count($indirectParentClasses));
        $expectedResult =  [GenerisRdf::CLASS_GENERIS_RESOURCE , OntologyRdfs::RDFS_RESOURCE];
        foreach ($indirectParentClasses as $parentClass) {
            $this->assertInstanceOf('core_kernel_classes_Class', $parentClass);
            $this->assertTrue(in_array($parentClass->getUri(), $expectedResult));
        }

        $directParentClass = $class->getParentClasses();
        $this->assertEquals(1, count($directParentClass));
        foreach ($directParentClass as $parentClass) {
            $this->assertInstanceOf('core_kernel_classes_Class', $parentClass);
            $this->assertEquals(GenerisRdf::CLASS_GENERIS_RESOURCE, $parentClass->getUri());
        }
    }




    public function testGetProperties()
    {
        $list = new core_kernel_classes_Class(OntologyRdf::RDF_LIST);
        $properties = $list->getProperties();
        $this->assertCount(2, $properties);
        $expectedResult =  [   OntologyRdf::RDF_FIRST, OntologyRdf::RDF_REST];

        foreach ($properties as $property) {
            $this->assertTrue($property instanceof core_kernel_classes_Property);
            $this->assertTrue(in_array($property->getUri(), $expectedResult));
            if ($property->getUri() === OntologyRdf::RDF_FIRST) {
                $this->assertEquals($property->getRange()->getUri(), OntologyRdfs::RDFS_RESOURCE);
                $this->assertEquals($property->getLabel(), 'first');
                $this->assertEquals($property->getComment(), 'The first item in the subject RDF list.');
            }
            if ($property->getUri() === OntologyRdf::RDF_REST) {
                $this->assertEquals($property->getRange()->getUri(), OntologyRdf::RDF_LIST);
                $this->assertEquals($property->getLabel(), 'rest');
                $this->assertEquals($property->getComment(), 'The rest of the subject RDF list after the first item.');
            }
        }


        $class = $list->createSubClass('toto', 'toto');
        $properties2 = $class->getProperties(true);
        $this->assertFalse(empty($properties2));

        $class->delete();
    }




    public function testGetInstances()
    {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $plop = $class->createInstance('test', 'comment');
        $instances = $class->getInstances();
        $subclass = $class->createSubClass('subTest Class', 'subTest Class');
        $subclassInstance = $subclass->createInstance('test3', 'comment3');


        $this->assertTrue(count($instances)  > 0);

        foreach ($instances as $k => $instance) {
            $this->assertTrue($instance instanceof core_kernel_classes_Resource);

            if ($instance->getUri() === 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ComboBox') {
                $this->assertEquals($instance->getLabel(), 'Drop down menu');
                $this->assertEquals($instance->getComment(), 'In drop down menu, one may select 1 to N options');
            }
            if ($instance->getUri() === 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#RadioBox') {
                $this->assertEquals($instance->getLabel(), 'Radio button');
                $this->assertEquals($instance->getComment(), 'In radio boxes, one may select exactly one option');
            }
            if ($instance->getUri() === 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#CheckBox') {
                $this->assertEquals($instance->getLabel(), 'Check box');
                $this->assertEquals($instance->getComment(), 'In check boxes, one may select 0 to N options');
            }
            if ($instance->getUri() === 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox') {
                $this->assertEquals($instance->getLabel(), 'A Text Box');
                $this->assertEquals($instance->getComment(), 'A particular text box');
            }
            if ($instance->getUri() === $subclassInstance->getUri()) {
                $this->assertEquals($instance->getLabel(), 'test3');
                $this->assertEquals($instance->getComment(), 'comment3');
            }
        }

        $instances2 = $class->getInstances(true);
        $this->assertTrue(count($instances2)  > 0);
        foreach ($instances2 as $k => $instance) {
            $this->assertTrue($instance instanceof core_kernel_classes_Resource);
            if ($instance->getUri() === 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ComboBox') {
                $this->assertEquals($instance->getLabel(), 'Drop down menu');
                $this->assertEquals($instance->getComment(), 'In drop down menu, one may select 1 to N options');
            }
            if ($instance->getUri() === 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#RadioBox') {
                $this->assertEquals($instance->getLabel(), 'Radio button');
                $this->assertEquals($instance->getComment(), 'In radio boxes, one may select exactly one option');
            }
            if ($instance->getUri() === 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#CheckBox') {
                $this->assertEquals($instance->getLabel(), 'Check box');
                $this->assertEquals($instance->getComment(), 'In check boxes, one may select 0 to N options');
            }
            if ($instance->getUri() === 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox') {
                $this->assertEquals($instance->getLabel(), 'A Text Box');
                $this->assertEquals($instance->getComment(), 'A particular text box');
            }
            if ($instance->getUri() === $plop->getUri()) {
                $this->assertEquals($instance->getLabel(), 'test');
                $this->assertEquals($instance->getComment(), 'comment');
            }
            if ($instance->getUri() === $plop->getUri()) {
                $this->assertEquals($instance->getLabel(), 'test');
                $this->assertEquals($instance->getComment(), 'comment');
            }
        }

        $plop->delete();
        $subclass->delete();
        $subclassInstance->delete();
    }



    public function testIsSubClassOf()
    {
        $class = new core_kernel_classes_Class(GenerisRdf::GENERIS_BOOLEAN);
        $subClass = $class->createSubClass('test', 'test');
        $this->assertTrue($class->isSubClassOf(new core_kernel_classes_Class(GenerisRdf::CLASS_GENERIS_RESOURCE)));
        $this->assertTrue($subClass->isSubClassOf($class));
        $this->assertFalse($subClass->isSubClassOf($subClass));
        $this->assertTrue($subClass->isSubClassOf(new core_kernel_classes_Class(GenerisRdf::CLASS_GENERIS_RESOURCE)));
        $subClass->delete();
    }

    public function testGetCountInstances()
    {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $sub1Class = $class->createSubClass('subTest Class', 'subTest Class');
        $sub1Class->createInstance('test', 'comment');
        $subclass2 = $sub1Class->createSubClass('subTest Class 2', 'subTest Class 2');
        $subclass2->createInstance('test3', 'comment3');

        $this->assertEquals(1, $sub1Class->countInstances());
        $this->assertEquals(2, $sub1Class->countInstances([], ['recursive' => true]));
    }

    public function testSetSubClasseOf()
    {
        $class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#Boolean');
        $subClass = $class->createSubClass('test', 'test');
        $subClass1 = $subClass->createSubClass('subclass of test', 'subclass of test');
        $subClass2 = $subClass->createSubClass('subclass of test2', 'subclass of test2');

        $this->assertTrue($subClass->isSubClassOf($class));
        $this->assertTrue($subClass1->isSubClassOf($class));
        $this->assertTrue($subClass2->isSubClassOf($class));

        $this->assertFalse($subClass2->isSubClassOf($subClass1));
        $subClass2->setSubClassOf($subClass1);
        $this->assertTrue($subClass2->isSubClassOf($subClass1));


        $subClass->delete();
        $subClass1->delete();
        $subClass2->delete();
    }

    public function testCreateInstance()
    {
        $class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#Boolean');
        $instance = $class->createInstance('toto', 'tata');
        $this->assertEquals($instance->getLabel(), 'toto');
        $this->assertEquals($instance->getComment(), 'tata');
        $instance2 = $class->createInstance('toto', 'tata');
        $this->assertNotSame($instance, $instance2);
        $instance->delete();
        $instance2->delete();
    }

    public function testCreateSubClass()
    {
        $class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#Boolean');
        $subClass = $class->createSubClass('toto', 'tata');
        $this->assertNotEquals($class, $subClass);
        $this->assertEquals($subClass->getLabel(), 'toto');
        $this->assertEquals($subClass->getComment(), 'tata');
        $subClassOfProperty = new core_kernel_classes_Property('http://www.w3.org/2000/01/rdf-schema#subClassOf');
        $subClassOfPropertyValue = $subClass->getPropertyValues($subClassOfProperty);
        $this->assertTrue(in_array($class->getUri(), array_values($subClassOfPropertyValue)));
        $subClass->delete();
    }

    public function testCreateProperty()
    {
        $class = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/generis.rdf#Boolean');

        $property = $class->createProperty('tata', 'toto');
        $property2 = $class->createProperty('tata2', 'toto2', true);
        $this->assertTrue($property->getLabel() == 'tata');

        $this->assertTrue($property->getComment() == 'toto');
        $this->assertTrue($property2->isLgDependent());
        $this->assertTrue($property->getDomain()->get(0)->getUri() == $class->getUri());
        $property->delete();
        $property2->delete();
    }

        /**
         * @group SearchInstances
         */
    public function testSearchInstances()
    {

        $instance = $this->getMockForAbstractClass(
            \core_kernel_classes_Class::class,
            [],
            '',
            false,
            false,
            true,
            ['getImplementation']
        );

        $mockResult = new \ArrayIterator([1,2,3,4,5,6]);

        $propertyFilter = [
            GenerisRdf::PROPERTY_IS_LG_DEPENDENT => GenerisRdf::GENERIS_TRUE
        ];

        $options = ['like' => false, 'recursive' => false];

        $prophetImplementation = $this->prophesize(\core_kernel_persistence_smoothsql_Class::class);

        $prophetImplementation
                ->searchInstances($instance, $propertyFilter, $options)
                ->willReturn($mockResult);

        $ImplementationMock = $prophetImplementation->reveal();

        $instance->expects($this->once())->method('getImplementation')->willReturn($ImplementationMock);
        $this->assertSame([1,2,3,4,5,6], $instance->searchInstances($propertyFilter, $options));
    }

    //Test search instances with a model shared between smooth and hard implentation
    public function testSearchInstancesMultipleImpl()
    {
        $clazz = new core_kernel_classes_Class(OntologyRdfs::RDFS_CLASS);
        $sub1Clazz = $clazz->createSubClass();
        $sub1ClazzInstance = $sub1Clazz->createInstance('test case instance');
        $sub2Clazz = $sub1Clazz->createSubClass();
        $sub2ClazzInstance = $sub2Clazz->createInstance('second test case instance');
        $sub3Clazz = $sub2Clazz->createSubClass();
        $sub3ClazzInstance = $sub3Clazz->createInstance('test case instance 3');

        $options = [
            'recursive'             => true,
            'append'                => true,
            'createForeigns'                => true,
            'referencesAllTypes'            => true,
            'rmSources'             => true
        ];

        //Test the search instances on the smooth impl
        $propertyFilter = [
            OntologyRdfs::RDFS_LABEL => 'test case instance'
        ];
        $instances = $clazz->searchInstances($propertyFilter, ['recursive' => true]);
        $this->assertTrue(array_key_exists($sub1ClazzInstance->getUri(), $instances));
        $this->assertTrue(array_key_exists($sub2ClazzInstance->getUri(), $instances));
        $this->assertTrue(array_key_exists($sub3ClazzInstance->getUri(), $instances));

        //clean data test
        foreach ($sub1Clazz->getInstances(true) as $instance) {
            $instance->delete();
        }
        $sub1Clazz->delete(true);
        $sub2Clazz->delete(true);
        $sub3Clazz->delete(true);
    }

    public function testSearchInstancesWithOrder()
    {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $subClass = $class->createSubClass();
        $sub1ClassInstance = $subClass->createInstance('test case instance');
        $sub2ClassInstance = $subClass->createInstance('second test case instance');
        $sub3ClassInstance = $subClass->createInstance('test case instance 3');

        $instances = $class->searchInstances(
            [
                OntologyRdfs::RDFS_LABEL => 'test case instance'
            ],
            [
                'recursive' => true,
                'order' => OntologyRdfs::RDFS_LABEL,
                'orderdir' => 'DESC',
                'limit' => 1,
                'offset' => 1,
            ]
        );

        $this->assertCount(1, $instances);
        $this->assertArrayHasKey($sub1ClassInstance->getUri(), $instances);
    }

    public function dataProviderSearchInstancesWithRegularExpressions(): iterable
    {
        yield 'case-insensitive match' => [
            'correctLabel' => 'test Case Instance With dot',
            'incorrectLabel' => 'test case instance without dot',
            'searchCriterion' => 'instance with dot',
        ];

        yield 'dot escape' => [
            'correctLabel' => 'test case instance with d.t',
            'incorrectLabel' => 'test case instance with dot',
            'searchCriterion' => 'instance with d.t',
        ];

        yield 'star in the beginning' => [
            'correctLabel' => 'test case instance with',
            'incorrectLabel' => 'test case instance without star',
            'searchCriterion' => '*instance with',
        ];

        yield 'star in the end' => [
            'correctLabel' => 'test case instance with',
            'incorrectLabel' => 'incorrect test case instance with',
            'searchCriterion' => 'test case instance*',
        ];

        yield 'star in the middle' => [
            'correctLabel' => 'test case instance with',
            'incorrectLabel' => 'incorrect test case instance without star',
            'searchCriterion' => 'test*with',
        ];

        yield 'percent in the beginning' => [
            'correctLabel' => 'test case instance with',
            'incorrectLabel' => 'test case wrong instance with',
            'searchCriterion' => '%case instance',
        ];

        yield 'percent in the end' => [
            'correctLabel' => 'test case instance with',
            'incorrectLabel' => 'test case wrong instance with',
            'searchCriterion' => 'case instance%',
        ];

        yield 'percent in the middle' => [
            'correctLabel' => 'test case instance with star',
            'incorrectLabel' => 'test instance without star',
            'searchCriterion' => 'case%with',
        ];

        yield 'multiple percents in the middle' => [
            'correctLabel' => 'test case instance with star',
            'incorrectLabel' => 'test instance without star',
            'searchCriterion' => 'test%case%with',
        ];

        yield 'both percent and star present' => [
            'correctLabel' => 'test case instance with',
            'incorrectLabel' => 'test instance without star',
            'searchCriterion' => '*case%with',
        ];

        yield 'underscore is present' => [
            'correctLabel' => 'test case instance with underscore symbol',
            'incorrectLabel' => 'test case instance without underscore symbol',
            'searchCriterion' => 'instance with under_core',
        ];

        yield 'escaped wildcard symbols' => [
            'correctLabel' => 'test case instance w_th %pecial %ymbols',
            'incorrectLabel' => 'test case instance with special ymbols',
            'searchCriterion' => 'w\_th \%pecial \%ymbols',
        ];
    }

    /**
     * @dataProvider dataProviderSearchInstancesWithRegularExpressions
     *
     * @param string $correctLabel
     * @param string $incorrectLabel
     * @param string $searchCriterion
     */
    public function testSearchInstancesWithRegularExpressions(
        string $correctLabel,
        string $incorrectLabel,
        string $searchCriterion
    ) {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $subClass = $class->createSubClass();
        $incorrectInstance = $subClass->createInstance($incorrectLabel);
        $correctInstance = $subClass->createInstance($correctLabel);

        $instances = $subClass->searchInstances(
            [
                OntologyRdfs::RDFS_LABEL => $searchCriterion
            ],
            [
                'recursive' => false,
                'like' => true,
            ]
        );

        $this->assertCount(1, $instances);
        $this->assertArrayHasKey($correctInstance->getUri(), $instances);
    }

    public function testSearchInstancesLanguageSpecific()
    {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $labelProperty = new \core_kernel_classes_Property(OntologyRdfs::RDFS_LABEL);
        $sub1Class = $class->createSubClass();
        $sub1ClassInstance = $sub1Class->createInstance('test case instance'); //en-US
        $sub1ClassInstance->setPropertyValueByLg($labelProperty, 'instance de cas de test', 'fr-FR');
        $sub1ClassInstance->setPropertyValueByLg($labelProperty, 'Testfallinstanz', 'de-DE');

        $sub2Class = $sub1Class->createSubClass();
        $sub2ClassInstance = $sub2Class->createInstance('second test case instance'); //en-US
        $sub2ClassInstance->setPropertyValueByLg($labelProperty, 'deuxième instance de cas de test', 'fr-FR');
        $sub2ClassInstance->setPropertyValueByLg($labelProperty, 'zweite Testfallinstanz', 'de-DE');

        $sub3Class = $sub2Class->createSubClass();
        $sub3ClassInstance = $sub3Class->createInstance('test case instance 3'); //en-US
        $sub3ClassInstance->setPropertyValueByLg($labelProperty, 'exemple de cas de test 3', 'fr-FR');
        $sub3ClassInstance->setPropertyValueByLg($labelProperty, 'Testfallinstanz 3', 'de-DE');

        $instances = $sub1Class->searchInstances(
            [
                OntologyRdfs::RDFS_LABEL => 'Testfallinstanz'
            ],
            [
                'recursive' => true,
                'order' => OntologyRdfs::RDFS_LABEL,
                'orderdir' => 'DESC',
                'lang' => 'de-DE'
            ]
        );

        $this->assertCount(3, $instances);
        $this->assertEquals($sub2ClassInstance->getUri(), array_shift($instances)->getUri());
        $this->assertEquals($sub3ClassInstance->getUri(), array_shift($instances)->getUri());
        $this->assertEquals($sub1ClassInstance->getUri(), array_shift($instances)->getUri());
    }

    //Test the function getInstancesPropertyValues of the class Class with literal properties
    public function testGetInstancesPropertyValuesWithLiteralProperties()
    {
        // create a class
        $class = new core_kernel_classes_Class(GenerisRdf::CLASS_GENERIS_RESOURCE);
        $subClass = $class->createSubClass('GetInstancesPropertyValuesClass', 'GetInstancesPropertyValues_Class');
        // create a first property for this class
        $p1 = core_kernel_classes_ClassFactory::createProperty(
            $subClass,
            'GetInstancesPropertyValues_Property1',
            'GetInstancesPropertyValues_Property1',
            false,
            LOCAL_NAMESPACE . "#P1"
        );
        $p1->setRange(new core_kernel_classes_Class(OntologyRdfs::RDFS_LITERAL));
        // create a second property for this class
        $p2 = core_kernel_classes_ClassFactory::createProperty(
            $subClass,
            'GetInstancesPropertyValues_Property2',
            'GetInstancesPropertyValues_Property2',
            false,
            LOCAL_NAMESPACE . "#P2"
        );
        $p2->setRange(new core_kernel_classes_Class(OntologyRdfs::RDFS_LITERAL));
        // create a second property for this class
        $p3 = core_kernel_classes_ClassFactory::createProperty(
            $subClass,
            'GetInstancesPropertyValues_Property3',
            'GetInstancesPropertyValues_Property3',
            false,
            LOCAL_NAMESPACE . "#P3"
        );
        $p2->setRange(new core_kernel_classes_Class(OntologyRdfs::RDFS_LITERAL));
        // $i1
        $i1 = $subClass->createInstance("i1", "i1");
        $i1->setPropertyValue($p1, "p11 litteral");
        $i1->setPropertyValue($p2, "p21 litteral");
        $i1->setPropertyValue($p3, "p31 litteral");
        $i1->getLabel();
        // $i2
        $i2 = $subClass->createInstance("i2", "i2");
        $i2->setPropertyValue($p1, "p11 litteral");
        $i2->setPropertyValue($p2, "p22 litteral");
        $i2->setPropertyValue($p3, "p31 litteral");
        $i2->getLabel();

        // Search * P1 for P1=P11 litteral
        // Expected 2 results, but 1 possibility
        $propertyFilters =  [
            LOCAL_NAMESPACE . "#P1" => "p11 litteral"
        ];
        $result = $subClass->getInstancesPropertyValues($p1, $propertyFilters);
        $this->assertEquals(count($result), 2);
        $this->assertTrue(in_array("p11 litteral", $result));

        // Search * P1 for P1=P11 litteral WITH DISTINCT options
        // Expected 1 results, and 1 possibility
        $propertyFilters =  [
            LOCAL_NAMESPACE . "#P1" => "p11 litteral"
        ];
        $result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, ["distinct" => true]);
        $this->assertEquals(count($result), 1);
        $this->assertTrue(in_array("p11 litteral", $result));

        // Search * P2 for P1=P11 litteral WITH DISTINCT options
        // Expected 2 results, and 2 possibilities
        $propertyFilters =  [
            LOCAL_NAMESPACE . "#P1" => "p11 litteral"
        ];
        $result = $subClass->getInstancesPropertyValues($p2, $propertyFilters, ["distinct" => true]);
        $this->assertEquals(count($result), 2);
        $this->assertTrue(in_array("p21 litteral", $result));
        $this->assertTrue(in_array("p22 litteral", $result));

        // Search * P2 for P1=P12 litteral WITH DISTINCT options
        // Expected 0 results, and 0 possibilities
        $propertyFilters =  [
            LOCAL_NAMESPACE . "#P1" => "p12 litteral"
        ];
        $result = $subClass->getInstancesPropertyValues($p2, $propertyFilters, ["distinct" => true]);
        $this->assertEquals(count($result), 0);

        // Search * P1 for P2=P21 litteral WITH DISTINCT options
        // Expected 1 results, and 1 possibilities
        $propertyFilters =  [
            LOCAL_NAMESPACE . "#P2" => "p21 litteral"
        ];
        $result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, ["distinct" => true]);
        $this->assertEquals(count($result), 1);
        $this->assertTrue(in_array("p11 litteral", $result));

        // Search * P1 for P2=P22 litteral WITH DISTINCT options
        // Expected 1 results, and 1 possibilities
        $propertyFilters =  [
            LOCAL_NAMESPACE . "#P2" => "p22 litteral"
        ];
        $result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, ["distinct" => true]);
        $this->assertEquals(count($result), 1);
        $this->assertTrue(in_array("p11 litteral", $result));

        // Search * P3 for P1=P11 & P2=P21 litteral WITH DISTINCT options
        // Expected 1 results, and 1 possibilities
        $propertyFilters =  [
            LOCAL_NAMESPACE . "#P1" => "p11 litteral"
            , LOCAL_NAMESPACE . "#P2" => "p21 litteral"
        ];
        $result = $subClass->getInstancesPropertyValues($p3, $propertyFilters, ["distinct" => true]);
        $this->assertEquals(count($result), 1);
        $this->assertTrue(in_array("p31 litteral", $result));

        // Search * P2 for P1=P11 & P3=P31 litteral WITH DISTINCT options
        // Expected 2 results, and 2 possibilities
        $propertyFilters =  [
            LOCAL_NAMESPACE . "#P1" => "p11 litteral"
            , LOCAL_NAMESPACE . "#P3" => "p31 litteral"
        ];
        $result = $subClass->getInstancesPropertyValues($p2, $propertyFilters, ["distinct" => true]);
        $this->assertEquals(count($result), 2);
        $this->assertTrue(in_array("p21 litteral", $result));
        $this->assertTrue(in_array("p22 litteral", $result));

        // Clean the model
        $i1->delete();
        $i2->delete();

        $p1->delete();
        $p2->delete();
        $p3->delete();

        $subClass->delete();
    }

    public function testGetInstancesPropertyValuesLanguageSpecific()
    {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $subClass = $class->createSubClass('GetInstancesPropertyValuesClass', 'GetInstancesPropertyValues_Class');
        $p1 = \core_kernel_classes_ClassFactory::createProperty(
            $subClass,
            'GetInstancesPropertyValues_Property1',
            'GetInstancesPropertyValues_Property1',
            true,
            LOCAL_NAMESPACE . "#PLG1"
        );
        $p1->setRange(new core_kernel_classes_Class(OntologyRdfs::RDFS_LITERAL));

        // $i1
        $i1 = $subClass->createInstance("i1", "i1");
        $i1->setPropertyValue($p1, "p11 litteral");
        $i1->setPropertyValueByLg($p1, "p11 littéral", 'fr-FR');
        // $i2
        $i2 = $subClass->createInstance("i2", "i2");
        $i2->setPropertyValue($p1, "p11 litteral");
        $i2->setPropertyValueByLg($p1, "p11 littéral", 'fr-FR');

        $propertyFilters =  [
            $p1->getUri() => "p11 littéral"
        ];
        $result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, ['lang' => 'fr-FR']);
        $this->assertCount(2, $result);
        $this->assertTrue(in_array("p11 littéral", $result));

        $propertyFilters =  [
            $p1->getUri() => "p11 littéral"
        ];
        $result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, ["distinct" => true, 'lang' => 'fr-FR']);
        $this->assertCount(1, $result);
        $this->assertTrue(in_array("p11 littéral", $result));

        $propertyFilters =  [
            $p1->getUri() => "p11 littéral"
        ];
        $result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, ["distinct" => true, 'lang' => 'en-US']);
        $this->assertCount(0, $result);
    }

    //Test the function getInstancesPropertyValues of the class Class  with resource properties
    public function testGetInstancesPropertyValuesWithResourceProperties()
    {
        // create a class
        $class = new core_kernel_classes_Class(GenerisRdf::CLASS_GENERIS_RESOURCE);
        $subClass = $class->createSubClass('GetInstancesPropertyValuesClass', 'GetInstancesPropertyValues_Class');
        // create a first property for this class
        $p1 = core_kernel_classes_ClassFactory::createProperty(
            $subClass,
            'GetInstancesPropertyValues_Property1',
            'GetInstancesPropertyValues_Property1',
            false,
            LOCAL_NAMESPACE . "#P1"
        );
        $p1->setRange(new core_kernel_classes_Class(GenerisRdf::GENERIS_BOOLEAN));
        // create a second property for this class
        $p2 = core_kernel_classes_ClassFactory::createProperty(
            $subClass,
            'GetInstancesPropertyValues_Property2',
            'GetInstancesPropertyValues_Property2',
            false,
            LOCAL_NAMESPACE . "#P2"
        );
        $p1->setRange(new core_kernel_classes_Class(GenerisRdf::GENERIS_BOOLEAN));
        // create a second property for this class
        $p3 = core_kernel_classes_ClassFactory::createProperty(
            $subClass,
            'GetInstancesPropertyValues_Property3',
            'GetInstancesPropertyValues_Property3',
            false,
            LOCAL_NAMESPACE . "#P3"
        );
        $p1->setRange(new core_kernel_classes_Class(OntologyRdfs::RDFS_LITERAL));
        // $i1
        $i1 = $subClass->createInstance("i1", "i1");
        $i1->setPropertyValue($p1, GenerisRdf::GENERIS_TRUE);
        $i1->setPropertyValue($p2, new core_kernel_classes_Class(GenerisRdf::GENERIS_TRUE));
        $i1->setPropertyValue($p3, "p31 litteral");
        $i1->getLabel();
        // $i2
        $i2 = $subClass->createInstance("i2", "i2");
        $i2->setPropertyValue($p1, GenerisRdf::GENERIS_TRUE);
        $i2->setPropertyValue($p2, new core_kernel_classes_Class(GenerisRdf::GENERIS_FALSE));
        $i2->setPropertyValue($p3, "p31 litteral");
        $i2->getLabel();

        // Search * P1 for P1=GenerisRdf::GENERIS_TRUE
        // Expected 2 results, but 1 possibility
        $propertyFilters =  [
            LOCAL_NAMESPACE . "#P1" => GenerisRdf::GENERIS_TRUE
        ];
        $result = $subClass->getInstancesPropertyValues($p1, $propertyFilters);
        $this->assertEquals(count($result), 2);
        foreach ($result as $property) {
            $this->assertTrue($property->getUri() == GenerisRdf::GENERIS_TRUE);
        }
        // Search * P1 for P1=GenerisRdf::GENERIS_TRUE WITH DISTINCT options
        // Expected 1 results, and 1 possibility
        $propertyFilters =  [
            LOCAL_NAMESPACE . "#P1" => GenerisRdf::GENERIS_TRUE
        ];
        $result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, ["distinct" => true]);
        $this->assertEquals(count($result), 1);
        $this->assertTrue($result[0]->getUri() == GenerisRdf::GENERIS_TRUE);

        // Search * P2 for P1=GenerisRdf::GENERIS_TRUE WITH DISTINCT options
        // Expected 2 results, and 2 possibilities
        $propertyFilters =  [
            LOCAL_NAMESPACE . "#P1" => GenerisRdf::GENERIS_TRUE
        ];
        $result = $subClass->getInstancesPropertyValues($p2, $propertyFilters, ["distinct" => true]);
        $this->assertEquals(count($result), 2);
        foreach ($result as $property) {
            $this->assertTrue(
                $property->getUri() == GenerisRdf::GENERIS_TRUE || $property->getUri() == GenerisRdf::GENERIS_FALSE
            );
        }

        // Search * P2 for P1=NotExistingProperty litteral WITH DISTINCT options
        // Expected 1 results, and 1 possibilities
        $propertyFilters =  [
            LOCAL_NAMESPACE . "#P1" => "NotExistingProperty"
        ];
        $result = $subClass->getInstancesPropertyValues($p2, $propertyFilters, ["distinct" => true]);
        $this->assertEquals(count($result), 0);

        // Search * P1 for P2=GenerisRdf::GENERIS_TRUE litteral WITH DISTINCT options
        // Expected 1 results, and 1 possibilities
        $propertyFilters =  [
            LOCAL_NAMESPACE . "#P2" => GenerisRdf::GENERIS_TRUE
        ];
        $result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, ["distinct" => true]);
        $this->assertEquals(count($result), 1);
        $this->assertTrue($result[0]->getUri() == GenerisRdf::GENERIS_TRUE);

        // Search * P1 for P2=GenerisRdf::GENERIS_FALSE WITH DISTINCT options
        // Expected 1 results, and 1 possibilities
        $propertyFilters =  [
            LOCAL_NAMESPACE . "#P2" => GenerisRdf::GENERIS_FALSE
        ];
        $result = $subClass->getInstancesPropertyValues($p1, $propertyFilters, ["distinct" => true]);
        $this->assertEquals(count($result), 1);
        $this->assertTrue($result[0]->getUri() == GenerisRdf::GENERIS_TRUE);

        // Search * P3 for P1=GenerisRdf::GENERIS_TRUE & P2=GenerisRdf::GENERIS_TRUE litteral WITH DISTINCT options
        // Expected 1 results, and 1 possibilities
        $propertyFilters =  [
            LOCAL_NAMESPACE . "#P1" => GenerisRdf::GENERIS_TRUE
            , LOCAL_NAMESPACE . "#P2" => GenerisRdf::GENERIS_TRUE
        ];
        $result = $subClass->getInstancesPropertyValues($p3, $propertyFilters, ["distinct" => true]);
        $this->assertEquals(count($result), 1);
        $this->assertTrue(in_array("p31 litteral", $result));

        // Search * P2 for P1=P11 & P3=P31 litteral WITH DISTINCT options
        // Expected 2 results, and 2 possibilities
        $propertyFilters =  [
            LOCAL_NAMESPACE . "#P1" => GenerisRdf::GENERIS_TRUE
            , LOCAL_NAMESPACE . "#P3" => "p31 litteral"
        ];
        $result = $subClass->getInstancesPropertyValues($p2, $propertyFilters, ["distinct" => true]);
        $this->assertEquals(count($result), 2);
        foreach ($result as $property) {
            $this->assertTrue(
                $property->getUri() == GenerisRdf::GENERIS_TRUE || $property->getUri() == GenerisRdf::GENERIS_FALSE
            );
        }

        // Clean the model
        $i1->delete();
        $i2->delete();

        $p1->delete();
        $p2->delete();
        $p3->delete();

        $subClass->delete();
    }

    public function testDeleteInstances()
    {
        $taoClass = new core_kernel_classes_Class(GenerisRdf::CLASS_GENERIS_RESOURCE);
        $creativeWorkClass = $taoClass->createSubClass('Creative Work');
        $authorProperty = $taoClass->createProperty('Author');
        $relatedWorksProperty = $creativeWorkClass->createProperty('Related Works');

        $bookLotr = $creativeWorkClass->createInstance('The Lord of The Rings (book)');
        $bookLotr->setPropertyValue($authorProperty, 'J.R.R. Tolkien');

        $movieLotr = $creativeWorkClass->createInstance('The Lord of The Rings (movie)');
        $movieLotr->setPropertyValue($authorProperty, 'Peter Jackson');

        $movieLotr->setPropertyValue($relatedWorksProperty, $bookLotr);
        $bookLotr->setPropertyValue($relatedWorksProperty, $movieLotr);

        $movieMinorityReport = $creativeWorkClass->createInstance('Minority Report');
        $movieMinorityReport->setPropertyValue($authorProperty, 'Steven Spielberg');

        $this->assertEquals(count($creativeWorkClass->getInstances()), 3);

        // delete the LOTR movie with its references.
        $relatedWorks = $bookLotr->getPropertyValuesCollection($relatedWorksProperty);
        $this->assertEquals($relatedWorks->sequence[0]->getLabel(), 'The Lord of The Rings (movie)');
        $creativeWorkClass->deleteInstances([$movieLotr], true);
        $relatedWorks = $bookLotr->getPropertyValues($relatedWorksProperty);
        $this->assertEquals(count($relatedWorks), 0);

        // Only 1 resource deleted ?
        $this->assertFalse($movieLotr->exists());
        $this->assertTrue($bookLotr->exists());
        $this->assertTrue($movieMinorityReport->exists());

        // Remove the rest.
        $creativeWorkClass->deleteInstances([$bookLotr, $movieMinorityReport]);
        $this->assertEquals(count($creativeWorkClass->getInstances()), 0);
        $this->assertFalse($bookLotr->exists());
        $this->assertFalse($movieMinorityReport->exists());

        $this->assertTrue($authorProperty->delete());
        $this->assertTrue($relatedWorksProperty->delete());

        $creativeWorkClass->delete(true);
    }
}

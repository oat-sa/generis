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

use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use oat\generis\model\data\Model;
use oat\generis\model\data\ModelManager;
use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyRdf;
use oat\generis\model\OntologyRdfs;
use oat\generis\model\resource\Repository\ClassRepository;
use oat\generis\model\WidgetRdf;
use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\generis\test\OntologyMockTrait;

/**
 * Test class for Class.
 *
 */
class ClassTest extends GenerisPhpUnitTestRunner
{
    use OntologyMockTrait;

    protected $object;
    private Model $oldModel;

    /** @var string[] */
    private array $cleanupList = [
        WidgetRdf::CLASS_URI_WIDGET,
        OntologyRdf::RDF_PROPERTY,

    ];

    protected function setUp(): void
    {
        GenerisPhpUnitTestRunner::initTest();

        $this->oldModel = ModelManager::getModel();
        $ontologyModel = $this->getStarSqlMock();
        ModelManager::setModel($ontologyModel);

        $this->object = new core_kernel_classes_Class(OntologyRdfs::RDFS_RESOURCE);
        $this->object->debug = __METHOD__;
    }

    protected function tearDown(): void
    {
        /** @var ClassRepository $classRepo */
        foreach ($this->cleanupList as $classUri) {
            $class = new \core_kernel_classes_Class($classUri);
            $instances = $class->searchInstances(
                [
                    'http://www.tao.lu/Ontologies/TAO.rdf#UpdatedBy' => LOCAL_NAMESPACE . 'virtualTestUser'
                ],
                ['recursive' => true, 'like' => false]
            );

            $class->deleteInstances($instances, true);
        }

        ModelManager::setModel($this->oldModel);
    }


    public function testGetInstances()
    {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $plop = $class->createInstance('test', 'comment');
        $instances = $class->getInstances();
        $subclass = $class->createSubClass('subTest Class', 'subTest Class');
        $subclassInstance = $subclass->createInstance('test3', 'comment3');

        $this->assertGreaterThan(0, count($instances));

        $expectedItems = [
            'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ComboBox' => [
                'label' => 'Drop down menu',
                'comment' => 'In drop down menu, one may select 1 to N options',
            ],
            'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#RadioBox' => [
                'label' => 'Radio button',
                'comment' => 'In radio boxes, one may select exactly one option',
            ],
            'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#CheckBox' => [
                'label' => 'Check box',
                'comment' => 'In check boxes, one may select 0 to N options',
            ],
            'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox' => [
                'label' => 'A Text Box',
                'comment' => 'A particular text box',
            ],
            $plop->getUri() => [
                'label' => 'test',
                'comment' => 'comment',
            ],
        ];

        foreach (array_intersect_key($instances, $expectedItems) as $instance) {
            $this->assertTrue($instance instanceof core_kernel_classes_Resource);
            $this->assertEquals($instance->getLabel(), $expectedItems[$instance->getUri()]['label']);
            $this->assertEquals($instance->getComment(), $expectedItems[$instance->getUri()]['comment']);
        }

        $instances2 = $class->getInstances(true);
        $this->assertTrue(count($instances2) > 0);

        $expectedItems[$subclassInstance->getUri()] = [
            'label' => 'test3',
            'comment' => 'comment3',
        ];

        foreach (array_intersect_key($instances2, $expectedItems) as $instance) {
            $this->assertTrue($instance instanceof core_kernel_classes_Resource);
            $this->assertEquals($instance->getLabel(), $expectedItems[$instance->getUri()]['label']);
            $this->assertEquals($instance->getComment(), $expectedItems[$instance->getUri()]['comment']);
        }
    }

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

        $implementationMock = $prophetImplementation->reveal();

        $instance->expects($this->once())->method('getImplementation')->willReturn($implementationMock);
        $this->assertSame([1,2,3,4,5,6], $instance->searchInstances($propertyFilter, $options));
    }

    public function testSearchInstancesMultipleImpl()
    {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $sub1Class = $class->createSubClass();
        $sub1ClassInstance = $sub1Class->createInstance('test case instance');
        $sub2Class = $sub1Class->createSubClass();
        $sub2ClassInstance = $sub2Class->createInstance('second test case instance');
        $sub3Class = $sub2Class->createSubClass();
        $sub3ClassInstance = $sub3Class->createInstance('test case instance 3');
        $sub4ClassInstance = $sub3Class->createInstance('non-matching instance');

        $propertyFilter = [
            OntologyRdfs::RDFS_LABEL => 'test case instance'
        ];
        $instances = $class->searchInstances($propertyFilter, ['recursive' => true]);

        $this->assertCount(3, $instances);
        $this->assertArrayHasKey($sub1ClassInstance->getUri(), $instances);
        $this->assertArrayHasKey($sub2ClassInstance->getUri(), $instances);
        $this->assertArrayHasKey($sub3ClassInstance->getUri(), $instances);
        $this->assertArrayNotHasKey($sub4ClassInstance->getUri(), $instances);
    }

    public function testSearchInstancesWithOr()
    {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $subClass = $class->createSubClass();
        $sub1ClassInstance = $subClass->createInstance('first test case instance', 'first test case instance');
        $sub2ClassInstance = $subClass->createInstance('second test case instance', 'second test case instance');
        $sub3ClassInstance = $subClass->createInstance('non-matching instance', 'non-matching instance');

        $propertyFilter = [
            OntologyRdfs::RDFS_LABEL => 'first test case instance',
            OntologyRdfs::RDFS_COMMENT => 'second test case instance'
        ];
        $instances = $class->searchInstances($propertyFilter, ['recursive' => true, 'chaining' => 'or']);

        $this->assertCount(2, $instances);
        $this->assertArrayHasKey($sub1ClassInstance->getUri(), $instances);
        $this->assertArrayHasKey($sub2ClassInstance->getUri(), $instances);
        $this->assertArrayNotHasKey($sub3ClassInstance->getUri(), $instances);
    }

    public function testSearchInstancesComplexQuery()
    {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $subClass = $class->createSubClass();
        $relationSubClass = $class->createSubClass();

        $relationProperty = \core_kernel_classes_ClassFactory::createProperty(
            $subClass,
            'ComplexQueryRelationProperty',
            'ComplexQueryRelationProperty',
            false,
            LOCAL_NAMESPACE . "#RP"
        );
        $relationProperty->setRange($relationSubClass);

        $sub1ClassInstance = $subClass->createInstance('test case instance');
        $sub2ClassInstance = $relationSubClass->createInstance('relation test case instance');
        $sub1ClassInstance->setPropertyValue($relationProperty, $sub2ClassInstance);

        $instances = $subClass->searchInstances(
            [
                $relationProperty->getUri() => $sub2ClassInstance->getUri()
            ],
            [
                'recursive' => false
            ]
        );

        $this->assertCount(1, $instances);
        $this->assertArrayHasKey($sub1ClassInstance->getUri(), $instances);
    }

    public function testSearchInstancesWithOrder()
    {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $subClass = $class->createSubClass();
        $sub1ClassInstance = $subClass->createInstance('test case instance');
        $sub2ClassInstance = $subClass->createInstance('second test case instance');
        $sub3ClassInstance = $subClass->createInstance('test case instance 3');
        $sub4ClassInstance = $subClass->createInstance('non-matching instance');

        $instances = $subClass->searchInstances(
            [
                OntologyRdfs::RDFS_LABEL => 'test case instance'
            ],
            [
                'recursive' => false,
                'order' => OntologyRdfs::RDFS_LABEL,
                'orderdir' => 'DESC',
                'limit' => 1,
                'offset' => 1,
            ]
        );

        $this->assertCount(1, $instances);
        $this->assertArrayHasKey($sub1ClassInstance->getUri(), $instances);
        $this->assertArrayNotHasKey($sub2ClassInstance->getUri(), $instances);
        $this->assertArrayNotHasKey($sub3ClassInstance->getUri(), $instances);
        $this->assertArrayNotHasKey($sub4ClassInstance->getUri(), $instances);
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
            'correctLabel' => 'test case instance with underScore symbol',
            'incorrectLabel' => 'test case instance with undercore symbol',
            'searchCriterion' => 'instance with under_core',
        ];

        yield 'escaped wildcard symbols' => [
            'correctLabel' => 'test case instance w_th %pecial %ymbols',
            'incorrectLabel' => 'test case instance with special ymbols',
            'searchCriterion' => 'w\_th \%pecial \%ymbols',
        ];

        yield 'regexp plus symbols' => [
            'correctLabel' => 'application/qti+xml',
            'incorrectLabel' => 'application/qtiiiiixml',
            'searchCriterion' => 'application/qti+xml',
        ];

        yield 'regexp question mark symbols' => [
            'correctLabel' => 'test case instance with question?',
            'incorrectLabel' => 'test case instance with question!',
            'searchCriterion' => '*question?*',
        ];

        yield 'regexp square brackets symbols' => [
            'correctLabel' => 'test case instance with [abc]',
            'incorrectLabel' => 'test case instance with c',
            'searchCriterion' => '*with [abc]*',
        ];

        yield 'regexp round brackets symbols' => [
            'correctLabel' => 'test case instance with (brackets)',
            'incorrectLabel' => 'test case instance with brackets',
            'searchCriterion' => '*with (brackets)*',
        ];

        yield 'regexp curly brackets symbols' => [
            'correctLabel' => 'test case instance with{1,2}',
            'incorrectLabel' => 'test case instance withh',
            'searchCriterion' => '*with{1,2}*',
        ];

        yield 'regexp dollar symbols' => [
            'correctLabel' => 'test case instance with$',
            'incorrectLabel' => 'test case instance with',
            'searchCriterion' => '*with$',
        ];

        yield 'regexp caret symbols' => [
            'correctLabel' => '^test case instance with',
            'incorrectLabel' => 'test case instance with',
            'searchCriterion' => '^test*',
        ];

        yield 'regexp pipe symbols' => [
            'correctLabel' => 'test case instance with|pipe',
            'incorrectLabel' => 'test case instance with',
            'searchCriterion' => '*with|pipe*',
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
    ): void {
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
        $this->assertArrayNotHasKey($incorrectInstance->getUri(), $instances);
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

        $sub4ClassInstance = $sub3Class->createInstance('non-matching instance'); //en-US
        $sub4ClassInstance->setPropertyValueByLg($labelProperty, 'instance non correspondante', 'fr-FR');
        $sub4ClassInstance->setPropertyValueByLg($labelProperty, 'nicht passende Instanz', 'de-DE');

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

    public function testGetUniquePropertyValueLanguageSpecific()
    {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $labelProperty = new \core_kernel_classes_Property(OntologyRdfs::RDFS_LABEL);
        $subClass = $class->createSubClass();

        $subClassInstance = $subClass->createInstance('test case instance'); //en-US
        $subClassInstance->setPropertyValueByLg($labelProperty, 'instance de cas de test', 'fr-FR');
        $subClassInstance->setPropertyValueByLg($labelProperty, 'Testfallinstanz', 'de-DE');

        $this->assertEquals('test case instance', $subClassInstance->getUniquePropertyValue($labelProperty));
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

    public function testCreateInstance()
    {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);

        $instance = $class->createInstance('toto', 'tata');
        $this->assertEquals('toto', $instance->getLabel());
        $this->assertEquals($instance->getComment(), 'tata');

        $instance2 = $class->createInstance('toto', 'tata');
        $this->assertNotSame($instance, $instance2);
    }

    public function testCreateSubClass()
    {
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $subClass = $class->createSubClass('toto', 'tata');
        $this->assertNotEquals($class, $subClass);
        $this->assertEquals($subClass->getLabel(), 'toto');
        $this->assertEquals($subClass->getComment(), 'tata');

        $subClassOfProperty = new \core_kernel_classes_Property('http://www.w3.org/2000/01/rdf-schema#subClassOf');
        $subClassOfPropertyValue = $subClass->getPropertyValues($subClassOfProperty);
        $this->assertTrue(in_array($class->getUri(), array_values($subClassOfPropertyValue)));
    }

    //Test the function getInstancesPropertyValues of the class Class with literal properties
    public function testGetInstancesPropertyValuesWithLiteralProperties()
    {
        // create a class
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $subClass = $class->createSubClass('GetInstancesPropertyValuesClass', 'GetInstancesPropertyValues_Class');
        // create a first property for this class
        $p1 = \core_kernel_classes_ClassFactory::createProperty(
            $subClass,
            'GetInstancesPropertyValues_Property1',
            'GetInstancesPropertyValues_Property1',
            false,
            LOCAL_NAMESPACE . "#P1"
        );
        $p1->setRange(new core_kernel_classes_Class(OntologyRdfs::RDFS_LITERAL));
        // create a second property for this class
        $p2 = \core_kernel_classes_ClassFactory::createProperty(
            $subClass,
            'GetInstancesPropertyValues_Property2',
            'GetInstancesPropertyValues_Property2',
            false,
            LOCAL_NAMESPACE . "#P2"
        );
        $p2->setRange(new core_kernel_classes_Class(OntologyRdfs::RDFS_LITERAL));
        // create a second property for this class
        $p3 = \core_kernel_classes_ClassFactory::createProperty(
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
        $class = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET);
        $subClass = $class->createSubClass('GetInstancesPropertyValuesClass', 'GetInstancesPropertyValues_Class');
        // create a first property for this class
        $p1 = \core_kernel_classes_ClassFactory::createProperty(
            $subClass,
            'GetInstancesPropertyValues_Property1',
            'GetInstancesPropertyValues_Property1',
            false,
            LOCAL_NAMESPACE . "#P1"
        );
        $p1->setRange(new core_kernel_classes_Class(GenerisRdf::GENERIS_BOOLEAN));
        // create a second property for this class
        $p2 = \core_kernel_classes_ClassFactory::createProperty(
            $subClass,
            'GetInstancesPropertyValues_Property2',
            'GetInstancesPropertyValues_Property2',
            false,
            LOCAL_NAMESPACE . "#P2"
        );
        $p1->setRange(new core_kernel_classes_Class(GenerisRdf::GENERIS_BOOLEAN));
        // create a second property for this class
        $p3 = \core_kernel_classes_ClassFactory::createProperty(
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
                $property->getUri() == GenerisRdf::GENERIS_TRUE
                || $property->getUri() == GenerisRdf::GENERIS_FALSE
            );
        }
    }
}

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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

namespace oat\generis\test\unit\core\kernel\classes;

use core_kernel_classes_Class as RdfClass;
use core_kernel_persistence_ClassInterface as ClassImplementation;
use oat\generis\model\data\RdfsInterface;
use oat\generis\test\GenerisTestCase;
use oat\generis\test\MockObject;
use oat\oatbox\event\EventManager;
use oat\taoWorkspace\model\generis\WrapperModel;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Tests for class Rdf Class.
 */
class ClassTest extends GenerisTestCase
{
    /** @var RdfClass */
    private $subject;

    private $eventManager;

    private $label1 = 'a label';

    private $label2 = 'another label';

    public function setUp()
    {
        $this->subject = new RdfClass('http://example.com/uri');
    }

    public function testRetrieveSubClassByLabelWithExistingClassReturnsClass()
    {
        $label = 'a label';
        $this->subject->setModel($this->createModel([$label]));

        $this->assertEquals(
            $this->createClassWithLabel($label),
            $this->subject->retrieveSubClassByLabel($label)
        );
    }

    public function testRetrieveSubClassByLabelWithNonExistingClassReturnsNull()
    {
        $this->subject->setModel($this->createModel(['a label']));

        $this->assertNull($this->subject->retrieveSubClassByLabel('non existing label'));
    }

    public function testRetrieveOrCreateSubClassByLabelWithExistingClassReturnsClass()
    {
        $label = 'a label';
        $this->subject->setModel($this->createModel([$label]));

        $this->assertEquals(
            $this->createClassWithLabel($label),
            $this->subject->retrieveSubClassByLabel($label)
        );
    }

    public function testRetrieveOrCreateSubClassByLabelWithNonExistingClassReturnsNewClass()
    {
        $nonExistingLabel = 'non existing label';
        $expectedEvents = 1;

        $this->subject->setModel($this->createModel(['a label'], $expectedEvents));

        $this->assertEquals(
            $this->createClassWithLabel($nonExistingLabel),
            $this->subject->retrieveOrCreateSubClassByLabel($nonExistingLabel)
        );
    }

    public function testCreateSubClassPathByLabelWithEmptyArrayReturnsCurrentClass()
    {
        $this->assertEquals(
            $this->subject,
            $this->subject->createSubClassPathByLabel([])
        );
    }

    public function testCreateSubClassPathByLabelWithExistingClassReturnsClass()
    {
        $label = 'a label';
        $this->subject->setModel($this->createModel([$label]));

        $this->assertEquals(
            $this->createClassWithLabel($label),
            $this->subject->createSubClassPathByLabel([$label])
        );
    }

    public function testCreateSubClassPathByLabelWithNonExistingDirectSubClassReturnsClass()
    {
        $label = 'a label';
        $this->subject->setModel($this->createModel([], 1));

        $this->assertEquals(
            $this->createClassWithLabel($label),
            $this->subject->createSubClassPathByLabel([$label])
        );
    }

    public function testCreateSubClassPathByLabelWithNonExistingSubClassOfExistingClassReturnsNewClass()
    {
        $label = 'a label';
        $this->subject->setModel($this->createModel([$label => 1]));

        $newLabel = 'a brand new label';

        $this->assertEquals(
            $this->createClassWithLabel($newLabel),
            $this->subject->createSubClassPathByLabel([$label, $newLabel])
        );
    }

    private function createModel(array $subClassLabels, $triggeredEvents = 0)
    {
        /** @var ClassImplementation|MockObject $classImplementation */
        $classImplementation = $this->getMockBuilder(ClassImplementation::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSubClasses', 'createSubClass'])
            ->getMockForAbstractClass();
        $subClasses = $this->createSubclasses($subClassLabels);
        $classImplementation->method('getSubClasses')->willReturn($subClasses);
        $classImplementation->method('createSubClass')->willReturnCallback(
            function ($label) {
                return $this->createClassWithLabel($label);
            }
        );

        /** @var RdfsInterface|MockObject $rdfsInterface */
        $rdfsInterface = $this->getMockBuilder(RdfsInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getClassImplementation'])
            ->getMockForAbstractClass();
        $rdfsInterface->method('getClassImplementation')->willReturn($classImplementation);

        /** @var EventManager|MockObject $eventManager */
        $eventManager = $this->getMockBuilder(EventManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['trigger'])
            ->getMock();
        $eventManager->expects($this->exactly($triggeredEvents))->method('trigger');

        /** @var ServiceLocatorInterface|MockObject $serviceLocatorInterface */
        $serviceLocatorInterface = $this->getMockBuilder(ServiceLocatorInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMockForAbstractClass();
        $serviceLocatorInterface->method('get')->with(EventManager::SERVICE_ID)->willReturn($eventManager);

        /** @var WrapperModel|MockObject $model */
        $model = $this->getMockBuilder(WrapperModel::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRdfsInterface', 'getServiceLocator'])
            ->getMockForAbstractClass();
        $model->method('getRdfsInterface')->willReturn($rdfsInterface);
        $model->method('getServiceLocator')->willReturn($serviceLocatorInterface);

        return $model;
    }

    private function createClassWithLabel($label, $subClassLabels = [], $expectedEvents = 0)
    {
        $model = $this->createModel($subClassLabels, $expectedEvents);

        $class = $this->getMockBuilder(RdfClass::class)
            ->disableOriginalConstructor()
            ->setMethods(['getLabel', 'getModel'])
            ->getMock();
        $class->method('getLabel')->willReturn($label);
        $class->method('getModel')->willReturn($model);

        return $class;
    }

    private function createSubClasses(array $subClassesLabels)
    {
        // Passing an associative array allows to specify the number of events triggered.
        if (isset($subClassesLabels[0])) {
            $subClassesLabels = array_combine($subClassesLabels, array_fill(0, count($subClassesLabels), 0));
        }

        $subClasses = [];
        foreach ($subClassesLabels as $label => $expectedEvents) {
            $subClasses[] = $this->createClassWithLabel($label, [], $expectedEvents);
        }

        return $subClasses;
    }
}

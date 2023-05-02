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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 */

namespace oat\generis\test\unit\core\kernel\persistence;

use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use oat\generis\model\data\event\ResourceCreated;
use oat\generis\model\data\Model;
use oat\generis\model\data\Ontology;
use oat\generis\test\GenerisTestCase;
use oat\oatbox\action\Action;
use oat\oatbox\event\EventManager;
use Prophecy\Argument;
use Prophecy\Prediction\CallTimesPrediction;

class OntologyRdfsTest extends GenerisTestCase
{
    /**
     * @dataProvider getOntologies
     *
     * @param mixed $model
     */
    public function testSetLabel($model)
    {
        $this->assertInstanceOf(Model::class, $model);
        $resource = $model->getResource('http://testing');
        $this->assertInstanceOf(core_kernel_classes_Resource::class, $resource);
        $label = $resource->getLabel();
        $this->assertEquals('', $label);
        $resource->setLabel('magic');
        $label = $resource->getLabel();
        $this->assertEquals('magic', $label);
    }

    /**
     * @dataProvider getOntologies
     *
     * @param mixed $model
     */
    public function testCreateInstance($model)
    {
        $class = $model->getClass('http://testing#class');
        $this->assertInstanceOf(core_kernel_classes_Class::class, $class);
        // with URI
        $resource = $class->createInstance('sample', 'comment', 'http://testing#resource');
        $this->assertInstanceOf(core_kernel_classes_Resource::class, $resource);
        // without URI
        $resource = $class->createInstance('sample');
        $this->assertInstanceOf(core_kernel_classes_Resource::class, $resource);

        return $resource;
    }

    /**
     * @dataProvider getOntologies
     */
    public function testCreateResourceEvent(Ontology $model)
    {
        $callable = $this->prophesize(Action::class);
        $callable->__invoke(Argument::any())->should(new CallTimesPrediction(1));
        $eventManager = $model->getServiceLocator()->get(EventManager::SERVICE_ID);
        $eventManager->attach(ResourceCreated::class, $callable->reveal());
        $class = $model->getClass('http://testing#class');
        $class->createInstance('One love');
    }
    /**
     * @dataProvider getOntologies
     */
    public function testCreateResourceEventWithProperties(Ontology $model)
    {
        $callable = $this->prophesize(Action::class);
        $callable->__invoke(Argument::any())->should(new CallTimesPrediction(1));
        $eventManager = $model->getServiceLocator()->get(EventManager::SERVICE_ID);
        $eventManager->attach(ResourceCreated::class, $callable->reveal());
        $class = $model->getClass('http://testing#class');
        $class->createInstanceWithProperties([
            'prop1' => 'value1',
            'prop2' => 'value2',
        ]);
    }

    /**
     * @dataProvider getOntologies
     */
    public function testDuplicateInstance(Ontology $model)
    {
        $class = $model->getClass('http://testing#class');
        $this->assertInstanceOf(core_kernel_classes_Class::class, $class);
        $resource = $class->createInstance('original');
        $this->assertInstanceOf(core_kernel_classes_Resource::class, $resource);
        $resourceClone = $resource->duplicate();
        $this->assertInstanceOf(core_kernel_classes_Resource::class, $resourceClone);
        $this->assertEquals($resource->getLabel(), $resourceClone->getLabel());
        $this->assertNotEquals($resource, $resourceClone);
    }

    public function getOntologies()
    {
        return [
            [$this->getOntologyMock()],
            //[$this->getNewSqlMock()],
            //current step does not contain fully working new sql implementation intermediate step
        ];
    }
}

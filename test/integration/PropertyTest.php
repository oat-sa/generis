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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung
 *                         (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor
 *                         (under the project TAO-SUSTAIN & TAO-DEV);
 *               2017 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\generis\test\integration;

use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyRdf;
use oat\generis\model\WidgetRdf;
use oat\generis\test\GenerisPhpUnitTestRunner;
use core_kernel_classes_ContainerCollection;
use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use core_kernel_classes_Property;

class PropertyTest extends GenerisPhpUnitTestRunner
{
    /**
     *
     * @var core_kernel_classes_Property
     */
    protected $object;
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function setUp(): void
    {
        GenerisPhpUnitTestRunner::initTest();
        $this->object = new core_kernel_classes_Property(WidgetRdf::PROPERTY_WIDGET);
    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetDomain()
    {
        $domainCollection = $this->object->getDomain();
        $this->assertTrue($domainCollection instanceof core_kernel_classes_ContainerCollection);
        $this->assertCount(1, $domainCollection);

        $domain = $domainCollection->get(0);
        $this->assertEquals($domain->getUri(), OntologyRdf::RDF_PROPERTY);
        $this->assertEquals($domain->getLabel(), 'Property');
        $this->assertEquals($domain->getComment(), 'The class of RDF properties.');
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testSetDomain()
    {
        $class = new core_kernel_classes_Class(GenerisRdf::GENERIS_BOOLEAN, __METHOD__);
        $prop = $class->createProperty('test', 'test');
        $domain = $prop->getDomain();
        $this->assertEquals(1, $domain->count());
        $this->assertEquals($class, $domain->get(0));
        $widget = new core_kernel_classes_Class(WidgetRdf::CLASS_URI_WIDGET, __METHOD__);

        $this->assertTrue($prop->setDomain($widget));
        $this->assertEquals(2, $prop->getDomain()->count());
        $this->assertGreaterThanOrEqual(0, $prop->getDomain()->indexOf($widget));
        $this->assertGreaterThanOrEqual(0, $prop->getDomain()->indexOf($class));

        //if domain already set return true
        $this->assertTrue($prop->setDomain($class));
        $this->assertEquals(2, $prop->getDomain()->count());
        $this->assertGreaterThanOrEqual(0, $prop->getDomain()->indexOf($widget));
        $this->assertGreaterThanOrEqual(0, $prop->getDomain()->indexOf($class));

        $prop->delete(true);
    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetRange()
    {
        $range = $this->object->getRange();
        $this->assertTrue($range instanceof core_kernel_classes_Class);
        $this->assertEquals(WidgetRdf::CLASS_URI_WIDGET, $range->getUri());
        $this->assertEquals('Widget Class', $range->getLabel());
        $this->assertEquals('The class of all possible widgets', $range->getComment());
    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetWidget()
    {
        $widget = $this->object->getWidget();
        $this->assertInstanceOf(core_kernel_classes_Resource::class, $widget);
        $this->assertEquals('http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ComboBox', $widget->getUri());
        $this->assertEquals('Drop down menu', $widget->getLabel());
        $this->assertEquals('In drop down menu, one may select 1 to N options', $widget->getComment());
    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testSetMultiple()
    {
        $class = new core_kernel_classes_Class(GenerisRdf::GENERIS_BOOLEAN, __METHOD__);
        $prop = $class->createProperty('test', 'test');
        $multipleProperty = new core_kernel_classes_Property(GenerisRdf::PROPERTY_MULTIPLE);

        $this->assertEquals([], $prop->getPropertyValues($multipleProperty));

        $prop->setMultiple(true);
        $this->assertEquals([GenerisRdf::GENERIS_TRUE], $prop->getPropertyValues($multipleProperty));

        $prop->delete(true);
    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testIsMultiple()
    {
        $class = new core_kernel_classes_Class(GenerisRdf::GENERIS_BOOLEAN, __METHOD__);
        $prop = $class->createProperty('test', 'test');
        $this->assertFalse($prop->isMultiple());
        $prop->setMultiple(true);
        $this->assertTrue($prop->isMultiple());

        $new = new core_kernel_classes_Property($prop->getUri());
        $this->assertTrue($new->isMultiple());

        $prop->delete(true);
    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testDelete()
    {

        $class = new core_kernel_classes_Class(GenerisRdf::GENERIS_BOOLEAN, __METHOD__);

        $prop = $class->createProperty('test', 'test');

        $instance = $class->createInstance('test', 'test');
        $instance->setPropertyValue($prop, GenerisRdf::GENERIS_TRUE);
        $instance->setPropertyValue($prop, '3');

        $this->assertArrayHasKey($prop->getUri(), $class->getProperties());
        $val = $instance->getPropertyValues($prop);
        $this->assertTrue(in_array(GenerisRdf::GENERIS_TRUE, $val));
        $this->assertTrue(in_array(3, $val));

        $this->assertTrue($prop->delete(true));
        $this->assertEquals([], $instance->getPropertyValues($prop));
        $this->assertArrayNotHasKey($prop->getUri(), $class->getProperties());

        $this->assertTrue($instance->delete());
    }
}

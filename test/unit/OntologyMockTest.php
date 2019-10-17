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
 * 
 */
namespace oat\generis\test\unit;

use oat\generis\model\data\Model;
use oat\generis\test\GenerisTestCase;
use oat\generis\model\data\Ontology;
use core_kernel_persistence_smoothsql_SmoothModel as SmoothModel;
/**
 * 
 */
class OntologyMockTest extends GenerisTestCase
{
    /** @var SmoothModel */
    private $subject;
    
    public function setUp()
    {
        $this->subject = $this->getOntologyMock();
    }

    public function testOntologyMockClass()
    {
        $this->assertInstanceOf(Model::class, $this->subject);
    }

    public function testSetLabel()
    {
        $resource = $this->subject->getResource('http://testing');
        $this->assertInstanceOf(\core_kernel_classes_Resource::class, $resource);
        $label = $resource->getLabel();
        $this->assertEquals('', $label);
        $resource->setLabel('magic');
        $label = $resource->getLabel();
        $this->assertEquals('magic', $label);
    }

    public function testCreateInstance()
    {
        $class = $this->subject->getClass('http://testing#class');
        $this->assertInstanceOf(\core_kernel_classes_Class::class, $class);
        // with URI
        $resource = $class->createInstance('sample', 'comment', 'http://testing#resource');
        $this->assertInstanceOf(\core_kernel_classes_Resource::class, $resource);
        // without URI
        $resource = $class->createInstance('sample');
        $this->assertInstanceOf(\core_kernel_classes_Resource::class, $resource);
    }

    public function testDuplicateInstance()
    {
        $class = $this->subject->getClass('http://testing#class');
        $this->assertInstanceOf(\core_kernel_classes_Class::class, $class);
        $resource = $class->createInstance('original');
        $this->assertInstanceOf(\core_kernel_classes_Resource::class, $resource);
        $resourceClone = $resource->duplicate();
        $this->assertInstanceOf(\core_kernel_classes_Resource::class, $resourceClone);
        $this->assertEquals($resource->getLabel(), $resourceClone->getLabel());
        $this->assertNotEquals($resource, $resourceClone);
    }

    public function testDeleteInstance()
    {
        $class = $this->subject->getClass('http://testing#class');
        $resource = $class->createInstance('sample');

        $this->assertTrue($resource->exists());
        $resource->delete();
        $this->assertFalse($resource->exists());
    }
}

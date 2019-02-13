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
/**
 * 
 */
class OntologyMockTest extends GenerisTestCase
{
    public function testModel()
    {
        $model = $this->getOntologyMock();
        $this->assertInstanceOf(Model::class, $model);
        return $model;
    }

    /**
     * @depends testModel
     */
    public function testSetLabel($model)
    {
        $resource = $model->getResource('http://testing');
        $this->assertInstanceOf(\core_kernel_classes_Resource::class, $resource);
        $label = $resource->getLabel();
        $this->assertEquals('', $label);
        $resource->setLabel('magic');
        $label = $resource->getLabel();
        $this->assertEquals('magic', $label);
    }

    /**
     * @depends testModel
     */
    public function testCreateInstance($model)
    {
        $class = $model->getClass('http://testing#class');
        $this->assertInstanceOf(\core_kernel_classes_Class::class, $class);
        $resource = $class->createInstance('sample', 'comment', 'http://testing#resource');
        $this->assertInstanceOf(\core_kernel_classes_Resource::class, $resource);
        return $resource;
    }

    /**
     * @depends testCreateInstance
     */
    public function testDeleteInstance(\core_kernel_classes_Resource $resource)
    {
        $this->assertTrue($resource->exists());
        $resource->delete();
        $this->assertFalse($resource->exists());
    }
}

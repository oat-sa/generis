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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\generis\test\integration\core\kernel\classes;

use oat\tao\test\TaoPhpUnitTestRunner;
use core_kernel_classes_ResourceIterator as ResourceIterator;

/**
 * Class ResourceIteratorTest
 * @package oat\generis
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class ResourceIteratorTest extends TaoPhpUnitTestRunner
{

    protected static $sampleClass = 'http://www.tao.lu/Ontologies/TAO.rdf#ResourceIteratorTest';

    public function tearDown()
    {
        $this->removeResources();
    }

    public function testNext()
    {
        $this->removeResources();
        $class = new \core_kernel_classes_Class(self::$sampleClass);

        $iterator = new ResourceIterator([$class->getUri()]);
        $this->assertTrue($iterator->valid() === false);
        $this->assertTrue($iterator->current() === null);
        $this->assertTrue($iterator->valid() === false);

        $this->loadResources();

        $iterator = new ResourceIterator([$class->getUri()]);
        $resources = [];
        foreach ($iterator as $resource) {
            $this->assertTrue(!isset($resources[$resource->getUri()]));
            $resources[$resource->getUri()] = $resource;
            $this->assertTrue($resource->isInstanceOf($class));
        }
        $this->assertEquals((ResourceIterator::CACHE_SIZE * 2), count($resources));
    }

    private function removeResources()
    {
        $class = new \core_kernel_classes_Class(self::$sampleClass);
        foreach ($class->getInstances() as $instance) {
            $instance->delete();
        }
    }

    private function loadResources()
    {
        $class = new \core_kernel_classes_Class(self::$sampleClass);
        for ($i = 0; $i < ResourceIterator::CACHE_SIZE * 2; $i++) {
            $class->createInstance($i);
        }
    }
}

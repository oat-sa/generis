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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\generis\test\unit\model;

use common_exception_NoContent;
use core_kernel_classes_Class;
use core_kernel_classes_Resource;
use core_kernel_classes_ResourceFormatter;
use Exception;
use oat\generis\model\GenerisRdf;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ResourceFormatterTest extends TestCase
{
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetResourceDescriptionNoContent(): void
    {
        $formatter = new core_kernel_classes_ResourceFormatter();

        try {
            $formatter->getResourceDescription($this->createResourceDescription(true));
            $this->fail('common_exception_NoContent should have been raised');
        } catch (Exception $e) {
            $this->assertInstanceOf('common_exception_NoContent', $e);
        }
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetResourceDesciptionFromDef(): void
    {
        $formatter = new core_kernel_classes_ResourceFormatter();
        $result = $formatter->getResourceDescription($this->createResourceDescription());

        $this->assertInstanceOf('stdClass', $result);
        $this->assertSame('#fakeUri', $result->uri);
        $this->assertIsArray($result->properties);
        $this->assertCount(2, $result->properties);

        $this->assertInstanceOf('stdClass', $result->properties[0]);
        $this->assertSame('#propertyUri', $result->properties[0]->predicateUri);
        $this->assertIsArray($result->properties[0]->values);
        $this->assertCount(2, $result->properties[0]->values);

        $this->assertInstanceOf('stdClass', $result->properties[0]->values[0]);
        $this->assertSame('literal', $result->properties[0]->values[0]->valueType);
        $this->assertSame('value1', $result->properties[0]->values[0]->value);

        $this->assertInstanceOf('stdClass', $result->properties[0]->values[1]);
        $this->assertSame('literal', $result->properties[0]->values[1]->valueType);
        $this->assertSame('value2', $result->properties[0]->values[1]->value);

        $this->assertInstanceOf('stdClass', $result->properties[1]);
        $this->assertSame('#propertyUri2', $result->properties[1]->predicateUri);
        $this->assertIsArray($result->properties[1]->values);
        $this->assertCount(1, $result->properties[1]->values);

        $this->assertInstanceOf('stdClass', $result->properties[1]->values[0]);
        $this->assertSame('resource', $result->properties[1]->values[0]->valueType);
        $this->assertSame(GenerisRdf::GENERIS_BOOLEAN, $result->properties[1]->values[0]->value);
    }

    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetResourceDesciptionNoContentTripple(): void
    {
        $this->expectException(common_exception_NoContent::class);
        $resourceDesc = $this->createResourceMock('#fakeUri');
        $resourceDesc
            ->method('getRdfTriples')
            ->willReturn([]);
        $formatter = new core_kernel_classes_ResourceFormatter();

        $result = $formatter->getResourceDescription($resourceDesc, false);
    }

    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetResourceDesciption(): void
    {
        $resourceDesc = $this->createResourceMock('#fakeUri');
        $resourceDesc
            ->method('getRdfTriples')
            ->willReturn($this->generateTriple());

        $formatter = new core_kernel_classes_ResourceFormatter();

        $result = $formatter->getResourceDescription($resourceDesc, false);

        $this->assertInstanceOf('stdClass', $result);
        $this->assertSame('#fakeUri', $result->uri);
        $this->assertIsArray($result->properties);
        $this->assertCount(3, $result->properties);

        $this->assertInstanceOf('stdClass', $result->properties[0]);
        $this->assertSame('#predicate0', $result->properties[0]->predicateUri);
        $this->assertIsArray($result->properties[0]->values);
        $this->assertCount(1, $result->properties[0]->values);

        $this->assertInstanceOf('stdClass', $result->properties[0]->values[0]);
        $this->assertSame('resource', $result->properties[0]->values[0]->valueType);
        $this->assertSame(GenerisRdf::GENERIS_BOOLEAN, $result->properties[0]->values[0]->value);

        for ($i = 1; $i < 3; $i++) {
            $this->assertInstanceOf('stdClass', $result->properties[$i]);
            $this->assertSame('#predicate' . $i, $result->properties[$i]->predicateUri);
            $this->assertIsArray($result->properties[$i]->values);
            $this->assertCount(1, $result->properties[$i]->values);

            $this->assertInstanceOf('stdClass', $result->properties[$i]->values[0]);
            $this->assertSame('literal', $result->properties[$i]->values[0]->valueType);
            $this->assertSame('object' . $i, $result->properties[$i]->values[0]->value);
        }
    }
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    private function createPropertyMock(string $uri): core_kernel_classes_Resource|MockObject
    {
        $property = $this->createResourceMock($uri);
        $property
            ->method('__toString')
            ->willReturn($uri);

        return $property;
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    private function createResourceMock(string $uri): core_kernel_classes_Resource|MockObject
    {
        $resource = $this->createMock(core_kernel_classes_Resource::class);
        $resource
            ->method('getUri')
            ->willReturn($uri);

        return $resource;
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    private function createClassMock(string $uri): core_kernel_classes_Class|MockObject
    {
        $class = $this->createMock(core_kernel_classes_Class::class);
        $class
            ->method('getUri')
            ->willReturn($uri);

        return $class;
    }

    /**
     *
     * Create a mock to test formater result
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    private function createResourceDescription(bool $withNoValue = false): core_kernel_classes_Resource|MockObject
    {
        $resource = $this->createResourceMock('#fakeUri');
        $property = $this->createPropertyMock('#propertyUri');
        $property2 = $this->createPropertyMock('#propertyUri2');

        $type = $this->createClassMock('#typeUri');
        $type
            ->method('getProperties')
            ->with(true)
            ->willReturn([]);

        $type2 = $this->createClassMock('#typeUri2');
        $type2
            ->method('getProperties')
            ->with(true)
            ->willReturn(
                [
                    $property,
                    $property2
                ]
            );

        $resource
            ->method('getTypes')
            ->willReturn(
                [
                    $type,
                    $type2
                ]
            );

        $resource
            ->method('getPropertiesValues')
            ->with(
                [
                    "#propertyUri" => $property,
                    "#propertyUri2" => $property2
                ]
            )
            ->willReturn(
                $withNoValue
                    ? []
                    : [
                    '#propertyUri' => [
                        new \core_kernel_classes_Literal('value1'),
                        new \core_kernel_classes_Literal('value2')
                    ],
                    '#propertyUri2' => [
                        new core_kernel_classes_Resource(GenerisRdf::GENERIS_BOOLEAN)
                    ]
                ]
            );

        return $resource;
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    private function generateTriple(): array
    {
        $returnValue = [];

        for ($i = 0; $i < 3; $i++) {
            $triple = new \core_kernel_classes_Triple();
            $triple->subject = '#subject' . $i;
            $triple->predicate = '#predicate' . $i;
            $triple->object = $i == 0 ? GenerisRdf::GENERIS_BOOLEAN : 'object' . $i;
            $returnValue[] = $triple;
        }

        return $returnValue;
    }
}

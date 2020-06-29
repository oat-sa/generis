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
use \core_kernel_classes_ResourceFormatter;
use oat\generis\model\GenerisRdf;
use oat\generis\test\TestCase;

class ResourceFormatterTest extends TestCase
{
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $uri
     */
    private function createPropertyProphecy($uri)
    {
        $propertyProphecy = $this->createResourceProphecy($uri);
        $propertyProphecy->__toString()->willReturn($uri);
        return $propertyProphecy;
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $uri
     */
    private function createResourceProphecy($uri)
    {
        $resourceProphecy = $this->prophesize('core_kernel_classes_Resource');
        $resourceProphecy->getUri()->willReturn($uri);
        return $resourceProphecy;
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $uri
     */
    private function createClassProphecy($uri)
    {
        $classProphecy = $this->prophesize('core_kernel_classes_Class');
        $classProphecy->getUri()->willReturn($uri);
        return $classProphecy;
    }

    /**
     *
     * Create a mock to test formater result
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param string $withNoValue
     * @return Prophecy/Double
     */
    private function createResourceDescription($withNoValue = false)
    {
        $resourceDescProphecy = $this->createResourceProphecy('#fakeUri');
        $propertyProphecy = $this->createPropertyProphecy('#propertyUri');
        $propertyProphecy2 = $this->createPropertyProphecy('#propertyUri2');

        $typeProphecy = $this->createClassProphecy('#typeUri');
        $typeProphecy->getProperties(true)->willReturn(
            [
                $propertyProphecy->reveal(),
                $propertyProphecy2->reveal()
            ]
        );

        $typeProphecy2 = $typeProphecy = $this->createClassProphecy('#typeUri2');
        $typeProphecy->getProperties(true)->willReturn([]);

        $prop1 = $propertyProphecy->reveal();
        $prop2 = $propertyProphecy2->reveal();

        $typeProphecy2->getProperties(true)->willReturn(
            [
                $prop1,
                $prop2
            ]
        );
        $resourceDescProphecy->getTypes()->willReturn(
            [
                $typeProphecy->reveal(),
                $typeProphecy2->reveal()
            ]
        );
        if ($withNoValue) {
            $resourceDescProphecy->getPropertiesValues(
                [
                    "#propertyUri" => $prop1,
                    "#propertyUri2" => $prop2
                ]
            )->willReturn([]);
        } else {
            $resourceDescProphecy->getPropertiesValues([
                "#propertyUri" => $prop1,
                "#propertyUri2" => $prop2
            ])->willReturn([
                '#propertyUri' => [
                    new \core_kernel_classes_Literal('value1'),
                    new \core_kernel_classes_Literal('value2')
                ],
                '#propertyUri2' => [
                    new \core_kernel_classes_Resource(GenerisRdf::GENERIS_BOOLEAN)
                ]
            ]);
        }
        return $resourceDescProphecy->reveal();
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetResourceDescriptionNoContent()
    {
        $formatter = new core_kernel_classes_ResourceFormatter();
        try {
            $result = $formatter->getResourceDescription($this->createResourceDescription(true));
            $this->fail('common_exception_NoContent should have been raised');
        } catch (\Exception $e) {
            $this->assertInstanceOf('common_exception_NoContent', $e);
        }
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetResourceDesciptionFromDef()
    {
        $formatter = new core_kernel_classes_ResourceFormatter();
        $result = $formatter->getResourceDescription($this->createResourceDescription(false));

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
    public function testGetResourceDesciptionNoContentTripple()
    {
        $this->expectException(common_exception_NoContent::class);
        $resourceDescProphecy = $this->createResourceProphecy('#fakeUri');
        $resourceDescProphecy->getRdfTriples()->willReturn([]);
        $formatter = new core_kernel_classes_ResourceFormatter();

        $result = $formatter->getResourceDescription($resourceDescProphecy->reveal(), false);
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return array
     */
    private function generateTriple()
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

    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetResourceDesciption()
    {
        $resourceDescProphecy = $this->createResourceProphecy('#fakeUri');

        $resourceDescProphecy->getRdfTriples()->willReturn($this->generateTriple());
        $formatter = new core_kernel_classes_ResourceFormatter();

        $result = $formatter->getResourceDescription($resourceDescProphecy->reveal(), false);

        $this->assertInstanceOf('stdClass', $result);
        $this->assertSame('#fakeUri', $result->uri);
        $this->assertIsArray($result->properties);
        $this->assertCount(3, $result->properties);

        $this->assertInstanceOf('stdClass', $result->properties[0]);
        $this->assertSame('#predicate0', $result->properties[0]->predicateUri);
        $this->assertIsArray($result->properties[0]->values);
        $this->assertCount(1,$result->properties[0]->values);

        $this->assertInstanceOf('stdClass', $result->properties[0]->values[0]);
        $this->assertSame('resource', $result->properties[0]->values[0]->valueType);
        $this->assertSame(GenerisRdf::GENERIS_BOOLEAN, $result->properties[0]->values[0]->value);

        for ($i = 1; $i < 3; $i++) {
            $this->assertInstanceOf('stdClass', $result->properties[$i]);
            $this->assertSame('#predicate' . $i, $result->properties[$i]->predicateUri);
            $this->assertIsArray($result->properties[$i]->values);
            $this->assertCount(1,$result->properties[$i]->values);

            $this->assertInstanceOf('stdClass', $result->properties[$i]->values[0]);
            $this->assertSame('literal', $result->properties[$i]->values[0]->valueType);
            $this->assertSame('object' . $i, $result->properties[$i]->values[0]->value);
        }
    }
}

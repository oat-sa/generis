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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 */

namespace oat\generis\test\unit\common\configuration;

use common_configuration_ComponentFactory;
use common_configuration_ComponentFactoryException;
use common_configuration_FileSystemComponent;
use common_configuration_Mock;
use common_configuration_PHPDatabaseDriver;
use common_configuration_PHPExtension;
use common_configuration_PHPINIValue;
use common_configuration_PHPRuntime;
use oat\generis\test\TestCase;

/**
 * Test the \common_configuration_ComponentFactory class
 *
 * @author Jonathan VUILLEMIN <jonathan@taotesting.com>
 */
class ComponentFactoryTest extends TestCase
{
    /** @var common_configuration_ComponentFactory */
    private $subject;

    protected function setUp()
    {
        parent::setUp();

        $this->subject = new common_configuration_ComponentFactory();
    }

    public function testBuildPHPRuntime()
    {
        $output = $this->subject->buildPHPRuntime(0, 100, true);

        $this->assertInstanceOf(common_configuration_PHPRuntime::class, $output);
        $this->assertEquals(0, $output->getMin());
        $this->assertEquals(100, $output->getMax());
        $this->assertTrue($output->isOptional());
    }

    public function testBuildPHPExtension()
    {
        $output = $this->subject->buildPHPExtension('name', 0, 100, true);

        $this->assertInstanceOf(common_configuration_PHPExtension::class, $output);

        $this->assertEquals('name', $output->getName());
        $this->assertEquals(0, $output->getMin());
        $this->assertEquals(100, $output->getMax());
        $this->assertTrue($output->isOptional());
    }

    public function testBuildPHPINIValue()
    {
        $output = $this->subject->buildPHPINIValue('name', 'value', true);

        $this->assertInstanceOf(common_configuration_PHPINIValue::class, $output);
        $this->assertEquals('value', $output->getExpectedValue());
        $this->assertEquals('name', $output->getName());
        $this->assertTrue($output->isOptional());
    }

    public function testBuildPHPDatabaseDriver()
    {
        $output = $this->subject->buildPHPDatabaseDriver('name', true);

        $this->assertInstanceOf(common_configuration_PHPDatabaseDriver::class, $output);
        $this->assertEquals('name', $output->getName());
        $this->assertTrue($output->isOptional());
    }

    public function testBuildFileSystemComponent()
    {
        $output = $this->subject->buildFileSystemComponent('/path', 'rw', true, true, true);

        $this->assertInstanceOf(common_configuration_FileSystemComponent::class, $output);
        $this->assertEquals('/path', $output->getLocation());
        $this->assertEquals('rw', $output->getExpectedRights());
        $this->assertTrue($output->isOptional());
        $this->assertTrue($output->getRecursive());
        $this->assertTrue($output->getMustCheckIfEmpty());
        $this->assertEquals('FileSystemComponentCheck_3', $output->getName());

        $output2 = $this->subject->buildFileSystemComponent('/path2', 'rw');

        $this->assertEquals('FileSystemComponentCheck_4', $output2->getName());
    }

    public function testBuildCustomFailureOnNonExistingExtension()
    {
        $this->expectException(common_configuration_ComponentFactoryException::class);
        $this->subject->buildCustom('invalid', 'invalid');
    }

    public function testBuildMock()
    {
        $output = $this->subject->buildMock('status', true);

        $this->assertInstanceOf(common_configuration_Mock::class, $output);
        $this->assertEquals('MockComponentCheck_1', $output->getName());
        $this->assertTrue($output->isOptional());

        $output2 = $this->subject->buildMock('status2');

        $this->assertEquals('MockComponentCheck_2', $output2->getName());
    }

    public function testBuildFromArray()
    {
        $output = $this->subject->buildFromArray(['type' => 'PHPRuntime', 'value'=> ['min' => 1, 'max' => 2]]);
        $this->assertInstanceOf(common_configuration_PHPRuntime::class, $output);

        $output = $this->subject->buildFromArray(['type' => 'PHPINIValue', 'value'=> ['name' => 'name', 'value' => 'value']]);
        $this->assertInstanceOf(common_configuration_PHPINIValue::class, $output);

        $output = $this->subject->buildFromArray(['type' => 'PHPExtension', 'value'=> ['name' => 'name', 'min' => 1, 'max' => 2]]);
        $this->assertInstanceOf(common_configuration_PHPExtension::class, $output);

        $output = $this->subject->buildFromArray(['type' => 'PHPDatabaseDriver', 'value'=> ['name' => 'name']]);
        $this->assertInstanceOf(common_configuration_PHPDatabaseDriver::class, $output);

        $output = $this->subject->buildFromArray(['type' => 'FileSystemComponent', 'value'=> ['location' => '/path', 'rights' => 'rw']]);
        $this->assertInstanceOf(common_configuration_FileSystemComponent::class, $output);

        $output = $this->subject->buildFromArray(['type' => 'Mock', 'value'=> ['status' => '/status']]);
        $this->assertInstanceOf(common_configuration_Mock::class, $output);
    }

    public function testBuildFromArrayWithInvalidType()
    {
        $this->expectException(common_configuration_ComponentFactoryException::class);
        $this->subject->buildFromArray(['type' => 'invalid', 'value' => []]);
    }

    public function testBuildFromArrayWithMissingValue()
    {
        $this->expectException(common_configuration_ComponentFactoryException::class);
        $this->subject->buildFromArray(['type' => 'PHPRuntime']);
    }
}

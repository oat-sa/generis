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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\generis\test\oatbox\registry;

use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\oatbox\service\ConfigurableService;
use oat\oatbox\registry\RegistryTrait;
use oat\oatbox\registry\RegistryInterface;

/**
 * Class RegistryTraitTest
 * @package oat\generis\test\oatbox\registry
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class RegistryTraitTest extends GenerisPhpUnitTestRunner
{

    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * Configure registry instance
     */
    protected function setUp()
    {
        $this->registry = new RegistryTestSample([
            RegistryInterface::OPTION_REGISTRY => [
                'key_1' => 'val_1',
                'key_2' => 'val_2',
            ]
        ]);
    }

    public function testGetFromRegistry()
    {
        $this->assertEquals('val_1', $this->registry->getFromRegistry('key_1'));
        $this->assertEquals('val_2', $this->registry->getFromRegistry('key_2'));
    }

    /**
     * @expectedException \oat\oatbox\registry\RegistryException
     */
    public function testGetFromRegistryException()
    {
        $this->registry->getFromRegistry('foo');
    }

    public function testAddToRegistry()
    {
        $this->registry->addToRegistry('key_3', 'val_3');
        $this->assertEquals('val_3', $this->registry->getFromRegistry('key_3'));
        $registryArray =  $this->registry->getOption(RegistryInterface::OPTION_REGISTRY);
        $this->assertEquals('val_3', $registryArray['key_3']);
    }

    public function testRemoveFromRegistry()
    {
        $this->assertTrue($this->registry->existsInRegistry('key_2'));
        $this->registry->removeFromRegistry('key_2');
        $this->assertFalse($this->registry->existsInRegistry('key_2'));
    }

    /**
     * @expectedException \oat\oatbox\registry\RegistryException
     */
    public function testRemoveFromRegistryException()
    {
        $this->registry->removeFromRegistry('foo');
    }

    public function testExistsInRegistry()
    {
        $this->assertTrue($this->registry->existsInRegistry('key_1'));
        $this->assertFalse($this->registry->existsInRegistry('foo'));
    }
}

/**
 * Class RegistryTestSample
 * @package oat\generis\test\oatbox\registry
 */
class RegistryTestSample extends ConfigurableService implements RegistryInterface
{
    use RegistryTrait;

    public function __construct(array $options = array())
    {
        if (isset($options[self::OPTION_REGISTRY])) {
            $this->registry = $options[self::OPTION_REGISTRY];
        }

        parent::__construct($options);
    }
}
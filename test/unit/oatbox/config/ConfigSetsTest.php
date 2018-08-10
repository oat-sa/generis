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
 */

namespace oat\generis\test\unit\config;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\config\ConfigSets;
use oat\generis\test\TestCase;

/**
 * Class ConfigSetsTest
 * @package oat\generis\test\config
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class ConfigSetsTest extends TestCase
{

    /**
     * @var ConfigSets
     */
    protected $configurable;

    /**
     * Configure registry instance
     */
    protected function setUp()
    {
        $this->configurable = new ConfigurableTestSample([
            'handlers' => [
                'handler_1_name' => 'handler_1',
                'handler_2_name' => 'handler_2',
            ],
            'listeners' => [
                'listener_1_name' => 'listener_1',
                'listener_2_name' => 'listener_2',
            ],
            'key' => 'val'
        ]);
    }

    public function testHashGet()
    {
        $this->assertEquals('handler_1', $this->configurable->hashGet('handlers', 'handler_1_name'));
        $this->assertEquals(null, $this->configurable->hashGet('foo', 'bar'));
    }

    public function testHashSet()
    {
        $this->configurable->hashSet('handlers', 'handler_3_name', 'handler_3');
        $this->assertEquals('handler_3', $this->configurable->hashGet('handlers', 'handler_3_name'));

        $this->configurable->hashSet('handlers', 'handler_2_name', 'handler_4');
        $this->assertEquals('handler_4', $this->configurable->hashGet('handlers', 'handler_2_name'));

        $this->configurable->hashSet('foo', 'bar', 'baz');
        $this->assertEquals('baz', $this->configurable->hashGet('foo', 'bar'));
    }

    /**
     * @expectedException \common_exception_InconsistentData
     */
    public function testHashSetException()
    {
        $this->configurable->hashSet('key', 'bar', 'baz');
    }

    public function testHashRemove()
    {
        $this->configurable->hashRemove('handlers', 'handler_1_name');
        $this->assertEquals(null, $this->configurable->hashGet('handlers', 'handler_1_name'));
        $this->assertFalse($this->configurable->hashRemove('handlers', 'handler_1_name'));
    }

}

/**
 * Class ConfigurableTestSample
 * @package oat\generis\test\config
 */
class ConfigurableTestSample extends ConfigurableService
{
    use ConfigSets;
}


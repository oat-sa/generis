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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA ;
 */

namespace oat\generis\test\unit\oatbox;

use oat\generis\test\TestCase;
use oat\oatbox\Configurable;

class ConfigurableTest extends TestCase
{
    /**
     * @param array $options
     * @param string $optionName,
     * @param mixed|null $defaultValue
     * @param mixed $expected
     *
     * @dataProvider providerTestGetOptionWithDefault
     */
    public function testGetOptionWithDefault(array $options, $optionName, $defaultValue, $expected)
    {
        $configurable = new ConfigurableImplementation($options);

        $result = $configurable->getOption($optionName, $defaultValue);

        $this->assertEquals($expected, $result, 'Returned option value must be as expected.');
    }

    /**
     * @param array $options
     * @param string $optionName,
     * @param mixed $expected
     *
     * @dataProvider providerTestGetOptionWithoutDefault
     */
    public function testGetOptionWithoutDefault(array $options, $optionName, $expected)
    {
        $configurable = new ConfigurableImplementation($options);

        $result = $configurable->getOption($optionName);

        $this->assertEquals($expected, $result, 'Returned option value must be as expected.');
    }

    /**
     * Data provider for testGetOptionWithDefault
     *
     * @return array
     */
    public function providerTestGetOptionWithDefault()
    {
        return [
            'Option is not set' => [
                'options' => [],
                'optionName' => 'TEST_OPTION',
                'defaultValue' => 'DEFAULT',
                'expected' => 'DEFAULT'
            ],
            'Option is empty' => [
                'options' => [
                    'TEST_OPTION' => ''
                ],
                'optionName' => 'TEST_OPTION',
                'defaultValue' => 'DEFAULT',
                'expected' => ''
            ],
            'Option is set' => [
                'options' => [
                    'TEST_OPTION' => 'TEST_VALUE'
                ],
                'optionName' => 'TEST_OPTION',
                'defaultValue' => 'DEFAULT',
                'expected' => 'TEST_VALUE'
            ],
        ];
    }

    /**
     * Data provider for testGetOptionWithoutDefault
     *
     * @return array
     */
    public function providerTestGetOptionWithoutDefault()
    {
        return [
            'Option is not set' => [
                'options' => [],
                'optionName' => 'TEST_OPTION',
                'expected' => null
            ],
            'Option is empty' => [
                'options' => [
                    'TEST_OPTION' => ''
                ],
                'optionName' => 'TEST_OPTION',
                'expected' => ''
            ],
            'Option is set' => [
                'options' => [
                    'TEST_OPTION' => 'TEST_VALUE'
                ],
                'optionName' => 'TEST_OPTION',
                'expected' => 'TEST_VALUE'
            ],
        ];
    }
}

/**
 * Configurable class implementation for test.
 */
class ConfigurableImplementation extends Configurable {}


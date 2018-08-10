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
namespace oat\generis\test\integration\oatbox\extension\script;

use oat\oatbox\extension\script\OptionContainer;
use oat\generis\test\TestCase;

class OptionContainerTest extends TestCase
{
    
    /**
     * @dataProvider instantiateProvider
     */
    public function testInstantiate(array $options, array $values, array $expectedOptions)
    {
        $optionContainer = new OptionContainer($options, $values);
        
        // Check flags.
        foreach ($expectedOptions as $optionName => $optionValue) {
            $this->assertTrue($optionContainer->has($optionName));
            $this->assertSame($optionValue, $optionContainer->get($optionName));
        }
    }
    
    public function instantiateProvider()
    {
        return [
            [
                // call = myscript -f
                [
                    'myflag' => [
                        'prefix' => 'f',
                        'flag' => true
                    ]
                ],
                [
                    '-f'
                ],
                [
                    'myflag' => true
                ]
            ],
            [
                // call = myscript
                [
                    'myflag' => [
                        'prefix' => 'f',
                        'flag' => true
                    ]
                ],
                [
                    // no values given
                ],
                [
                    // no expected options
                ]
            ],
            [
                // call = myscript --flag
                [
                    'myflag' => [
                        'longPrefix' => 'flag',
                        'flag' => true
                    ]
                ],
                [
                    '--flag'
                ],
                [
                    'myflag' => true
                ]
            ],
            [
                // call = myscript -f -v value
                [
                    'myflag' => [
                        'prefix' => 'f',
                        'flag' => true
                    ],
                    'myValue' => [
                        'prefix' => 'v'
                    ]
                ],
                [
                    '-f', '-v', 'value'
                ],
                [
                    'myflag' => true,
                    'myValue' => 'value'
                ]
            ],
            [
                // call = myscript -f --value value
                [
                    'myflag' => [
                        'prefix' => 'f',
                        'flag' => true
                    ],
                    'myValue' => [
                        'prefix' => 'v',
                        'longPrefix' => 'value',
                        'required' => true
                    ]
                ],
                [
                    '-f', '--value', 'value'
                ],
                [
                    'myflag' => true,
                    'myValue' => 'value'
                ]
            ],
            [
                // call = myscript -f -v value
                [
                    'myflag' => [
                        'prefix' => 'f',
                        'flag' => true
                    ],
                    'myValue' => [
                        'prefix' => 'v',
                        'longPrefix' => 'value',
                        'required' => true
                    ]
                ],
                [
                    '-f', '-v', 'value'
                ],
                [
                    'myflag' => true,
                    'myValue' => 'value'
                ]
            ],
        ];
    }
}

<?php declare(strict_types=1);
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
namespace oat\generis\test\unit\common\oatbox\log\logger;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\TestHandler;
use Monolog\Processor\UidProcessor;
use oat\generis\test\TestCase;
use oat\oatbox\log\logger\TaoMonolog;
use Psr\Log\LogLevel;

class TaoMonologTest extends TestCase
{
    /** @var TaoMonolog */
    private $subject;

    /**
     * @dataProvider taoMonologConfigProvider
     */
    public function testLog(string $expectedOutput, array $config): void
    {
        $this->subject = new TaoMonolog($config);

        $this->subject->log(LogLevel::DEBUG, 'test message', ['foo' => 'bar']);

        $this->assertContains(
            $expectedOutput,
            $this->subject->getLogger()->getHandlers()[0]->getRecords()[0]['formatted']
        );
    }

    public function taoMonologConfigProvider(): array
    {
        return [
            // logger without channel name
            [
                'tao.DEBUG: test message {"foo":"bar"}',
                [
                    'handlers' => [
                        [
                            'class' => TestHandler::class,
                            'options' => [],
                        ],
                    ],
                ],
            ],
            // logger with channel name
            [
                'tao.DEBUG: test message {"foo":"bar"}',
                [
                    'handlers' => [
                        [
                            'class' => TestHandler::class,
                            'options' => [],
                        ],
                    ],
                    'channel' => 'foo',
                ],
            ],
            // logger with valid processor config
            [
                'tao.DEBUG: test message {"foo":"bar"}',
                [
                    'handlers' => [
                        [
                            'class' => TestHandler::class,
                            'options' => [],
                        ],
                    ],
                    'processors' => [
                        [
                            'class' => UidProcessor::class,
                            'options' => [32],
                        ],
                    ],
                ],
            ],
            // logger with valid processor class
            [
                'tao.DEBUG: test message {"foo":"bar"}',
                [
                    'handlers' => [
                        [
                            'class' => TestHandler::class,
                            'options' => [],
                        ],
                    ],
                    'processors' => [
                        new UidProcessor(32),
                    ],
                ],
            ],
            // logger with valid handler class
            [
                'tao.DEBUG: test message {"foo":"bar"}',
                [
                    'handlers' => [
                        [
                            'class' => TestHandler::class,
                            'options' => [],
                        ],
                    ],
                ],
            ],
            // logger with valid handler config
            [
                'tao.DEBUG: test message {"foo":"bar"}',
                [
                    'handlers' => [
                        [
                            'class' => TestHandler::class,
                            'options' => [],
                        ],
                    ],
                ],
            ],
            // logger with valid handler and processor config
            [
                'tao.DEBUG: test message {"foo":"bar"}',
                [
                    'handlers' => [
                        [
                            'class' => TestHandler::class,
                            'options' => [],
                            'processors' => [
                                [
                                    'class' => UidProcessor::class,
                                    'options' => [32],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            // logger with valid handler and processor class
            [
                'tao.DEBUG: test message {"foo":"bar"}',
                [
                    'handlers' => [
                        [
                            'class' => TestHandler::class,
                            'options' => [],
                            'processors' => [
                                new UidProcessor(32),
                            ],
                        ],
                    ],
                ],
            ],
            // logger with valid handler and formatter config
            [
                '"message":"test message","context":{"foo":"bar"},"level":100',
                [
                    'handlers' => [
                        [
                            'class' => TestHandler::class,
                            'options' => [],
                            'formatter' => [
                                'class' => JsonFormatter::class,
                            ],
                        ],
                    ],
                ],
            ],
            // logger with valid handler and formatter class
            [
                '"message":"test message","context":{"foo":"bar"},"level":100',
                [
                    'handlers' => [
                        [
                            'class' => TestHandler::class,
                            'options' => [],
                            'formatter' => new JsonFormatter(),
                        ],
                    ],
                ],
            ],
        ];
    }
}

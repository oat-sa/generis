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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\generis\test\unit\common\persistence;

use Monolog\Logger;
use oat\generis\persistence\DriverConfigurationFeeder;
use oat\generis\test\TestCase;
use stdClass;

class DriverConfigurationFeederTest extends TestCase
{
    /** @var DriverConfigurationFeeder */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new DriverConfigurationFeeder();
        $this->subject->setServiceLocator(
            $this->getServiceLocatorMock(
                [
                    Logger::class => $this->createMock(Logger::class),
                ]
            )
        );
    }

    public function testFeedWithCustomService(): void
    {
        $result = $this->subject->feed(
            [
                'connection' =>
                    [
                        'driverClass' => 'OAT\Library\DBALSpanner\SpannerDriver',
                        'driverOptions' => [
                            'driver-option-auth-pool' => Logger::class,
                            'driver-option-session-pool' => new stdClass(),
                        ]
                    ]
            ]
        );

        $this->assertInstanceOf(Logger::class, $result['connection']['driverOptions']['driver-option-auth-pool']);
        $this->assertInstanceOf(stdClass::class, $result['connection']['driverOptions']['driver-option-session-pool']);
    }

    public function testFeedWithNoDriverOptions(): void
    {
        $config = [
            'connection' =>
                [
                    'driverClass' => 'OAT\Library\DBALSpanner\SpannerDriver',
                ]
        ];

        $result = $this->subject->feed($config);

        $this->assertSame($config, $result);
    }

    public function testFeedWithNoDriverClass(): void
    {
        $config = [
            'connection' => []
        ];

        $result = $this->subject->feed($config);

        $this->assertSame($config, $result);
    }
}

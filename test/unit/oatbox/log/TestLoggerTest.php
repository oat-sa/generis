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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 */

namespace oat\generis\test\unit\oatbox\log;

use Psr\Log\LogLevel;
use oat\generis\test\TestCase;

/**
 * Test of test logger implementation
 */
class TestLoggerTest extends TestCase
{
    public function testArbitraryLogger() {
        $logger = new TestLogger();

        $logger->log(LogLevel::EMERGENCY, 'testEmergency1', ["context1" => "value1", "context2" => "value2"]);
        $logger->log(LogLevel::EMERGENCY, 'testEmergency2', ["context3" => "value3", "context4" => "value4"]);

        $entries = $logger->get(LogLevel::EMERGENCY);
        $this->assertEquals(2, count($entries));

        $this->assertEquals(0, count($logger->get(LogLevel::ALERT)));
        $this->assertEquals(0, count($logger->get(LogLevel::CRITICAL)));
        $this->assertEquals(0, count($logger->get(LogLevel::DEBUG)));
        $this->assertEquals(0, count($logger->get(LogLevel::ERROR)));
        $this->assertEquals(0, count($logger->get(LogLevel::INFO)));
        $this->assertEquals(0, count($logger->get(LogLevel::NOTICE)));
        $this->assertEquals(0, count($logger->get(LogLevel::WARNING)));

        $this->assertEquals('testEmergency1', $entries[0]['message']);
        $this->assertEquals(["context1" => "value1", "context2" => "value2"], $entries[0]['context']);
        $this->assertEquals('testEmergency2', $entries[1]['message']);
        $this->assertEquals(["context3" => "value3", "context4" => "value4"], $entries[1]['context']);

        $this->assertTrue($logger->has(LogLevel::EMERGENCY, 'testEmergency1'));
        $this->assertTrue($logger->has(LogLevel::EMERGENCY, 'testEmergency2'));

        $this->assertFalse($logger->has(LogLevel::EMERGENCY, 'I haven\'t been logged'));
    }

    public function testLogBadLevel() {
        $logger = new TestLogger();
        $logger->log('BAD_LEVEL', 'testMessage');

        $this->assertEquals(1, count($logger->get(LogLevel::ERROR)));
    }

    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testGetBadLevel() {
        $logger = new TestLogger();
        $logger->get('BAD_LEVEL');
    }

    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testHasBadLevel() {
        $logger = new TestLogger();
        $logger->has('BAD_LEVEL', 'testMessage');
    }
}
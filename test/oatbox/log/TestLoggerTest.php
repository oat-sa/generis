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

namespace oat\generis\test\oatbox;

use common_exception_InconsistentData;
use oat\oatbox\log\TestLogger;
use oat\tao\test\TaoPhpUnitTestRunner;

/**
 * Test of test logger implementation
 */
class TestRunnerFeatureTest extends TaoPhpUnitTestRunner
{
    public function testArbitraryLogger() {
        $logger = new TestLogger();

        $logger->log(TestLogger::EMERGENCY, 'testEmergency1', ["context1" => "value1", "context2" => "value2"]);
        $logger->log(TestLogger::EMERGENCY, 'testEmergency2', ["context3" => "value3", "context4" => "value4"]);

        $entries = $logger->get(TestLogger::EMERGENCY);
        $this->assertEquals(2, count($entries));

        $this->assertEquals(0, count($logger->get(TestLogger::ALERT)));
        $this->assertEquals(0, count($logger->get(TestLogger::CRITICAL)));
        $this->assertEquals(0, count($logger->get(TestLogger::DEBUG)));
        $this->assertEquals(0, count($logger->get(TestLogger::ERROR)));
        $this->assertEquals(0, count($logger->get(TestLogger::INFO)));
        $this->assertEquals(0, count($logger->get(TestLogger::NOTICE)));
        $this->assertEquals(0, count($logger->get(TestLogger::WARNING)));

        $this->assertEquals('testEmergency1', $entries[0]['message']);
        $this->assertEquals(["context1" => "value1", "context2" => "value2"], $entries[0]['context']);
        $this->assertEquals('testEmergency2', $entries[1]['message']);
        $this->assertEquals(["context3" => "value3", "context4" => "value4"], $entries[1]['context']);

        $this->assertTrue($logger->has(TestLogger::EMERGENCY, 'testEmergency1'));
        $this->assertTrue($logger->has(TestLogger::EMERGENCY, 'testEmergency2'));

        $this->assertFalse($logger->has(TestLogger::EMERGENCY, 'I haven\'t been logged'));
    }

    /**
     * @expectedException common_exception_InconsistentData
     */
    public function testLogBadLevel() {
        $logger = new TestLogger();
        $logger->log('BAD_LEVEL', 'testMessage');
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

    public function provideTestLevelData() {
        return [
            [ TestLogger::EMERGENCY, 'emergency',   'getEmergency', 'hasEmergency'],
            [ TestLogger::ALERT,     'alert',       'getAlert',     'hasAlert'],
            [ TestLogger::CRITICAL,  'critical',    'getCritical',  'hasCritical'],
            [ TestLogger::ERROR,     'error',       'getError',     'hasError'],
            [ TestLogger::WARNING,   'warning',     'getWarning',   'hasWarning'],
            [ TestLogger::NOTICE,    'notice',      'getNotice',    'hasNotice'],
            [ TestLogger::INFO,      'info',        'getInfo',      'hasInfo'],
            [ TestLogger::DEBUG,     'debug',       'getDebug',     'hasDebug'],
        ];
    }

    /**
     * @dataProvider provideTestLevelData
     * @param $currentLevel
     * @param $logFunction
     * @param $getFunction
     * @param $hasFunction
     */
    public function testLevel($currentLevel, $logFunction, $getFunction, $hasFunction) {
        $logger = new TestLogger();

        // log Entries
        $logMessage1 = 'testLevel ' . $currentLevel . '-1';
        $logMessage2 = 'testLevel ' . $currentLevel . '-2';
        call_user_func(array($logger, $logFunction), $logMessage1, [1, 2, 3]);
        call_user_func(array($logger, $logFunction), $logMessage2, [4, 5, 6]);

        // check entries content
        $entries = call_user_func(array($logger, $getFunction));

        $this->assertEquals($entries, $logger->get($currentLevel));

        $this->assertEquals(2, count($entries));
        $this->assertEquals($logMessage1, $entries[0]['message']);
        $this->assertEquals([1, 2, 3], $entries[0]['context']);
        $this->assertEquals($logMessage2, $entries[1]['message']);
        $this->assertEquals([4, 5, 6], $entries[1]['context']);

        // check entries search
        $this->assertTrue($logger->has($currentLevel, $logMessage1));
        $this->assertTrue($logger->has($currentLevel, $logMessage2));
        $this->assertTrue(call_user_func(array($logger, $hasFunction), $logMessage1));
        $this->assertTrue(call_user_func(array($logger, $hasFunction), $logMessage2));
        $this->assertFalse(call_user_func(array($logger, $hasFunction), 'I was never logged...'));

        // check for unwanted logs
        $allLevels = [
            "emergency",
            "alert",
            "critical",
            "error",
            "warning",
            "notice",
            "info",
            "debug"
        ];

        foreach ($allLevels as $level) {
            if ($level !== $currentLevel) {
                $this->assertEquals(0, count($logger->get($level)));
            }
        }
    }
}
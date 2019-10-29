<?php
/*
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */
namespace oat\generis\test\unit\oatbox\log;

use Monolog\Handler\StreamHandler;
use oat\oatbox\log\logger\TaoMonolog;
use oat\oatbox\log\LoggerService;
use oat\oatbox\log\logger\TaoLog;
use oat\generis\test\TestCase;
use Psr\Log\LogLevel;

class LoggerServiceTest extends TestCase
{
    /** @var LoggerService */
    private $subject;

    /** @var string */
    private $logFilePath;

    protected function setUp(): void
    {
        $this->logFilePath = tempnam(sys_get_temp_dir(), 'logtest');
    }

    protected function tearDown(): void
    {
        unlink($this->logFilePath);
    }

    public function testItLogsWithTaoLog(): void
    {
        $this->subject = new LoggerService([
            'logger' => $this->getTaoLog(),
        ]);

        $this->subject->log(LogLevel::ERROR, 'test message', ['foo' => 'bar']);

        $this->assertContains("[ERROR] 'test message'", file_get_contents($this->logFilePath));
    }

    public function testItLogsWithTaoMonolog(): void
    {
        $this->subject = new LoggerService([
            'logger' => $this->getTaoMonolog('tao'),
        ]);

        $this->subject->log(LogLevel::ERROR, 'test message', ['foo' => 'bar']);

        $this->assertContains('tao.ERROR: test message {"foo":"bar"}', file_get_contents($this->logFilePath));
    }

    public function testItSupportsMultipleTaoLogger(): void
    {
        $this->subject = new LoggerService([
            'logger' => $this->getTaoLog(),
            'loggers' => [
                $this->getTaoLog('foo'),
            ],
        ]);

        $this->subject->getLogger()->log(LogLevel::ERROR, 'test message', ['foo' => 'bar']);

        $this->assertEquals(2, substr_count(file_get_contents($this->logFilePath), "[ERROR] 'test message'"));
    }

    public function testItSupportsMultipleMonologLogger(): void
    {
        $this->subject = new LoggerService([
            'logger' => $this->getTaoMonolog('tao'),
            'loggers' => [
                $this->getTaoMonolog('foo'),
                $this->getTaoMonolog('bar'),
            ],
        ]);

        $this->subject->getLogger('tao')->log(LogLevel::ERROR, 'test message', ['foo' => 'bar']);
        $this->subject->getLogger('foo')->log(LogLevel::ERROR, 'test message', ['foo' => 'bar']);
        $this->subject->getLogger('bar')->log(LogLevel::ERROR, 'test message', ['foo' => 'bar']);

        $this->assertContains('tao.ERROR: test message {"foo":"bar"}', file_get_contents($this->logFilePath));
        $this->assertContains('foo.ERROR: test message {"foo":"bar"}', file_get_contents($this->logFilePath));
        $this->assertContains('bar.ERROR: test message {"foo":"bar"}', file_get_contents($this->logFilePath));
    }

    public function testItMergesLoggersWithSameChannels(): void
    {
        $this->subject = new LoggerService([
            'logger' => $this->getTaoMonolog('tao'),
            'loggers' => [
                $this->getTaoMonolog('tao'),
            ],
        ]);

        $this->subject->getLogger('tao')->log(LogLevel::ERROR, 'test message', ['foo' => 'bar']);

        $this->assertEquals(2, substr_count(file_get_contents($this->logFilePath), 'tao.ERROR: test message {"foo":"bar"}'));
    }

    public function testItLogsToDefaultMonologChannelByDefault(): void
    {
        $this->subject = new LoggerService([
            'logger' => $this->getTaoMonolog(),
        ]);

        $this->subject->log(LogLevel::ERROR, 'test message', ['foo' => 'bar']);

        $this->assertContains('tao.ERROR: test message {"foo":"bar"}', file_get_contents($this->logFilePath));
    }

    public function testNonRequiredLoggerKey(): void
    {
        $this->subject = new LoggerService([
            'loggers' => [
                $this->getTaoMonolog(),
            ],
        ]);

        $this->subject->log(LogLevel::ERROR, 'test message', ['foo' => 'bar']);

        $this->assertContains('tao.ERROR: test message {"foo":"bar"}', file_get_contents($this->logFilePath));
    }

    private function getTaoLog(): TaoLog
    {
        return new TaoLog([
            'appenders' => [
                [
                    'class' => 'SingleFileAppender',
                    'threshold' => \common_Logger::DEBUG_LEVEL,
                    'file' => $this->logFilePath,
                ],
            ]
        ]);
    }

    private function getTaoMonolog(string $channel = null): TaoMonolog
    {
        return new TaoMonolog([
            'name' => $channel,
            'handlers' => [
                [
                    'class' => StreamHandler::class,
                    'options' => [
                        $this->logFilePath,
                    ],
                ],
            ]
        ]);
    }
}

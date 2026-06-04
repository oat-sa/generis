<?php

declare(strict_types=1);

namespace oat\generis\test\unit\core\log;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use oat\generis\model\log\LogSingleFileAppenderToJsonUpdater;
use oat\oatbox\log\LoggerService;
use oat\oatbox\log\logger\formatter\StderrJsonFormatter;
use oat\oatbox\log\logger\TaoLog;
use oat\oatbox\log\logger\TaoMonolog;
use PHPUnit\Framework\TestCase;

class LogSingleFileAppenderToJsonUpdaterTest extends TestCase
{
    private LogSingleFileAppenderToJsonUpdater $updater;

    protected function setUp(): void
    {
        $this->updater = new LogSingleFileAppenderToJsonUpdater();
    }

    public function testApplyConvertsSingleFileAppenderToTaoMonologJson(): void
    {
        $options = [
            LoggerService::LOGGER_OPTION => [
                'class' => TaoLog::class,
                'options' => [
                    'appenders' => [
                        [
                            'class' => 'SingleFileAppender',
                            'file' => 'php://stderr',
                            'format' => "%d [%s] [%p] '%m' %f %l",
                            'threshold' => \common_Logger::INFO_LEVEL,
                            'prefix' => 'tao',
                        ],
                    ],
                ],
            ],
        ];

        $updated = $this->updater->apply($options);

        $this->assertTrue($this->updater->usesStderrJsonLogger($updated));

        $logger = $updated[LoggerService::LOGGER_OPTION];
        $this->assertSame(TaoMonolog::class, $logger['class']);
        $this->assertSame('tao', $logger['options']['name']);

        $handler = $logger['options']['handlers'][0];
        $this->assertSame(StreamHandler::class, $handler['class']);
        $this->assertSame('php://stderr', $handler['options'][0]);
        $this->assertSame(Level::Info, $handler['options'][1]);
        $this->assertSame(StderrJsonFormatter::class, $handler['formatter']['class']);
    }

    public function testApplyIsIdempotent(): void
    {
        $options = [
            LoggerService::LOGGER_OPTION => [
                'class' => TaoMonolog::class,
                'options' => [
                    'name' => 'tao',
                    'handlers' => [
                        [
                            'class' => StreamHandler::class,
                            'options' => ['php://stderr', Level::Info],
                            'formatter' => ['class' => StderrJsonFormatter::class],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertSame($options, $this->updater->apply($options));
    }

    public function testRevertRestoresSingleFileAppender(): void
    {
        $options = [
            LoggerService::LOGGER_OPTION => [
                'class' => TaoMonolog::class,
                'options' => [
                    'name' => 'tao',
                    'handlers' => [
                        [
                            'class' => StreamHandler::class,
                            'options' => ['php://stderr', Level::Info],
                            'formatter' => ['class' => StderrJsonFormatter::class],
                        ],
                    ],
                ],
            ],
        ];

        $reverted = $this->updater->revert($options);

        $this->assertSame(TaoLog::class, $reverted[LoggerService::LOGGER_OPTION]['class']);
        $this->assertSame(
            'SingleFileAppender',
            $reverted[LoggerService::LOGGER_OPTION]['options']['appenders'][0]['class']
        );
        $this->assertSame('php://stderr', $reverted[LoggerService::LOGGER_OPTION]['options']['appenders'][0]['file']);
    }
}

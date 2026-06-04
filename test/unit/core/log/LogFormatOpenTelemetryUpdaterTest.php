<?php

declare(strict_types=1);

namespace oat\generis\test\unit\core\log;

use oat\generis\model\log\LogFormatOpenTelemetryUpdater;
use oat\oatbox\log\LoggerService;
use PHPUnit\Framework\TestCase;

class LogFormatOpenTelemetryUpdaterTest extends TestCase
{
    private LogFormatOpenTelemetryUpdater $updater;

    protected function setUp(): void
    {
        $this->updater = new LogFormatOpenTelemetryUpdater();
    }

    public function testApplyAppendsTracePlaceholdersToSingleFileAppender(): void
    {
        $options = [
            LoggerService::LOGGER_OPTION => [
                'class' => 'oat\\oatbox\\log\\logger\\TaoLog',
                'options' => [
                    'appenders' => [
                        [
                            'class' => 'SingleFileAppender',
                            'file' => 'php://stderr',
                            'format' => "%d [%s] [%p] '%m' %f %l",
                        ],
                    ],
                ],
            ],
        ];

        $updated = $this->updater->apply($options);

        $this->assertSame(
            "%d [%s] [%p] '%m' %f %l traceId=%traceId spanId=%spanId",
            $updated[LoggerService::LOGGER_OPTION]['options']['appenders'][0]['format']
        );
    }

    public function testApplyIsIdempotent(): void
    {
        $options = [
            LoggerService::LOGGER_OPTION => [
                'class' => 'oat\\oatbox\\log\\logger\\TaoLog',
                'options' => [
                    'appenders' => [
                        [
                            'class' => 'SingleFileAppender',
                            'format' => "%d [%s] [%p] '%m' %f %l traceId=%traceId spanId=%spanId",
                        ],
                    ],
                ],
            ],
        ];

        $this->assertSame($options, $this->updater->apply($options));
    }

    public function testApplyIgnoresNonSingleFileAppender(): void
    {
        $options = [
            LoggerService::LOGGER_OPTION => [
                'class' => 'oat\\oatbox\\log\\logger\\TaoLog',
                'options' => [
                    'appenders' => [
                        [
                            'class' => 'SomeOtherAppender',
                            'format' => '%m',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertSame($options, $this->updater->apply($options));
    }

    public function testRevertRemovesTracePlaceholders(): void
    {
        $options = [
            LoggerService::LOGGER_OPTION => [
                'class' => 'oat\\oatbox\\log\\logger\\TaoLog',
                'options' => [
                    'appenders' => [
                        [
                            'class' => 'SingleFileAppender',
                            'format' => "%d [%s] [%p] '%m' %f %l traceId=%traceId spanId=%spanId",
                        ],
                    ],
                ],
            ],
        ];

        $reverted = $this->updater->revert($options);

        $this->assertSame(
            "%d [%s] [%p] '%m' %f %l",
            $reverted[LoggerService::LOGGER_OPTION]['options']['appenders'][0]['format']
        );
    }
}

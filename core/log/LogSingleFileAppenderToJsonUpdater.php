<?php

declare(strict_types=1);

namespace oat\generis\model\log;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use oat\oatbox\log\LoggerService;
use oat\oatbox\log\logger\formatter\StderrJsonFormatter;
use oat\oatbox\log\logger\TaoLog;
use oat\oatbox\log\logger\TaoMonolog;

/**
 * Migrates TaoLog + SingleFileAppender (text) to TaoMonolog + StreamHandler + StderrJsonFormatter.
 */
final class LogSingleFileAppenderToJsonUpdater
{
    private const DEFAULT_TEXT_FORMAT = "%d [%s] [%p] '%m' %f %l";

    /**
     * @param array<string, mixed> $options LoggerService options
     *
     * @return array<string, mixed>
     */
    public function apply(array $options): array
    {
        if ($this->usesStderrJsonLogger($options)) {
            return $options;
        }

        $appender = $this->extractPrimarySingleFileAppender($options);
        if ($appender === null) {
            return $options;
        }

        $options[LoggerService::LOGGER_OPTION] = $this->buildMonologJsonLoggerConfig($appender);

        return $options;
    }

    /**
     * @param array<string, mixed> $options LoggerService options
     *
     * @return array<string, mixed>
     */
    public function revert(array $options): array
    {
        if (!$this->usesStderrJsonLogger($options)) {
            return $options;
        }

        $handler = $this->extractMonologHandler($options);
        $file = is_array($handler) ? ($handler['file'] ?? 'php://stderr') : 'php://stderr';
        $prefix = $this->extractChannelName($options);

        $options[LoggerService::LOGGER_OPTION] = [
            'class' => TaoLog::class,
            'options' => [
                TaoLog::OPTION_APPENDERS => [
                    [
                        'class' => 'SingleFileAppender',
                        'file' => $file,
                        'format' => self::DEFAULT_TEXT_FORMAT,
                        'threshold' => \common_Logger::INFO_LEVEL,
                        'prefix' => $prefix,
                        'rotation-ratio' => 0,
                    ],
                ],
            ],
        ];

        return $options;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function usesStderrJsonLogger(array $options): bool
    {
        $logger = $options[LoggerService::LOGGER_OPTION] ?? null;
        if (!is_array($logger)) {
            return false;
        }

        $class = $logger['class'] ?? '';
        if (!is_string($class) || !str_contains($class, 'TaoMonolog')) {
            return false;
        }

        $handlers = $logger['options']['handlers'] ?? [];
        if (!is_array($handlers)) {
            return false;
        }

        foreach ($handlers as $handler) {
            if (!is_array($handler)) {
                continue;
            }

            $formatterClass = $handler['formatter']['class'] ?? '';
            if (is_string($formatterClass) && str_contains($formatterClass, 'StderrJsonFormatter')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>|null
     */
    private function extractPrimarySingleFileAppender(array $options): ?array
    {
        $appenders = $this->extractAppenders($options);
        if ($appenders === null) {
            return null;
        }

        foreach ($appenders as $appender) {
            if ($this->isSingleFileAppender($appender)) {
                return $appender;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return list<array<string, mixed>>|null
     */
    private function extractAppenders(array $options): ?array
    {
        if (!isset($options[LoggerService::LOGGER_OPTION])) {
            return null;
        }

        $logger = $options[LoggerService::LOGGER_OPTION];

        if ($logger instanceof TaoLog) {
            $appenders = $logger->getOption(TaoLog::OPTION_APPENDERS);

            return is_array($appenders) ? $appenders : null;
        }

        if (
            is_array($logger)
            && isset($logger['options']['appenders'])
            && is_array($logger['options']['appenders'])
        ) {
            return $logger['options']['appenders'];
        }

        return null;
    }

    /**
     * @param array<string, mixed> $appender
     *
     * @return array<string, mixed>
     */
    private function buildMonologJsonLoggerConfig(array $appender): array
    {
        $file = isset($appender['file']) && is_string($appender['file']) ? $appender['file'] : 'php://stderr';
        $prefix = isset($appender['prefix']) && is_string($appender['prefix']) ? $appender['prefix'] : 'tao';
        $threshold = isset($appender['threshold']) && is_numeric($appender['threshold'])
            ? (int) $appender['threshold']
            : \common_Logger::INFO_LEVEL;

        return [
            'class' => TaoMonolog::class,
            'options' => [
                'name' => $prefix,
                'handlers' => [
                    [
                        'class' => StreamHandler::class,
                        'options' => [
                            $file,
                            $this->mapThresholdToMonologLevel($threshold),
                        ],
                        'formatter' => [
                            'class' => StderrJsonFormatter::class,
                            'options' => [
                                [
                                    'includeContext' => true,
                                    'maxContextJsonLength' => 1024,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function mapThresholdToMonologLevel(int $threshold): Level
    {
        return match ($threshold) {
            \common_Logger::TRACE_LEVEL, \common_Logger::DEBUG_LEVEL => Level::Debug,
            \common_Logger::INFO_LEVEL => Level::Info,
            \common_Logger::WARNING_LEVEL => Level::Warning,
            \common_Logger::ERROR_LEVEL => Level::Error,
            \common_Logger::FATAL_LEVEL => Level::Critical,
            default => Level::Info,
        };
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>|null
     */
    private function extractMonologHandler(array $options): ?array
    {
        $logger = $options[LoggerService::LOGGER_OPTION] ?? null;
        if (!is_array($logger)) {
            return null;
        }

        $handlers = $logger['options']['handlers'] ?? [];
        if (!is_array($handlers) || $handlers === []) {
            return null;
        }

        $handler = $handlers[0];
        if (!is_array($handler) || !isset($handler['options'][0])) {
            return null;
        }

        return [
            'file' => $handler['options'][0],
        ];
    }

    /**
     * @param array<string, mixed> $options
     */
    private function extractChannelName(array $options): string
    {
        $logger = $options[LoggerService::LOGGER_OPTION] ?? null;
        if (!is_array($logger)) {
            return 'tao';
        }

        $name = $logger['options']['name'] ?? 'tao';

        return is_string($name) ? $name : 'tao';
    }

    /**
     * @param array<string, mixed> $appender
     */
    private function isSingleFileAppender(array $appender): bool
    {
        if (!isset($appender['class']) || !is_string($appender['class'])) {
            return false;
        }

        return str_contains($appender['class'], 'SingleFileAppender');
    }
}

<?php

declare(strict_types=1);

namespace oat\generis\model\log;

use oat\oatbox\log\LoggerService;
use oat\oatbox\log\logger\TaoLog;

/**
 * Updates TaoLog SingleFileAppender format strings so OpenTelemetry trace/span ids
 * from PSR-3 context appear in GCP stderr logs (%traceId / %spanId placeholders).
 */
final class LogFormatOpenTelemetryUpdater
{
    public const TRACE_FORMAT_SUFFIX = ' traceId=%traceId spanId=%spanId';

    /**
     * @param array<string, mixed> $options LoggerService options
     *
     * @return array<string, mixed>
     */
    public function apply(array $options): array
    {
        if (!isset($options[LoggerService::LOGGER_OPTION])) {
            return $options;
        }

        $logger = $options[LoggerService::LOGGER_OPTION];

        if ($logger instanceof TaoLog) {
            $appenders = $logger->getOption(TaoLog::OPTION_APPENDERS);
            if (!is_array($appenders)) {
                return $options;
            }

            $updatedAppenders = $this->updateAppenders($appenders);
            if ($updatedAppenders === $appenders) {
                return $options;
            }

            $logger->setOption(TaoLog::OPTION_APPENDERS, $updatedAppenders);

            return $options;
        }

        if (!is_array($logger) || !isset($logger['options']['appenders']) || !is_array($logger['options']['appenders'])) {
            return $options;
        }

        $updatedAppenders = $this->updateAppenders($logger['options']['appenders']);
        if ($updatedAppenders === $logger['options']['appenders']) {
            return $options;
        }

        $logger['options']['appenders'] = $updatedAppenders;
        $options[LoggerService::LOGGER_OPTION] = $logger;

        return $options;
    }

    /**
     * @param array<string, mixed> $options LoggerService options
     *
     * @return array<string, mixed>
     */
    public function revert(array $options): array
    {
        if (!isset($options[LoggerService::LOGGER_OPTION])) {
            return $options;
        }

        $logger = $options[LoggerService::LOGGER_OPTION];

        if ($logger instanceof TaoLog) {
            $appenders = $logger->getOption(TaoLog::OPTION_APPENDERS);
            if (!is_array($appenders)) {
                return $options;
            }

            $revertedAppenders = $this->revertAppenders($appenders);
            if ($revertedAppenders === $appenders) {
                return $options;
            }

            $logger->setOption(TaoLog::OPTION_APPENDERS, $revertedAppenders);

            return $options;
        }

        if (!is_array($logger) || !isset($logger['options']['appenders']) || !is_array($logger['options']['appenders'])) {
            return $options;
        }

        $revertedAppenders = $this->revertAppenders($logger['options']['appenders']);
        if ($revertedAppenders === $logger['options']['appenders']) {
            return $options;
        }

        $logger['options']['appenders'] = $revertedAppenders;
        $options[LoggerService::LOGGER_OPTION] = $logger;

        return $options;
    }

    /**
     * @param list<array<string, mixed>> $appenders
     *
     * @return list<array<string, mixed>>
     */
    private function updateAppenders(array $appenders): array
    {
        foreach ($appenders as $index => $appender) {
            if (!is_array($appender) || !isset($appender['format']) || !is_string($appender['format'])) {
                continue;
            }

            if ($this->hasTracePlaceholders($appender['format'])) {
                continue;
            }

            if (!$this->isSingleFileAppender($appender)) {
                continue;
            }

            $appenders[$index]['format'] = rtrim($appender['format']) . self::TRACE_FORMAT_SUFFIX;
        }

        return $appenders;
    }

    /**
     * @param list<array<string, mixed>> $appenders
     *
     * @return list<array<string, mixed>>
     */
    private function revertAppenders(array $appenders): array
    {
        foreach ($appenders as $index => $appender) {
            if (!is_array($appender) || !isset($appender['format']) || !is_string($appender['format'])) {
                continue;
            }

            if (!$this->isSingleFileAppender($appender)) {
                continue;
            }

            $format = $appender['format'];
            if (!str_ends_with($format, self::TRACE_FORMAT_SUFFIX)) {
                continue;
            }

            $appenders[$index]['format'] = substr($format, 0, -strlen(self::TRACE_FORMAT_SUFFIX));
        }

        return $appenders;
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

    private function hasTracePlaceholders(string $format): bool
    {
        return str_contains($format, '%traceId') || str_contains($format, '%spanId');
    }
}

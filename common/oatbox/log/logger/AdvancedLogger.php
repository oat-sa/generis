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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA
 */

declare(strict_types=1);

namespace oat\oatbox\log\logger;

use Psr\Log\LoggerInterface;
use Throwable;

class AdvancedLogger implements LoggerInterface
{
    public const CONTEXT_EXCEPTION = 'contextException';

    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function emergency($message, array $context = [])
    {
        $this->logData('emergency', $message, $context);
    }

    public function alert($message, array $context = [])
    {
        $this->logData('alert', $message, $context);
    }

    public function critical($message, array $context = [])
    {
        $this->logData('critical', $message, $context);
    }

    public function error($message, array $context = [])
    {
        $this->logData('error', $message, $context);
    }

    public function warning($message, array $context = [])
    {
        $this->logData('warning', $message, $context);
    }

    public function notice($message, array $context = [])
    {
        $this->logData('notice', $message, $context);
    }

    public function info($message, array $context = [])
    {
        $this->logData('info', $message, $context);
    }

    public function debug($message, array $context = [])
    {
        $this->logData('debug', $message, $context);
    }

    public function log($level, $message, array $context = [])
    {
        $this->logData('log', $message, $context, $level);
    }

    private function logData(string $methodName, $message, array $context = [], $level = null): void
    {
        //@TODO @FIXME Add user, URL and method

        if (isset($context[self::CONTEXT_EXCEPTION]) && $context[self::CONTEXT_EXCEPTION] instanceof Throwable) {
            $message = $message . '. Exception: ' . $this->buildLogMessage($context[self::CONTEXT_EXCEPTION]);

            unset($context[self::CONTEXT_EXCEPTION]);
        }

        $level === null
            ? $this->logger->{$methodName}($message, $context)
            : $this->logger->{$methodName}($level, $message, $context);
    }

    private function buildLogMessage(Throwable $exception): string
    {
        $message = $this->createMessage($exception);

        if ($exception->getPrevious()) {
            $message = sprintf(
                '%s, previous: %s',
                $message,
                $this->buildLogMessage($exception->getPrevious())
            );
        }

        return $message;
    }

    private function createMessage(Throwable $exception): string
    {
        return sprintf(
            '"%s", code: "%s", file: "%s", line: "%s"',
            $exception->getMessage(),
            $exception->getCode(),
            $exception->getFile(),
            $exception->getLine()
        );
    }
}

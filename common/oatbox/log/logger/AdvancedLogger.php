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

use oat\oatbox\log\logger\extender\ContextExtenderInterface;
use Psr\Log\LoggerInterface;

class AdvancedLogger implements LoggerInterface
{
    public const ACL_SERVICE_ID = self::class . '::ACL';

    /** @var LoggerInterface */
    private $logger;

    /** @var ContextExtenderInterface[] */
    private $contextExtenders = [];

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function addContextExtender(ContextExtenderInterface $contextExtender): self
    {
        $this->contextExtenders[get_class($contextExtender)] = $contextExtender;

        return $this;
    }

    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->logData('emergency', $message, $context);
    }

    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->logData('alert', $message, $context);
    }

    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->logData('critical', $message, $context);
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->logData('error', $message, $context);
    }

    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->logData('warning', $message, $context);
    }

    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->logData('notice', $message, $context);
    }

    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->logData('info', $message, $context);
    }

    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->logData('debug', $message, $context);
    }

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $this->logData('log', $message, $context, $level);
    }

    private function logData(string $methodName, string|\Stringable $message, array $context = [], $level = null): void
    {
        $context = $this->extendContext($context);

        $level === null
            ? $this->logger->{$methodName}($message, $context)
            : $this->logger->{$methodName}($level, $message, $context);
    }

    private function extendContext(array $context): array
    {
        foreach ($this->contextExtenders as $contextExtender) {
            $context = $contextExtender->extend($context);
        }

        return $context;
    }
}

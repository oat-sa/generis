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

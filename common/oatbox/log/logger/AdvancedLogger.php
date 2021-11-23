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

use oat\oatbox\session\SessionService;
use Psr\Log\LoggerInterface;
use Throwable;

class AdvancedLogger implements LoggerInterface
{
    public const CONTEXT_EXCEPTION = 'contextException';
    public const CONTEXT_USER_DATA = 'contextUserData';
    public const CONTEXT_REQUEST_DATA = 'contextRequestData';

    /** @var LoggerInterface */
    private $logger;

    /** @var SessionService */
    private $sessionService;

    /** @var array|null */
    private $userData;

    /** @var array */
    private $requestData = [];

    /** @var array */
    private $serverData;

    public function __construct(LoggerInterface $logger, SessionService $sessionService)
    {
        $this->logger = $logger;
        $this->sessionService = $sessionService;
    }

    public function withServerData(array $data): self
    {
        $this->serverData = $data;

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
        $context[self::CONTEXT_REQUEST_DATA] = isset($context[self::CONTEXT_REQUEST_DATA])
            && is_array($context[self::CONTEXT_REQUEST_DATA])
            ? array_merge($context[self::CONTEXT_REQUEST_DATA], $this->getContextRequestData())
            : $this->getContextRequestData();

        $context[self::CONTEXT_USER_DATA] = isset($context[self::CONTEXT_USER_DATA])
            && is_array($context[self::CONTEXT_USER_DATA])
            ? array_merge($context[self::CONTEXT_USER_DATA], $this->getContextUserData())
            : $this->getContextUserData();

        if (isset($context[self::CONTEXT_EXCEPTION]) && $context[self::CONTEXT_EXCEPTION] instanceof Throwable) {
            $context[self::CONTEXT_EXCEPTION] = $this->buildLogMessage($context[self::CONTEXT_EXCEPTION]);
        }

        return $context;
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
            '"%s", code: %s, file: "%s", line: %s',
            $exception->getMessage(),
            $exception->getCode(),
            $exception->getFile(),
            $exception->getLine()
        );
    }

    private function getContextUserData(): array
    {
        if ($this->userData === null) {
            $user = $this->sessionService->getCurrentUser();

            if ($user) {
                $this->userData = [
                    'id' => $user->getIdentifier(),
                ];

                return $this->userData;
            }

            $this->userData = [
                'id' => 'anonymous'
            ];
        }

        return $this->userData;
    }

    private function getContextRequestData(): array
    {
        if (!empty($this->requestData)) {
            return $this->requestData;
        }

        $serverData = $this->serverData ?? $_SERVER;

        $this->requestData = [];

        if (isset($serverData['SERVER_ADDR'])) {
            $this->requestData['serverIp'] = $serverData['SERVER_ADDR'];
        }

        if (isset($serverData['SERVER_NAME'])) {
            $this->requestData['serverName'] = $serverData['SERVER_NAME'];
        }

        if (isset($serverData['REQUEST_URI'])) {
            $this->requestData['requestUri'] = substr($serverData['REQUEST_URI'], 0, 500);
        }

        if (isset($serverData['REQUEST_METHOD'])) {
            $this->requestData['requestMethod'] = $serverData['REQUEST_METHOD'];
        }

        return $this->requestData;
    }
}

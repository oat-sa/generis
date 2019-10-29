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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA
 *
 */

namespace oat\oatbox\log;

use oat\oatbox\log\logger\TaoMonolog;
use oat\oatbox\service\ConfigurableService;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\NullLogger;

class LoggerService extends ConfigurableService implements LoggerInterface
{
    use LoggerTrait;

    public const SERVICE_ID = 'generis/log';
    public const DEFAULT_CHANNEL = 'tao';
    private const OPTION_LOGGER = 'logger';
    private const OPTION_LOGGERS = 'loggers';
    private const OPTION_CLASS = 'class';
    private const OPTION_OPTIONS = 'options';
    private const OPTION_NAME = 'name';

    /** @var LoggerInterface[] */
    private $loggers = [];

    /**
     * Register the given PSR-3 logger into the defined channel.
     * Previous and new logger are encapsulated into a LoggerAggregator.
     */
    public function addLogger(LoggerInterface $logger, string $channel = null)
    {
        $channel = $channel ?? self::DEFAULT_CHANNEL;

        if (!array_key_exists($channel, $this->loggers)) {
            $this->loggers[$channel] = new NullLogger();
        }

        $this->loggers[$channel] = new LoggerAggregator([$logger, $this->loggers[$channel]]);

        return $this->loggers[$channel];
    }

    public function getLogger(string $channel = null): LoggerInterface
    {
        $channel = $channel ?? self::DEFAULT_CHANNEL;

        if ($this->loggers === []) {
            // To keep backward compatibility, "logger" key must be supported also
            if ($this->hasOption(self::OPTION_LOGGER)) {
                $this->loadLogger($this->getOption(self::OPTION_LOGGER));
            }

            $this->loadLoggers();
        }

        if (array_key_exists($channel, $this->loggers) && $this->loggers[$channel] instanceof LoggerInterface) {
            return $this->loggers[$channel];
        }

        return new NullLogger();
    }

    /**
     * Logs to the default channel
     */
    public function log($level, $message, array $context = []): void
    {
        $this->getLogger()->log($level, $message, $context);
    }

    private function loadLoggers(): void
    {
        foreach ($this->getOption(self::OPTION_LOGGERS) ?? [] as $logger) {
            $this->loadLogger($logger);
        }
    }

    private function loadLogger($logger): void
    {
        if ($logger instanceof TaoMonolog) {
            $this->registerLogger($logger, $logger->getName());
            return;
        }

        if ($logger instanceof LoggerInterface) {
            $channel = method_exists($logger, 'getName') ? $logger->getName() : self::DEFAULT_CHANNEL;

            $this->registerLogger($logger, $channel);
            return;
        }

        if (!is_array($logger)) {
            throw new \LogicException('Logger options must be an array');
        }

        if (!array_key_exists(self::OPTION_CLASS, $logger)) {
            throw new \LogicException('No class defined for logger');
        }

        if (!is_a($logger[self::OPTION_CLASS], LoggerInterface::class, true)) {
            throw new \LogicException(sprintf('Logger class must implement %s', LoggerInterface::class));
        }

        $channel = $logger[self::OPTION_OPTIONS][self::OPTION_NAME] ?? self::DEFAULT_CHANNEL;

        $this->registerLogger(new $logger[self::OPTION_CLASS]($logger[self::OPTION_OPTIONS]), $channel);
    }

    private function registerLogger(LoggerInterface $logger, string $channel): void
    {
        $this->loggers[$channel] = array_key_exists($channel, $this->loggers)
            ? new LoggerAggregator([$logger, $this->loggers[$channel]])
            : $this->loggers[$channel] = $logger;
    }
}

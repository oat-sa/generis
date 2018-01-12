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

use oat\oatbox\service\ConfigurableService;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\NullLogger;

class LoggerService extends ConfigurableService implements LoggerInterface
{
    const SERVICE_ID = 'generis/logger';

    const LOGGER_OPTION = 'logger';

    /** @var LoggerInterface */
    protected $logger;

    /** @var bool Set the logger to active or not to avoid log during logging itself */
    protected $enabled = true;

    /**
     * Add a Psr3 logger to LoggerService instance
     * Previous and new logger are encapsulated into a LoggerAggregator
     *
     * @param LoggerInterface $logger
     * @return LoggerAggregator|LoggerInterface
     */
    public function addLogger(LoggerInterface $logger)
    {
        if ($this->isLoggerLoaded() && !($this->logger instanceof NullLogger)) {
            $logger = new LoggerAggregator([$logger, $this->logger]);
        }
        $this->logger = $logger;
        return $logger;
    }

    /**
     * Get the logger
     *
     * @return LoggerInterface
     */
    public function getLogger()
    {
        if (!$this->isLoggerLoaded()) {
            $this->loadLogger();
        }
        return $this->logger;
    }

    /**
     * Wrap a log to built logger
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        if ($this->enabled) {
            $this->enabled = false;
            $this->getLogger()->log($level, $message, $context);
            $this->enabled = true;
        }
    }


    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function emergency($message, array $context = array())
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function alert($message, array $context = array())
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function critical($message, array $context = array())
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function error($message, array $context = array())
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function warning($message, array $context = array())
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function notice($message, array $context = array())
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function info($message, array $context = array())
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function debug($message, array $context = array())
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Load the logger from configuration
     *
     * If options does not contain any Psr3 Logger, NullLogger is set by default
     *
     * @return LoggerInterface
     */
    protected function loadLogger()
    {
        $logger = null;
        if ($this->hasOption(self::LOGGER_OPTION)) {
            $loggerOptions = $this->getOption(self::LOGGER_OPTION);
            if (is_object($loggerOptions)) {
                if (is_a($loggerOptions, LoggerInterface::class)) {
                    $logger = $loggerOptions;
                }
            } elseif (is_array($loggerOptions) && isset($loggerOptions['class'])) {
                $classname = $loggerOptions['class'];
                if (is_a($classname, LoggerInterface::class, true)) {
                    if (isset($loggerOptions['options'])) {
                        $logger = new $classname($loggerOptions['options']);
                    } else {
                        $logger = new $classname();
                    }
                }
            }
        }

        if (!is_null($logger)) {
            $this->logger = $logger;
        } else {
            $this->logger = new NullLogger();
        }
        $this->propagate($this->logger);

        return $this->logger;
    }

    /**
     * Check if the logger is loaded from configuration
     *
     * @return bool
     */
    protected function isLoggerLoaded()
    {
        return $this->logger instanceof LoggerInterface;
    }
}
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
use Psr\Log\NullLogger;

class LoggerService extends ConfigurableService
{
    const SERVICE_ID = 'generis/logger';

    const LOGGER_OPTION = 'logger';

    /**
     * LoggerService constructor.
     *
     * Initialize the logger from config
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        parent::__construct($options);
        $this->loadLogger();
    }

    /**
     * Add a Psr3 logger to LoggerService instance
     * Previous and new logger are encapsulated into a LoggerAggregator
     * If $replace is set to true, only new logger is taken
     *
     * @param LoggerInterface $logger
     * @param bool $replace
     * @return LoggerInterface
     */
    public function addLogger(LoggerInterface $logger, $replace = false)
    {
        if (!$replace || $this->isLoggerLoaded()) {
            $logger = new LoggerAggregator([$logger, $this->getLogger()]);
        }
        $this->setLogger($logger);
        return $logger;
    }

    /**
     * Get the current logger.
     * If options does not contain any Psr3 Logger, NullLogger is set by default
     *
     * @return LoggerInterface
     */
    protected function loadLogger()
    {
        if (!$this->isLoggerLoaded()) {
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
                $this->setLogger($logger);
            }
        }
        return $this->getLogger();
    }

    /**
     * Check if the logger is set to default NullLogger
     *
     * @return bool
     */
    protected function isLoggerLoaded()
    {
        return !($this->getLogger() instanceof NullLogger);
    }
}
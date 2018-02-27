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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA
 * 
 */

use Psr\Log\LogLevel;
use Psr\Log\AbstractLogger;
/**
 * A wrapper for the old Logger
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package generis
 */
class common_log_Logger2Psr extends AbstractLogger
{
    /**
     * A map between the loggers
     * @var array
     */
    private static $map = array(
        LogLevel::EMERGENCY => common_Logger::FATAL_LEVEL,
        LogLevel::ALERT => common_Logger::FATAL_LEVEL,
        LogLevel::CRITICAL => common_Logger::ERROR_LEVEL,
        LogLevel::ERROR => common_Logger::ERROR_LEVEL,
        LogLevel::WARNING => common_Logger::WARNING_LEVEL,
        LogLevel::INFO => common_Logger::INFO_LEVEL,
        LogLevel::NOTICE => common_Logger::DEBUG_LEVEL,
        LogLevel::DEBUG => common_Logger::DEBUG_LEVEL,
    );

    /**
     * @var array   The common_logger level - PSR3 log level conversion.
     */
    private static $reverseMap = array(
        common_Logger::TRACE_LEVEL   => LogLevel::DEBUG,
        't'                          => LogLevel::DEBUG,

        common_Logger::DEBUG_LEVEL   => LogLevel::DEBUG,
        'd'                          => LogLevel::DEBUG,

        common_Logger::INFO_LEVEL    => LogLevel::INFO,
        'i'                          => LogLevel::INFO,

        common_Logger::WARNING_LEVEL => LogLevel::WARNING,
        'w'                          => LogLevel::WARNING,

        common_Logger::ERROR_LEVEL   => LogLevel::ERROR,
        'e'                          => LogLevel::ERROR,

        common_Logger::FATAL_LEVEL   => LogLevel::CRITICAL,
        'f'                          => LogLevel::CRITICAL,
    );

    /**
     * @var common_Logger
     */
    private $logger;
    
    public function __construct(common_Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * (non-PHPdoc)
     * @see \Psr\Log\LoggerInterface::log()
     */
    public function log($level, $message, array $context = array())
    {
        $errorLevel = isset(self::$map[$level]) ? self::$map[$level] : common_Logger::ERROR_LEVEL;
        $this->logger->log($errorLevel, $message, $context);
    }
    
    public static function getCommonFromPsrLevel($level)
    {
        if (empty(self::$map[$level])) {
            throw new Exception('Invalid error level in Common level to PSR conversion: ' . $level);
        }
    
        return self::$map[$level];
    }

    /**
     * Returns the PSR log level based on the common log level.
     *
     * @param string $level
     *
     * @return string   The PSR log level.
     *
     * @throws Exception   When invalid level was presented.
     */
    public static function getPsrLevelFromCommon($level)
    {
        if (empty(self::$reverseMap[$level])) {
            throw new Exception('Invalid error level in PSR to Common level conversion: ' . $level);
        }

        return self::$reverseMap[$level];
    }
}
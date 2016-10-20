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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 */

namespace oat\oatbox\log;

use common_exception_InconsistentData;
use Psr\Log\LoggerInterface;

/**
 * This logger stores messages in an internal registry that can be queried.
 * It can be used to, for example, assert that specific messages have been logged in unit tests
 */
class TestLogger implements LoggerInterface {

    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';

    private $registry = array();

    /**
     * TestLogger constructor.
     */
    public function __construct() {
        $this->registry[self::EMERGENCY] = array();
        $this->registry[self::ALERT]     = array();
        $this->registry[self::CRITICAL]  = array();
        $this->registry[self::ERROR]     = array();
        $this->registry[self::WARNING]   = array();
        $this->registry[self::NOTICE]    = array();
        $this->registry[self::INFO]      = array();
        $this->registry[self::DEBUG]     = array();
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function emergency($message, array $context = array())
    {
        $this->log(self::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function alert($message, array $context = array())
    {
        $this->log(self::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function critical($message, array $context = array())
    {
        $this->log(self::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function error($message, array $context = array())
    {
        $this->log(self::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function warning($message, array $context = array())
    {
        $this->log(self::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function notice($message, array $context = array())
    {
        $this->log(self::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function info($message, array $context = array())
    {
        $this->log(self::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function debug($message, array $context = array())
    {
        $this->log(self::DEBUG, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     * @throws common_exception_InconsistentData
     */
    public function log($level, $message, array $context = array())
    {
        if (! array_key_exists($level, $this->registry)) {
            $level = self::ERROR;
        }
        $this->registry[$level][] = [
            'message' => $message,
            'context' => $context
        ];
    }

    /**
     * The following methods are not part of the PSR-3 interface and are used to query the logger
     */

    /**
     * Return all Emergency log entries
     * @return array
     */
    public function getEmergency() {
        return $this->get(self::EMERGENCY);
    }

    /**
     * Return all Alert log entries
     * @return array
     */
    public function getAlert() {
        return $this->get(self::ALERT);
    }

    /**
     * Return all Critical log entries
     * @return array
     */
    public function getCritical() {
        return $this->get(self::CRITICAL);
    }

    /**
     * Return all Error log entries
     * @return array
     */
    public function getError() {
        return $this->get(self::ERROR);
    }

    /**
     * Return all Warning log entries
     * @return array
     */
    public function getWarning() {
        return $this->get(self::WARNING);
    }

    /**
     * Return all Notice log entries
     * @return array
     */
    public function getNotice() {
        return $this->get(self::NOTICE);
    }

    /**
     * Return all Info log entries
     * @return array
     */
    public function getInfo() {
        return $this->get(self::INFO);
    }

    /**
     * Return all Debug log entries
     * @return array
     */
    public function getDebug() {
        return $this->get(self::DEBUG);
    }

    /**
     * Return all log entries for an arbitrary level
     *
     * @param string $level
     * @return array
     * @throws common_exception_InconsistentData
     */
    public function get($level) {
        if (isset($this->registry[$level])) {
            return $this->registry[$level];
        } else {
            throw new common_exception_InconsistentData('Unknown level ' . $level);
        }
    }

    /**
     * Check that a specific Emergency message has been logged
     *
     * @param $message
     * @return bool
     */
    public function hasEmergency($message) {
        return $this->has(self::EMERGENCY, $message);
    }

    /**
     * Check that a specific Alert message has been logged
     *
     * @param $message
     * @return bool
     */
    public function hasAlert($message) {
        return $this->has(self::ALERT, $message);
    }

    /**
     * Check that a specific Critical message has been logged
     *
     * @param $message
     * @return bool
     */
    public function hasCritical($message) {
        return $this->has(self::CRITICAL, $message);
    }

    /**
     * Check that a specific Error message has been logged
     *
     * @param $message
     * @return bool
     */
    public function hasError($message) {
        return $this->has(self::ERROR, $message);
    }

    /**
     * Check that a specific Warning message has been logged
     *
     * @param $message
     * @return bool
     */
    public function hasWarning($message) {
        return $this->has(self::WARNING, $message);
    }

    /**
     * Check that a specific Notice message has been logged
     *
     * @param $message
     * @return bool
     */
    public function hasNotice($message) {
        return $this->has(self::NOTICE, $message);
    }

    /**
     * Check that a specific Info message has been logged
     *
     * @param $message
     * @return bool
     */
    public function hasInfo($message) {
        return $this->has(self::INFO, $message);
    }

    /**
     * Check that a specific Debug message has been logged
     *
     * @param $message
     * @return bool
     */
    public function hasDebug($message) {
        return $this->has(self::DEBUG, $message);
    }

    /**
     * Check that a specific message has been logged at an arbitrary level
     *
     * @param $level
     * @param $message
     * @return bool
     * @throws common_exception_InconsistentData
     */
    public function has($level, $message) {
        if (isset($this->registry[$level]) && count($this->registry[$level]) > 0) {
            foreach ($this->registry[$level] as $logEntry) {
                if ($logEntry['message'] == $message) {
                    return true;
                }
            }
            return false;
        } else {
            throw new common_exception_InconsistentData('Unknown level ' . $level);
        }
    }

}
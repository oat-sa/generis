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

namespace oat\generis\test\unit\oatbox\log;

use common_exception_InconsistentData;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;

/**
 * This logger stores messages in an internal registry that can be queried.
 * It can be used to, for example, assert that specific messages have been logged in unit tests
 */
class TestLogger implements LoggerInterface {

    use LoggerTrait;

    private $registry = array();

    /**
     * TestLogger constructor.
     */
    public function __construct() {
        $this->registry[LogLevel::EMERGENCY] = array();
        $this->registry[LogLevel::ALERT]     = array();
        $this->registry[LogLevel::CRITICAL]  = array();
        $this->registry[LogLevel::ERROR]     = array();
        $this->registry[LogLevel::WARNING]   = array();
        $this->registry[LogLevel::NOTICE]    = array();
        $this->registry[LogLevel::INFO]      = array();
        $this->registry[LogLevel::DEBUG]     = array();
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
            $level = LogLevel::ERROR;
        }
        $this->registry[$level][] = [
            'message' => $message,
            'context' => $context
        ];
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
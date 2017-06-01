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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\oatbox\log;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class VerboseLogger extends AbstractLogger
{
    /**
     * @var int The position of logger verbosity.
     */
    protected $levelPosition;

    /**
     * @var array Level priority list.
     */
    protected $levels = array(
        LogLevel::EMERGENCY,
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::ERROR,
        LogLevel::WARNING,
        LogLevel::NOTICE,
        LogLevel::INFO,
        LogLevel::DEBUG,
    );

    /**
     * VerboseLogger constructor.
     *
     * @param $minimumLevel
     * @throws \common_Exception
     */
    public function __construct($minimumLevel)
    {
        if (! in_array($minimumLevel, array_keys($this->levels))) {
            throw new \common_Exception('Level "' . $minimumLevel . '" is not managed by verbose logger');
        }
        $this->levelPosition = array_search($minimumLevel, $this->levels);
    }

    /**
     * Log message following minimum level of verbosity
     * If $level is bigger than minimum verbosity required
     * Set color associated to $level
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        $this->logMessage(
            $level,
            $this->getFormattedMessage($level, $message)
        );
    }

    /**
     * Log message following minimum level of verbosity
     * If $level is bigger than minimum verbosity required
     *
     * @param mixed $level
     * @param string $message
     */
    protected function logMessage($level, $message)
    {
        if (array_search($level, $this->levels) > $this->levelPosition) {
            return;
        }

        echo $message;
    }

    /**
     * Returns the formatted message.
     *
     * @param $level
     * @param $message
     *
     * @return string
     */
    public function getFormattedMessage($level, $message)
    {
        return '[' . (new \DateTime())->format('Y-m-d H:i') . ']'
            . str_pad('[' . $level . ']', 12)
            . $message
            . PHP_EOL;
    }
}

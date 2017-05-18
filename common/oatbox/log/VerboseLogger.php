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
     * @var array List of colors associated to a level
     */
    private $colors = array(
        LogLevel::EMERGENCY => '1;31', // red
        LogLevel::ALERT     => '1;31', // red
        LogLevel::CRITICAL  => '1;31', // red
        LogLevel::ERROR     => '1;31', // red
        LogLevel::WARNING   => '1;33', // yellow
        LogLevel::NOTICE    => '1;33', // yellow
        LogLevel::INFO      => '0;32', // green
        LogLevel::DEBUG     => '0;37', // light grey
    );

    /**
     * @var array List of ordered LogLevel
     */
    private $levels = array(
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
     * @var int The position of logger verbosity
     */
    protected $levelPosition;

    /**
     * VerboseLogger constructor.
     *
     * @param $minimumLevel
     * @throws \common_Exception
     */
    public function __construct($minimumLevel)
    {
        if (! in_array($minimumLevel, $this->levels)) {
            throw new \common_Exception('Level has to be manage');
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
        if (array_search($level, $this->levels) > $this->levelPosition) {
            return;
        }

        $this->setLevelColor($level);
        echo '[' . (new \DateTime())->format('Y-m-d H:i') . ']' .
            str_pad('[' . $level . ']', 12) .
            $message . PHP_EOL;
        $this->setDefaultColor();
    }

    /**
     * Set the CLI color associated to given $level
     *
     * @param $level
     */
    protected function setLevelColor($level)
    {
        if (array_key_exists($level, $this->colors)) {
            echo "\033[" . $this->colors[$level] . 'm';
        } else {
            $this->setDefaultColor();
        }
    }

    /**
     * Set the default CLI color e.q. dark grey
     */
    protected function setDefaultColor()
    {
        echo "\033[1;30m"; // Dark grey
    }

}
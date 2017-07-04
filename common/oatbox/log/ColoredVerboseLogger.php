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

class ColoredVerboseLogger extends VerboseLogger
{
    /**
     * @var array List of colors associated to a level
     */
    protected $colors = array(
        LogLevel::EMERGENCY => '1;31', // red
        LogLevel::ALERT     => '1;31', // red
        LogLevel::CRITICAL  => '1;31', // red
        LogLevel::ERROR     => '1;31', // red
        LogLevel::WARNING   => '1;33', // yellow
        LogLevel::NOTICE    => '1;34', // light blue
        LogLevel::INFO      => '0;32', // green
        LogLevel::DEBUG     => '0;37', // light grey
    );

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
            (
                $this->getLevelColor($level)
                . $this->getFormattedMessage($level, $message)
                . $this->getDefaultColor()
            )
        );
    }

    /**
     * Get the CLI color associated to given $level
     *
     * @param $level
     *
     * @return string
     */
    protected function getLevelColor($level)
    {
        if (array_key_exists($level, $this->colors)) {
            return "\033[" . $this->colors[$level] . 'm';
        } else {
            return $this->getDefaultColor();
        }
    }

    /**
     * Set the default CLI color e.q. dark grey
     *
     * @return string
     */
    protected function getDefaultColor()
    {
        return "\033[0m"; // Dark grey
    }

}
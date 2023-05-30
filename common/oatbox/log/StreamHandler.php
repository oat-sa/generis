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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

declare(strict_types=1);

namespace oat\oatbox\log;

use Monolog\Handler\StreamHandler as MonologStreamHandler;
use Monolog\Logger;

/**
 * Stores to any stream resource
 *
 * Provides the ability to use command line parameters --log-file and --log-level:
 *
 * if the --log-file parameter is specified, the path to the file for logging is taken from this parameter,
 * if this parameter is not specified, the path to the file is taken from the system configuration
 * for example:
 * php index.php 'oat\taoTaskQueue\scripts\tools\RunWorker' --log-file /var/www/html/data/tao/log/tao-nccer.log
 *
 * if the --log-level parameter is specified, the minimum logging level is taken from this parameter,
 * if this parameter is not specified, the minimum logging level is taken from the system configuration
 * can take values DEBUG, INFO, NOTICE, WARNING, ERROR, CRITICAL, ALERT or EMERGENCY
 * for example:
 * php index.php 'oat\taoTaskQueue\scripts\tools\RunWorker' --log-level DEBUG
 *
 * @author Andrey Niahrou, <andrei.niahrou@taotesting.com>
 */
class StreamHandler extends MonologStreamHandler
{
    public const PARAM_LOG_FILE = '--log-file';
    public const PARAM_LOG_LEVEL = '--log-level';

    /**
     * @param resource|string $defaultStream (Unless this parameter is specified on the command line as --log-file,
     *                                       otherwise it is ignored) resource where data will be output
     * @param int $defaultLevel (Unless this parameter is specified on the command line as --log-level, otherwise it is
     *                          ignored) The minimum logging level at which this handler will be triggered
     * @param bool $bubble Whether the messages that are handled can bubble up the stack or not
     * @param int|null $filePermission Optional file permissions (default (0644) are only for owner read/write)
     * @param bool $useLocking Try to lock log file before doing any writes
     *
     * @throws \Exception                If a missing directory is not buildable
     * @throws \InvalidArgumentException If stream is not a resource or string
     */
    public function __construct(
        $defaultStream,
        int $defaultLevel = Logger::DEBUG,
        bool $bubble = true,
        $filePermission = null,
        bool $useLocking = false
    ) {
        $stream = $this->getScriptParameter(self::PARAM_LOG_FILE) ?: $defaultStream;
        $logLevel = $this->getLogLevelParameter() ?: $defaultLevel;
        parent::__construct($stream, $logLevel, $bubble, $filePermission, $useLocking);
    }

    /**
     * @param string $parameter
     * @return string|null
     */
    private function getScriptParameter(string $parameter): ?string
    {
        if (!in_array($parameter, $_SERVER['argv'])) {
            return null;
        }

        return $_SERVER['argv'][array_search($parameter, $_SERVER['argv']) + 1];
    }

    /**
     * @return int|null
     * @throws \Exception
     */
    private function getLogLevelParameter(): ?int
    {
        $logLevelParameter = $this->getScriptParameter(self::PARAM_LOG_LEVEL);
        if (!$logLevelParameter) {
            return null;
        }

        $errorLevels = Logger::getLevels();
        if (!isset($errorLevels[$logLevelParameter])) {
            throw new \Exception(
                sprintf(
                    'Such log level doesn`t exist. Please, use one of: %s',
                    implode(', ', array_flip($errorLevels))
                )
            );
        }

        return $errorLevels[$logLevelParameter];
    }
}

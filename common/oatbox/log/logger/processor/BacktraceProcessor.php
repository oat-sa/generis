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

namespace oat\oatbox\log\logger\processor;

use Psr\Log\LogLevel;

/**
 * Class BacktraceProcessor
 *
 * A processor to add the trace to the log, only with a level superior to given level
 *
 * Inspired from \Monolog\Processor\IntrospectionProcessor
 *
 * @package oat\oatbox\log\logger\processor
 */
class BacktraceProcessor
{
    /**
     * Trace offset name under the log extra offset.
     */
    const TRACE_OFFSET = 'trace';

    /**
     * @var array
     */
    private $classKeywordsToSkip = [
        'Monolog\\',
        '\\TaoMonolog',
        '\\LoggerService',
        'common_Logger',
    ];

    /**
     * @var bool
     */
    private $skipLoggerClasses;

    /**
     * @var string
     */
    protected $level;

    /**
     * BacktraceProcessor constructor.
     *
     * @param string $level
     * @param bool   $skipLoggerClasses
     * @param array  $classKeywordsToSkip
     */
    public function __construct($level = LogLevel::DEBUG, $skipLoggerClasses = false, $classKeywordsToSkip = [])
    {
        $this->level               = $level;
        $this->skipLoggerClasses   = $skipLoggerClasses;
        $this->classKeywordsToSkip = array_merge(
            $this->classKeywordsToSkip,
            $classKeywordsToSkip
        );
    }

    /**
     * Returns the record decorated with the backtrace.
     *
     * @param array $record
     *
     * @return array
     */
    public function __invoke(array $record)
    {
        // return if the level is not high enough
        if ($record['level'] < $this->level) {
            return $record;
        }

        /*
        * http://php.net/manual/en/function.debug-backtrace.php
        * As of 5.3.6, DEBUG_BACKTRACE_IGNORE_ARGS option was added.
        * Any version less than 5.3.6 must use the DEBUG_BACKTRACE_IGNORE_ARGS constant value '2'.
        */
        $trace = debug_backtrace((PHP_VERSION_ID < 50306) ? 2 : DEBUG_BACKTRACE_IGNORE_ARGS);

        // skip first since it's always the current method
        array_shift($trace);
        // the call_user_func call is also skipped
        array_shift($trace);

        foreach ($trace as $key => $row) {
            // If we need to skip the trace row.
            if ($this->isTheClassSkippable($row)) {
                unset($trace[$key]);

                continue;
            }

            if (isset($trace[$key]['object'])) {
                unset($trace[$key]['object']);
            }

            if (isset($trace[$key]['args'])) {
                $vars = array();
                foreach ($trace[$key]['args'] as $k => $v) {
                    switch (gettype($v)) {
                        case 'boolean' :
                        case 'integer' :
                        case 'double' :
                            $vars[$k] = (string)$v;
                            break;
                        case 'string' :
                            $vars[$k] = strlen($v) > 128 ? 'string('.strlen($v).')' : $v;
                            break;
                        case 'class' :
                            $vars[$k] = get_class($v);
                            break;
                        default:
                            $vars[$k] = gettype($v);
                    }
                }
                $trace[$key]['args'] = $vars;
            }
        }

        // we should have the call source now
        $record['extra'] = array_merge(
            $record['extra'],
            array(
                static::TRACE_OFFSET => array_values($trace)
            )
        );

        return $record;
    }

    /**
     * Returns TRUE if the given trace is skippable.
     *
     * @param array $trace
     *
     * @return bool
     */
    private function isTheClassSkippable(array $trace)
    {
        if ($this->skipLoggerClasses === false) {
            return false;
        }

        if (empty($trace['class'])) {
            return false;
        }

        foreach ($this->classKeywordsToSkip as $current) {
            if (strpos($trace['class'], $current) !== false) {
                return true;
            }
        }

        return false;
    }
}
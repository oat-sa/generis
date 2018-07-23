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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\oatbox\log\logger\formatter;


use Monolog\Formatter\FormatterInterface;
use oat\oatbox\log\logger\processor\BacktraceProcessor;
use oat\oatbox\log\logger\processor\EnvironmentProcessorAbstract;

/**
 * TAO Json style log formatter.
 *
 * @package oat\oatbox\log\logger\formatter
 */
class TaoJsonLogFormatter implements FormatterInterface
{
    /**
     * Used datetime format.
     */
    const DATETIME_FORMAT = 'd/m/Y:H:i:s O';

    /**
     * Datetime offset in the generated output.
     */
    const DATETIME_OFFSET = 'datetime';

    /**
     * Stack offset in the generated output.
     */
    const STACK_OFFSET    = 'stack';

    /**
     * Severity offset in the generated output.
     */
    const SEVERITY_OFFSET = 'severity';

    /**
     * Line offset in the generated output.
     */
    const LINE_OFFSET     = 'line';

    /**
     * File offset in the generated output.
     */
    const FILE_OFFSET     = 'file';

    /**
     * Content/message offset in the generated output.
     */
    const CONTENT_OFFSET  = 'content';

    /**
     * Backtrace offset in the generated output.
     */
    const TRACE_OFFSET    = 'trace';

    /**
     * @inheritdoc
     *
     * @throws \RuntimeException
     */
    public function format(array $record)
    {
        $jsonString = json_encode($this->getOutputRecord($record));

        if ($jsonString === false) {
            throw new \RuntimeException('Error happened during the log format process! (json encode error)');
        }

        return $jsonString . PHP_EOL;
    }

    /**
     * @inheritdoc
     *
     * @throws \ErrorException
     */
    public function formatBatch(array $records)
    {
        foreach ($records as $key => $record) {
            $records[$key] = $this->format($record);
        }

        return $records;
    }

    /**
     * Returns the customized record.
     *
     * @param array $record
     *
     * @return array
     */
    protected function getOutputRecord(array $record)
    {
        // Adds the basic log details.
        $output = [
            static::DATETIME_OFFSET => $record['datetime']->format(static::DATETIME_FORMAT),
            static::SEVERITY_OFFSET => $record['level_name'],
            static::CONTENT_OFFSET  => $record['message'],
        ];

        // Adds the context file.
        if (isset($record['context']['file'])) {
            $output[static::FILE_OFFSET] = $record['context']['file'];
        }

        // Adds the context line.
        if (isset($record['context']['line'])) {
            $output[static::LINE_OFFSET] = $record['context']['line'];
        }

        // Adds the stack information.
        if (isset($record['extra'][EnvironmentProcessorAbstract::LOG_STACK])) {
            $output[static::STACK_OFFSET] = $record['extra'][EnvironmentProcessorAbstract::LOG_STACK];
        }

        // Adds the trace information.
        if (isset($record['extra'][BacktraceProcessor::TRACE_OFFSET])) {
            $output[static::TRACE_OFFSET] = $record['extra'][BacktraceProcessor::TRACE_OFFSET];
        }

        return $output;
    }
}

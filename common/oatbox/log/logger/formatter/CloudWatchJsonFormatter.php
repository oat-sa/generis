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

/**
 * Json log formatter.
 *
 * @package oat\oatbox\log\logger\formatter
 */
class CloudWatchJsonFormatter implements FormatterInterface
{

    /**
     * Used datetime format.
     */
    const DATETIME_FORMAT = 'd/m/Y:H:i:s O';

    /**
     * @inheritdoc
     *
     * @throws \RuntimeException
     */
    public function format(array $record)
    {
        $jsonString = json_encode($this->getOutputRecord($record));

        if ($jsonString === false) {
            throw new \RuntimeException('Error happened during the log format process! ' . json_last_error_msg());
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
            'datetime' => $record['datetime']->format(static::DATETIME_FORMAT),
            'severity' => $record['level_name'],
            'message' => $record['message'],
            'tag' => $record['context'],
        ];

        if (isset($record['extra']['trace'])) {
            $output['trace'] = $record['extra']['trace'];
        }
        if (isset($record['user_id'])) {
            $output['user_id'] = $record['user_id'];
        }

        return $output;
    }
}

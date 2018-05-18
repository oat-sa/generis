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

namespace oat\oatbox\log\logger\handler;

use Fluent\Logger\FluentLogger;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

/**
 * Based on https://github.com/yegortokmakov/monolog-fluentd
 */
class FluentdHandler extends AbstractProcessingHandler
{
    /**
     * @var FluentLogger
     */
    private $logger;

    /**
     * Initialize Handler
     *
     * @param FluentLogger $logger
     * @param int          $level
     * @param bool         $bubble
     */
    public function __construct(FluentLogger $logger = null, $level  = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record)
    {
        if (isset($record['formatted'])) {
            $logRecord = json_decode($record['formatted'], true);
        }

        if (empty($logRecord)) {
            $logRecord = $record['context'];
            $logRecord['level'] = Logger::getLevelName($record['level']);
            $logRecord['message'] = $record['message'];
        }

        $this->logger->post($record['channel'], $logRecord);
    }
}

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

use Monolog\Logger;

/**
 * Class UserIdProcessor
 *
 * @package oat\oatbox\log\logger\processor
 */
class UserIdProcessor
{
    /**
     * @var string
     */
    protected $level;

    /**
     * UserIdProcessor constructor.
     * @param int $level
     */
    public function __construct($level = Logger::DEBUG)
    {
        $this->level = $level;
    }

    /**
     * @param array $record
     * @return array
     * @throws \common_exception_Error
     */
    public function __invoke(array $record)
    {
        // No action required when the log level is too low.
        if ($record['level'] < $this->level) {
            return $record;
        }

        $record['user_id'] = $this->getUserId();

        return $record;
    }

    /**
     * @return string
     * @throws \common_exception_Error
     */
    private function getUserId()
    {
        return \common_session_SessionManager::getSession()->getUser()->getIdentifier();
    }
}

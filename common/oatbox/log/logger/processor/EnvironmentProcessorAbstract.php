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

namespace oat\oatbox\log\logger\processor;


use Psr\Log\LogLevel;

/**
 * Processor to add the environment details to the log.
 *
 * @package oat\oatbox\log\logger\processor
 */
abstract class EnvironmentProcessorAbstract
{
    /**
     * Stack offset name under the log extra offset.
     */
    const LOG_STACK           = 'stack';

    /**
     * Stack identifier offset name under the stack offset.
     */
    const LOG_STACK_ID        = 'id';

    /**
     * Stack type offset name under the stack offset.
     */
    const LOG_STACK_TYPE      = 'type';

    /**
     * Client name offset name under the stack offset.
     */
    const LOG_STACK_NAME      = 'name';

    /**
     * Host type offset name under the stack offset.
     */
    const LOG_STACK_HOST_TYPE = 'host_type';

    /**
     * @var string
     */
    protected $level;

    /**
     * EnvironmentProcessor constructor.
     *
     * @param string $level
     */
    public function __construct($level = LogLevel::DEBUG)
    {
        $this->level = $level;
    }

    /**
     * Adds the environment specific information to the log.
     *
     * @param array $record
     *
     * @return array
     */
    public function __invoke(array $record)
    {
        // No action required when the log level is too low.
        if ($record['level'] < $this->level) {
            return $record;
        }

        // Adds the environment details.
        $record['extra'][static::LOG_STACK] = isset($record['extra'][static::LOG_STACK])
            ? $record['extra'][static::LOG_STACK]
            : []
        ;
        $record['extra'][static::LOG_STACK] = array_merge(
            $record['extra'][static::LOG_STACK],
            [
                static::LOG_STACK_ID        => $this->getStackId(),
                static::LOG_STACK_TYPE      => $this->getStackType(),
                static::LOG_STACK_NAME      => $this->getStackName(),
                static::LOG_STACK_HOST_TYPE => $this->getStackHostType(),
            ]
        );

        return $record;
    }

    /**
     * Returns the current stack id.
     *
     * @return string
     */
    abstract protected function getStackId();

    /**
     * Returns the current stack type.
     *
     * @return string
     */
    abstract protected function getStackType();

    /**
     * Returns the current stack name.
     *
     * @return string
     */
    abstract protected function getStackName();

    /**
     * Returns the current stack host type.
     *
     * @return string
     */
    abstract protected function getStackHostType();
}

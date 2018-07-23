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


/**
 * Processor to add the environment details to the log.
 *
 * @package oat\oatbox\log\logger\processor
 */
class EnvironmentProcessor extends EnvironmentProcessorAbstract
{
    /**
     * Environment variable name for stack identifier.
     */
    const ENV_STACK_ID        = 'STACK_ID';

    /**
     * Environment variable name for stack name.
     */
    const ENV_STACK_NAME      = 'STACK_NAME';

    /**
     * Environment variable name for stack host type.
     */
    const ENV_STACK_HOST_TYPE = 'HOST_TYPE';

    /**
     * Default stack type value.
     */
    const DEFAULT_STACK_TYPE = 'tao';

    /**
     * @inheritdoc
     */
    protected function getStackId()
    {
        return (string)getenv(static::ENV_STACK_ID);
    }

    /**
     * @inheritdoc
     */
    protected function getStackType()
    {
        return static::DEFAULT_STACK_TYPE;
    }

    /**
     * @inheritdoc
     */
    protected function getStackName()
    {
        return (string)getenv(static::ENV_STACK_NAME);
    }

    /**
     * @inheritdoc
     */
    protected function getStackHostType()
    {
        return (string)getenv(static::ENV_STACK_HOST_TYPE);
    }
}
